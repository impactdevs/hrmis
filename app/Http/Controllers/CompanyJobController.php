<?php

namespace App\Http\Controllers;

use App\Models\CompanyJob;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class CompanyJobController extends Controller
{
    public function index()
    {
        $jobs = CompanyJob::withCount('jobApplications')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('company-jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('company-jobs.create', [
            'qualificationLevels' => array_keys(CompanyJob::QUALIFICATION_LEVELS),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateJob($request);
        $job = CompanyJob::create($validated);

        return redirect()
            ->route('hr.company-jobs.show', $job->company_job_id)
            ->with('success', 'Job posting created. Copy the application link below to share with candidates.');
    }

    public function show(CompanyJob $companyJob)
    {
        $companyJob->loadCount('jobApplications');

        $statusCounts = $companyJob->jobApplications()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('company-jobs.show', compact('companyJob', 'statusCounts'));
    }

    public function edit(CompanyJob $companyJob)
    {
        return view('company-jobs.edit', [
            'companyJob'          => $companyJob,
            'qualificationLevels' => array_keys(CompanyJob::QUALIFICATION_LEVELS),
        ]);
    }

    public function update(Request $request, CompanyJob $companyJob)
    {
        $validated = $this->validateJob($request, $companyJob);
        $companyJob->update($validated);

        return redirect()
            ->route('hr.company-jobs.show', $companyJob->company_job_id)
            ->with('success', 'Job posting updated.');
    }

    public function regenerateLink(CompanyJob $companyJob)
    {
        $companyJob->regenerateToken();
        return back()->with('success', 'Application link regenerated. The previous link is now invalid.');
    }

    public function destroy(CompanyJob $companyJob)
    {
        if ($companyJob->jobApplications()->exists()) {
            return back()->with('error', 'Cannot delete a posting that has received applications.');
        }
        $companyJob->delete();
        return redirect()->route('hr.company-jobs.index')->with('success', 'Job posting deleted.');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function validateJob(Request $request, ?CompanyJob $existing = null): array
    {
        $uniqueCode = 'unique:company_jobs,job_code';
        if ($existing) {
            $uniqueCode .= ',' . $existing->company_job_id . ',company_job_id';
        }

        $validated = $request->validate([
            'job_code'                       => "required|string|max:50|{$uniqueCode}",
            'job_title'                      => 'required|string|max:255',
            'job_description'                => 'nullable|string',
            'will_become_active_at'          => 'required|date',
            'will_become_inactive_at'        => 'required|date|after:will_become_active_at',

            // Criteria
            'criteria_min_qualification'     => 'nullable|in:Certificate,Diploma,Degree,Masters,PhD',
            'criteria_min_experience_years'  => 'nullable|integer|min:0|max:50',
            'criteria_min_age'               => 'nullable|integer|min:18|max:100',
            'criteria_max_age'               => 'nullable|integer|min:18|max:100|gte:criteria_min_age',
            'criteria_required_keywords'     => 'nullable|string', // comma-separated from form

            // Weights
            'weight_qualification'           => 'required|integer|min:0|max:100',
            'weight_experience'              => 'required|integer|min:0|max:100',
            'weight_keyword_match'           => 'required|integer|min:0|max:100',
            'weight_age_fit'                 => 'required|integer|min:0|max:100',
        ]);

        // Convert comma-separated keywords string → array
        if (!empty($validated['criteria_required_keywords'])) {
            $validated['criteria_required_keywords'] = array_values(array_filter(
                array_map('trim', explode(',', $validated['criteria_required_keywords']))
            ));
        } else {
            $validated['criteria_required_keywords'] = null;
        }

        return $validated;
    }
}