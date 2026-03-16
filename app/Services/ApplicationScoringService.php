<?php

namespace App\Services;

use App\Models\CompanyJob;
use App\Models\JobApplication;
use App\Mail\ApplicationStatusChangedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * ApplicationScoringService
 *
 * Called immediately after every new application is saved.
 * If the job has criteria set, it:
 *
 *   1. Runs HARD FILTERS — any failure auto-rejects the application and sends
 *      a rejection email to the candidate.
 *
 *   2. For passing applications, calculates a WEIGHTED SCORE (0-100) across
 *      four factors and saves it. Applications are then sortable by score
 *      on the HR index page.
 */
class ApplicationScoringService
{
    // Qualification levels for comparison
    private const LEVELS = [
        'certificate' => 1,
        'diploma'     => 2,
        'degree'      => 3,
        'masters'     => 4,
        'phd'         => 5,
    ];

    // Synonyms recognised when scanning free-text education records
    private const SYNONYMS = [
        'certificate' => ['certificate', 'cert'],
        'diploma'     => ['diploma', 'hnd', 'pgd'],
        'degree'      => ['degree', 'bachelor', 'bsc', 'b.sc', 'ba', 'b.a', 'beng'],
        'masters'     => ['masters', 'master', 'msc', 'm.sc', 'mba', 'ma', 'm.a', 'postgraduate', 'pg'],
        'phd'         => ['phd', 'ph.d', 'doctorate', 'doctor of'],
    ];

    public function score(JobApplication $application): void
    {
        $job = $application->companyJob;

        // Nothing to do if the job has no criteria set
        if (!$job || !$job->hasCriteria()) {
            return;
        }

        $failures  = [];
        $breakdown = [];

        // ── Phase 1: Hard Filters ─────────────────────────────────────────────

        // Age
        $age = $application->date_of_birth?->age;
        if (!is_null($age)) {
            if (!is_null($job->criteria_min_age) && $age < $job->criteria_min_age) {
                $failures[] = "Age {$age} is below the minimum required age of {$job->criteria_min_age}.";
            }
            if (!is_null($job->criteria_max_age) && $age > $job->criteria_max_age) {
                $failures[] = "Age {$age} exceeds the maximum allowed age of {$job->criteria_max_age}.";
            }
        }

        // Minimum qualification
        if ($job->criteria_min_qualification) {
            $required = strtolower($job->criteria_min_qualification);
            $achieved = $this->detectHighestLevel($application->education_training ?? []);
            $requiredLevel = self::LEVELS[$required] ?? 0;
            if ($achieved < $requiredLevel) {
                $achieved_label = array_search($achieved, self::LEVELS) ?: 'none detected';
                $failures[] = "Minimum qualification required: {$job->criteria_min_qualification}. Detected: {$achieved_label}.";
            }
        }

        // Minimum years of experience
        if (!is_null($job->criteria_min_experience_years)) {
            $years = $this->totalYearsExperience($application->employment_record ?? []);
            if ($years < $job->criteria_min_experience_years) {
                $failures[] = "Minimum {$job->criteria_min_experience_years} years of experience required. Detected: {$years} year(s).";
            }
        }

        // Required keywords
        if (!empty($job->criteria_required_keywords)) {
            $searchText = $this->buildSearchText($application);
            foreach ($job->criteria_required_keywords as $keyword) {
                if (!str_contains(strtolower($searchText), strtolower(trim($keyword)))) {
                    $failures[] = "Required keyword not found in application: \"{$keyword}\".";
                }
            }
        }

        $passesCriteria = empty($failures);

        // ── Phase 2: Weighted Score (only for passing applications) ───────────

        $score = 0;

        if ($passesCriteria) {
            $qualScore     = $this->scoreQualification($job, $application);
            $expScore      = $this->scoreExperience($job, $application);
            $keywordScore  = $this->scoreKeywords($job, $application);
            $ageScore      = $this->scoreAge($job, $application);

            $totalWeight = $job->weight_qualification
                         + $job->weight_experience
                         + $job->weight_keyword_match
                         + $job->weight_age_fit;

            $score = $totalWeight > 0
                ? (int) round(
                    ($qualScore    * $job->weight_qualification
                   + $expScore     * $job->weight_experience
                   + $keywordScore * $job->weight_keyword_match
                   + $ageScore     * $job->weight_age_fit)
                    / $totalWeight
                )
                : 0;

            $breakdown = [
                'qualification'  => ['score' => $qualScore,    'weight' => $job->weight_qualification],
                'experience'     => ['score' => $expScore,     'weight' => $job->weight_experience],
                'keyword_match'  => ['score' => $keywordScore, 'weight' => $job->weight_keyword_match],
                'age_fit'        => ['score' => $ageScore,     'weight' => $job->weight_age_fit],
            ];
        }

        // ── Persist scoring results ───────────────────────────────────────────

        $application->score           = $passesCriteria ? $score : 0;
        $application->score_breakdown = $breakdown;
        $application->meets_criteria  = $passesCriteria;
        $application->criteria_failures = $failures;
        $application->scored_at       = now();

        if (!$passesCriteria) {
            $application->status           = JobApplication::STATUS_REJECTED;
            $application->rejection_reason = implode(' ', $failures);
        }

        $application->saveQuietly(); // avoid triggering model events again

        // ── Send rejection email if auto-rejected ─────────────────────────────

        if (!$passesCriteria) {
            try {
                Mail::to($application->email)
                    ->send(new ApplicationStatusChangedMail($application, JobApplication::STATUS_PENDING));
            } catch (\Throwable $e) {
                Log::warning("Auto-rejection email failed for application #{$application->id}: {$e->getMessage()}");
            }
        }
    }

