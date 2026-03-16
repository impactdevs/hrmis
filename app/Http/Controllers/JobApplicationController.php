<?php

namespace App\Http\Controllers;

use App\Helpers\ApplicationIdEncoder;
use App\Mail\ApplicationReceivedMail;
use App\Mail\ApplicationStatusChangedMail;
use App\Models\CompanyJob;
use App\Models\JobApplication;
use App\Services\ApplicationScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JobApplicationController extends Controller
{
    public function __construct(private ApplicationScoringService $scorer) {}

    // =========================================================================
    // PUBLIC ROUTES  (no auth)
    // =========================================================================

    /**
     * Show the application form for a job via its public token.
     * GET /apply/{token}
     */
    public function showForm(string $token)
    {
        $job = CompanyJob::where('public_token', $token)->firstOrFail();

        return match ($job->statusLabel()) {
            'upcoming' => view('job-applications.closed', [
                'message' => 'This application is not yet open. It opens on '
                    . $job->will_become_active_at->format('d M Y \a\t H:i') . '.',
            ]),
            'closed' => view('job-applications.closed', [
                'message' => 'This application closed on '
                    . $job->will_become_inactive_at->format('d M Y') . '.',
            ]),
            default => view('job-applications.create', compact('job')),
        };
    }

    /**
     * Store a new application submitted via the public form.
     * POST /apply/{token}
     */
    public function store(string $token, Request $request)
    {
        $job = CompanyJob::where('public_token', $token)->firstOrFail();

        if ($job->statusLabel() !== 'active') {
            return back()->with('error', 'Sorry, this application is no longer accepting submissions.');
        }

        $validated    = $this->validateApplication($request, $job);
        $uploadedPaths = [];

        try {
            [$academicPaths, $cvPath, $otherPaths, $uploadedPaths] = $this->uploadFiles($request);

            $application = JobApplication::create(array_merge(
                $this->mapToModel($validated),
                [
                    'company_job_id'     => $job->company_job_id,
                    'post_applied'       => $job->job_title,
                    'reference_number'   => $job->job_code,
                    'academic_documents' => $academicPaths,
                    'cv'                 => $cvPath,
                    'other_documents'    => $otherPaths,
                ]
            ));

        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) Storage::disk('public')->delete($path);
            return back()->withInput()->with('error', 'Submission failed. Please try again.');
        }

        // Score immediately — may auto-reject if criteria not met
        $this->scorer->score($application);
        $application->refresh();

        // Send confirmation email with encoded edit link
        $encodedId = ApplicationIdEncoder::encode($application->id);
        $editUrl   = route('apply.edit', $encodedId);

        try {
            Mail::to($application->email)
                ->send(new ApplicationReceivedMail($application, $editUrl));
        } catch (\Throwable $e) {
            Log::warning("Confirmation email failed for application #{$application->id}: {$e->getMessage()}");
        }

        return redirect()->route('apply.thankyou')
            ->with('applicant_name', $application->full_name)
            ->with('was_rejected', $application->status === JobApplication::STATUS_REJECTED);
    }

    /**
     * Thank you page shown after submission.
     * GET /apply/thank-you
     */
    public function thankyou()
    {
        return view('job-applications.thankyou');
    }

    /**
     * Show edit form — accessed via encoded link in confirmation email.
     * GET /apply/edit/{encodedId}
     */
    public function edit(string $encodedId)
    {
        $application = $this->resolveEncoded($encodedId);

        if ($application->companyJob?->statusLabel() === 'closed') {
            return view('job-applications.closed', [
                'message' => 'The deadline for this application has passed. Edits are no longer accepted.',
            ]);
        }

        // Rejected applications can still be viewed but not edited
        if ($application->status === JobApplication::STATUS_REJECTED && $application->meets_criteria === false) {
            return view('job-applications.rejected-view', compact('application'));
        }

        return view('job-applications.edit', compact('application', 'encodedId'));
    }

    /**
     * Save edits to a submitted application.
     * PUT /apply/edit/{encodedId}
     */
    public function update(string $encodedId, Request $request)
    {
        $application = $this->resolveEncoded($encodedId);

        if ($application->companyJob?->statusLabel() === 'closed') {
            return back()->with('error', 'The deadline has passed. Edits are no longer accepted.');
        }

        $validated     = $this->validateApplication($request, $application->companyJob, $application->id);
        $uploadedPaths = [];

        try {
            [$academicPaths, $cvPath, $otherPaths, $uploadedPaths] = $this->uploadFiles(
                $request, $application
            );

            $application->update(array_merge(
                $this->mapToModel($validated),
                [
                    'academic_documents' => $academicPaths,
                    'cv'                 => $cvPath,
                    'other_documents'    => $otherPaths,
                ]
            ));

        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) Storage::disk('public')->delete($path);
            return back()->withInput()->with('error', 'Update failed. Please try again.');
        }

        // Re-score after edits
        $application->refresh();
        $this->scorer->score($application);

        return redirect()->route('apply.edit', $encodedId)
            ->with('success', 'Your application has been updated successfully.');
    }

    // =========================================================================
    // HR ROUTES  (auth required)
    // =========================================================================

    /**
     * List all applications with filters + sort.
     * GET /hr/job-applications
     */
    public function index(Request $request)
    {
        $validSorts = ['reference_number', 'full_name', 'created_at', 'status', 'score'];
        $sort      = in_array($request->sort, $validSorts) ? $request->sort : 'score';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $companyJobs = CompanyJob::orderBy('job_title')->get();

        $applications = JobApplication::with('companyJob')
            ->filter($request->only(['company_job_id', 'status', 'search', 'created_from', 'created_to']))
            ->orderByRaw('score IS NULL ASC') // scored applications first
            ->orderBy($sort, $direction)
            ->paginate($request->integer('per_page', 15))
            ->appends($request->query());

        return view('job-applications.index', compact('applications', 'companyJobs'));
    }

    /**
     * Kanban pipeline board.
     * GET /hr/job-applications/pipeline
     */
    public function pipeline(Request $request)
    {
        $companyJobs = CompanyJob::orderBy('job_title')->get();
        $jobId       = $request->company_job_id;

        $columns = [];
        foreach (JobApplication::statuses() as $status) {
            $columns[$status] = JobApplication::with('companyJob')
                ->when($jobId, fn($q) => $q->where('company_job_id', $jobId))
                ->where('status', $status)
                ->orderByDesc('score')
                ->get();
        }

        return view('job-applications.pipeline', compact('columns', 'companyJobs', 'jobId'));
    }

    /**
     * Show a single application.
     * GET /hr/job-applications/{application}
     */
    public function show(JobApplication $application)
    {
        $application->load('companyJob');
        return view('job-applications.show', compact('application'));
    }

    /**
     * Update the pipeline status.
     * PATCH /hr/job-applications/{application}/status
     */
    public function updateStatus(Request $request, JobApplication $application)
    {
        $request->validate([
            'status'           => ['required', Rule::in(JobApplication::statuses())],
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $previousStatus = $application->status;
        $newStatus      = $request->status;

        if ($previousStatus === $newStatus) return back();

        $application->update([
            'status'           => $newStatus,
            'rejection_reason' => $newStatus === JobApplication::STATUS_REJECTED
                ? $request->rejection_reason
                : $application->rejection_reason, // preserve auto-rejection reason
        ]);

        try {
            Mail::to($application->email)
                ->send(new ApplicationStatusChangedMail($application, $previousStatus));
        } catch (\Throwable $e) {
            Log::warning("Status email failed for application #{$application->id}: {$e->getMessage()}");
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'status' => $newStatus]);
        }

        return back()->with('success', 'Status updated to ' . ucfirst($newStatus) . '.');
    }

    /**
     * Delete an application and its uploaded files.
     * DELETE /hr/job-applications/{application}
     */
    public function destroy(JobApplication $application)
    {
        foreach ($application->academic_documents ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        if ($application->cv) Storage::disk('public')->delete($application->cv);
        foreach ($application->other_documents ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $application->delete();

        return redirect()->route('hr.job-applications.index')
            ->with('success', 'Application deleted.');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function resolveEncoded(string $encodedId): JobApplication
    {
        $id = ApplicationIdEncoder::decode($encodedId);
        return JobApplication::findOrFail($id);
    }

    private function validateApplication(Request $request, ?CompanyJob $job, ?int $ignoreId = null): array
    {
        $emailRule = ['required', 'email'];
        if ($job) {
            $emailRule[] = Rule::unique('job_applications', 'email')
                ->where('company_job_id', $job->company_job_id)
                ->ignore($ignoreId);
        }

        return $request->validate([
            'personal_details.full_name'                       => 'required|string|max:255',
            'personal_details.date_of_birth'                   => 'required|date|before:-16 years',
            'personal_details.email'                           => $emailRule,
            'personal_details.telephone_number'                => 'required|string|max:30',

            'nationality_and_residence.nationality'            => 'required|string|max:100',
            'nationality_and_residence.nin'                    => 'required|string|max:20',
            'nationality_and_residence.home_district'          => 'nullable|string|max:100',
            'nationality_and_residence.sub_county'             => 'nullable|string|max:100',
            'nationality_and_residence.village'                => 'nullable|string|max:100',
            'nationality_and_residence.residency_type'         => 'required|in:Temporary,Permanent',

            'work_background.present_department'               => 'nullable|string|max:255',
            'work_background.present_post'                     => 'nullable|string|max:255',
            'work_background.date_of_appointment_present_post' => 'nullable|date',
            'work_background.terms_of_employment'              => 'nullable|string|max:100',

            'family_background.marital_status'                 => 'nullable|string|max:50',

            'employment_record'                                => 'nullable|array',
            'employment_record.*.period'                       => 'nullable|string|max:50',
            'employment_record.*.position'                     => 'nullable|string|max:255',
            'employment_record.*.details'                      => 'nullable|string|max:500',

            'education_training'                               => 'nullable|array',
            'education_training.*.qualification'               => 'nullable|string|max:255',
            'education_training.*.institution'                 => 'nullable|string|max:255',
            'education_training.*.year'                        => 'nullable|string|max:10',

            'criminalHistory'                                  => 'required|in:yes,no',
            'criminal_history_details'                         => 'required_if:criminalHistory,yes|nullable|string|max:1000',

            'availability_if_appointed'                        => 'required|string|max:255',
            'minimum_salary_expected'                          => 'required|numeric|min:0',

            'reference'                                        => 'nullable|array',
            'reference.*'                                      => 'nullable|string|max:500',
            'recommender_name'                                 => 'nullable|string|max:255',
            'recommender_title'                                => 'nullable|string|max:255',

            'academic_documents'                               => 'nullable|array|max:5',
            'academic_documents.*'                             => 'file|mimes:pdf|max:2048',
            'cv'                                               => ($ignoreId ? 'nullable' : 'required') . '|file|mimes:pdf|max:2048',
            'other_documents'                                  => 'nullable|array|max:5',
            'other_documents.*'                                => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
    }

    /**
     * Handle all file uploads.
     * Returns [academicPaths, cvPath, otherPaths, allUploadedPaths].
     * On update, keeps existing files if no new ones uploaded.
     */
    private function uploadFiles(Request $request, ?JobApplication $existing = null): array
    {
        $allPaths = [];

        // Academic documents
        $academicPaths = $existing?->academic_documents ?? [];
        if ($request->hasFile('academic_documents')) {
            // Delete old files on update
            if ($existing) {
                foreach ($academicPaths as $p) Storage::disk('public')->delete($p);
            }
            $academicPaths = [];
            foreach ($request->file('academic_documents') as $file) {
                $path = $file->store('academic_docs', 'public');
                $academicPaths[] = $path;
                $allPaths[]      = $path;
            }
        }

        // CV
        $cvPath = $existing?->cv;
        if ($request->hasFile('cv')) {
            if ($existing && $cvPath) Storage::disk('public')->delete($cvPath);
            $cvPath     = $request->file('cv')->store('cvs', 'public');
            $allPaths[] = $cvPath;
        }

        // Other documents
        $otherPaths = $existing?->other_documents ?? [];
        if ($request->hasFile('other_documents')) {
            if ($existing) {
                foreach ($otherPaths as $p) Storage::disk('public')->delete($p);
            }
            $otherPaths = [];
            foreach ($request->file('other_documents') as $file) {
                $path = $file->store('other_docs', 'public');
                $otherPaths[] = $path;
                $allPaths[]   = $path;
            }
        }

        return [$academicPaths, $cvPath, $otherPaths, $allPaths];
    }

    /** Map validated nested form data to flat model columns. */
    private function mapToModel(array $v): array
    {
        return [
            'full_name'                        => $v['personal_details']['full_name'],
            'date_of_birth'                    => $v['personal_details']['date_of_birth'],
            'email'                            => $v['personal_details']['email'],
            'telephone'                        => $v['personal_details']['telephone_number'],

            'nationality'                      => $v['nationality_and_residence']['nationality'],
            'nin'                              => $v['nationality_and_residence']['nin'],
            'home_district'                    => $v['nationality_and_residence']['home_district'] ?? null,
            'sub_county'                       => $v['nationality_and_residence']['sub_county'] ?? null,
            'village'                          => $v['nationality_and_residence']['village'] ?? null,
            'residency_type'                   => $v['nationality_and_residence']['residency_type'],

            'present_department'               => $v['work_background']['present_department'] ?? null,
            'present_post'                     => $v['work_background']['present_post'] ?? null,
            'date_of_appointment_present_post' => $v['work_background']['date_of_appointment_present_post'] ?? null,
            'terms_of_employment'              => $v['work_background']['terms_of_employment'] ?? null,

            'marital_status'                   => $v['family_background']['marital_status'] ?? null,
            'employment_record'                => $v['employment_record'] ?? null,
            'education_training'               => $v['education_training'] ?? null,

            'criminal_convicted'               => $v['criminalHistory'] === 'yes',
            'criminal_details'                 => $v['criminal_history_details'] ?? null,

            'availability'                     => $v['availability_if_appointed'],
            'salary_expectation'               => $v['minimum_salary_expected'],

            'references'                       => $v['reference'] ?? null,
            'recommender_name'                 => $v['recommender_name'] ?? null,
            'recommender_title'                => $v['recommender_title'] ?? null,
        ];
    }
}