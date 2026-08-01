<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationStatusChangedMail;
use App\Models\CompanyJob;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * Public screening-board flow.
 *
 * HR generates one shareable link + PIN per job posting (see
 * CompanyJobController::generateScreeningLink). Board members open the link,
 * enter the PIN once (verified per-session, per-job), then can view every
 * applicant for that job and mark them shortlisted or rejected. No HRMIS
 * account is required — access is gated entirely by token + PIN.
 */
class ScreeningController extends Controller
{
    /**
     * PIN entry (if not yet verified this session) or the applicants list.
     * GET /screening/{token}
     */
    public function show(Request $request, string $token)
    {
        $job = $this->resolveJob($token);

        if (!$this->isVerified($request, $job)) {
            return view('screening.pin', compact('job'));
        }

        $applications = JobApplication::where('company_job_id', $job->company_job_id)
            ->orderByRaw('score IS NULL ASC')
            ->orderByDesc('score')
            ->get();

        return view('screening.index', compact('job', 'applications'));
    }

    /**
     * Verify the shared PIN and unlock the applicants list for this session.
     * POST /screening/{token}/verify
     */
    public function verifyPin(Request $request, string $token)
    {
        $job = $this->resolveJob($token);

        $request->validate(['pin' => 'required|string']);

        if (!$job->checkScreeningPin($request->input('pin'))) {
            return back()->with('error', 'Incorrect PIN. Please try again.');
        }

        $request->session()->put($this->sessionKey($job), true);

        return redirect()->route('screening.show', $token);
    }

    /**
     * End the screening session on this device (useful on a shared computer).
     * POST /screening/{token}/exit
     */
    public function exit(Request $request, string $token)
    {
        $job = $this->resolveJob($token);
        $request->session()->forget($this->sessionKey($job));

        return redirect()->route('screening.show', $token);
    }

    /**
     * Full applicant detail, read by the board before deciding.
     * GET /screening/{token}/applications/{application}
     */
    public function viewApplication(Request $request, string $token, JobApplication $application)
    {
        $job = $this->resolveJob($token);
        $this->ensureVerified($request, $job);
        $this->ensureBelongsToJob($application, $job);

        return view('screening.show', compact('job', 'application', 'token'));
    }

    /**
     * Shortlist or reject a candidate. This is the only status change a
     * screening board can make — later stages (interview, offer, hire) stay
     * with HR.
     * POST /screening/{token}/applications/{application}/status
     */
    public function updateStatus(Request $request, string $token, JobApplication $application)
    {
        $job = $this->resolveJob($token);
        $this->ensureVerified($request, $job);
        $this->ensureBelongsToJob($application, $job);

        $request->validate([
            'status'           => ['required', Rule::in([JobApplication::STATUS_SHORTLISTED, JobApplication::STATUS_REJECTED])],
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $previousStatus = $application->status;
        $newStatus      = $request->input('status');

        if ($previousStatus === $newStatus) {
            return back();
        }

        $application->update([
            'status'           => $newStatus,
            'rejection_reason' => $newStatus === JobApplication::STATUS_REJECTED
                ? $request->input('rejection_reason')
                : $application->rejection_reason,
        ]);

        // Same deadline-aware deferral as HR's own status changes — don't tell a
        // candidate they're rejected while the posting is still open to others.
        $deferRejection = $newStatus === JobApplication::STATUS_REJECTED
            && !$job->applicationsClosed();

        if (!$deferRejection) {
            try {
                Mail::to($application->email)
                    ->send(new ApplicationStatusChangedMail($application, $previousStatus));
                $application->forceFill(['status_notified_at' => now()])->saveQuietly();
            } catch (\Throwable $e) {
                Log::warning("Screening status email failed for application #{$application->id}: {$e->getMessage()}");
            }
        }

        return back()->with('success', 'Candidate marked as "' . ucfirst($newStatus) . '".'
            . ($deferRejection ? ' The rejection email will be sent automatically once the application deadline passes.' : ''));
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function resolveJob(string $token): CompanyJob
    {
        return CompanyJob::where('screening_token', $token)->firstOrFail();
    }

    private function sessionKey(CompanyJob $job): string
    {
        return "screening_verified.{$job->company_job_id}";
    }

    private function isVerified(Request $request, CompanyJob $job): bool
    {
        return (bool) $request->session()->get($this->sessionKey($job));
    }

    private function ensureVerified(Request $request, CompanyJob $job): void
    {
        if (!$this->isVerified($request, $job)) {
            abort(403, 'Please enter the screening PIN first.');
        }
    }

    /** Prevent guessing an application ID to view/act on it under a different job's link. */
    private function ensureBelongsToJob(JobApplication $application, CompanyJob $job): void
    {
        if ((string) $application->company_job_id !== (string) $job->company_job_id) {
            abort(404);
        }
    }
}