    // ── Scoring factor methods ────────────────────────────────────────────────

    /** Score 0-100 based on how far above the minimum qualification the candidate is. */
    private function scoreQualification(CompanyJob $job, JobApplication $application): int
    {
        $achieved = $this->detectHighestLevel($application->education_training ?? []);
        $required = self::LEVELS[strtolower($job->criteria_min_qualification ?? '')] ?? 1;
        $max      = max(self::LEVELS);

        // Full marks if at or above required, scaled down if below (shouldn't happen post-filter)
        if ($achieved >= $required) {
            return (int) min(100, round(($achieved / $max) * 100));
        }
        return 0;
    }

    /** Score 0-100 based on years of experience, capped at 25 years = 100. */
    private function scoreExperience(CompanyJob $job, JobApplication $application): int
    {
        $years   = $this->totalYearsExperience($application->employment_record ?? []);
        $minimum = $job->criteria_min_experience_years ?? 0;
        $cap     = max(25, $minimum * 3); // dynamic cap — generous ceiling

        return (int) min(100, round(($years / $cap) * 100));
    }

    /** Score 0-100 based on how many required keywords were found. */
    private function scoreKeywords(CompanyJob $job, JobApplication $application): int
    {
        $keywords = $job->criteria_required_keywords ?? [];
        if (empty($keywords)) return 100;

        $text    = strtolower($this->buildSearchText($application));
        $matched = 0;
        foreach ($keywords as $kw) {
            if (str_contains($text, strtolower(trim($kw)))) {
                $matched++;
            }
        }
        return (int) round(($matched / count($keywords)) * 100);
    }

    /**
     * Score 0-100 based on age fit within the preferred range.
     * Full marks if within range, declining score outside.
     */
    private function scoreAge(CompanyJob $job, JobApplication $application): int
    {
        $age = $application->date_of_birth?->age;
        if (is_null($age)) return 50; // unknown age → neutral score

        $min = $job->criteria_min_age;
        $max = $job->criteria_max_age;

        if (is_null($min) && is_null($max)) return 100;

        if (!is_null($min) && !is_null($max)) {
            if ($age >= $min && $age <= $max) return 100;
            // Penalty for being outside the range
            $midpoint = ($min + $max) / 2;
            $range    = ($max - $min) / 2;
            $distance = abs($age - $midpoint) - $range;
            return (int) max(0, 100 - ($distance * 8));
        }

        if (!is_null($min)) return $age >= $min ? 100 : max(0, 100 - (($min - $age) * 10));
        if (!is_null($max)) return $age <= $max ? 100 : max(0, 100 - (($age - $max) * 10));

        return 100;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Detect the highest qualification level found in education_training records.
     * Returns an integer level (0 = nothing detected, 5 = PhD).
     */
    private function detectHighestLevel(array $records): int
    {
        $highest = 0;
        foreach ($records as $row) {
            $text = strtolower(implode(' ', array_filter([
                $row['qualification'] ?? '',
                $row['institution']   ?? '',
            ])));
            foreach (self::SYNONYMS as $level => $words) {
                foreach ($words as $word) {
                    if (str_contains($text, $word)) {
                        $highest = max($highest, self::LEVELS[$level]);
                        break;
                    }
                }
            }
        }
        return $highest;
    }

    /**
     * Estimate total years of experience from employment_record periods.
     * Handles formats: "2018-2022", "2018–2022", "Jan 2018 - Dec 2022", "2019-present"
     */
    private function totalYearsExperience(array $records): int
    {
        $total = 0;
        foreach ($records as $row) {
            $period = trim($row['period'] ?? '');
            if (!$period) continue;

            preg_match_all('/\b(19|20)\d{2}\b/', $period, $matches);
            $years = array_map('intval', $matches[0]);

            if (count($years) >= 2) {
                sort($years);
                $total += end($years) - $years[0];
            } elseif (count($years) === 1) {
                if (preg_match('/present|current|now|date/i', $period)) {
                    $total += (int) date('Y') - $years[0];
                } else {
                    $total += 1;
                }
            }
        }
        return $total;
    }

    /**
     * Flatten all free-text fields into a single searchable string.
     */
    private function buildSearchText(JobApplication $application): string
    {
        $parts = [];

        foreach ($application->education_training ?? [] as $row) {
            $parts[] = implode(' ', array_filter($row));
        }
        foreach ($application->employment_record ?? [] as $row) {
            $parts[] = implode(' ', array_filter($row));
        }
        $parts[] = $application->present_department ?? '';
        $parts[] = $application->present_post ?? '';

        return implode(' ', array_filter($parts));
    }
}