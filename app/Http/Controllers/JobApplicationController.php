<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceivedMail;
use App\Models\CompanyJob;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Helpers\IdEncoder;
use App\Helpers\IdEncoderFactory;

class JobApplicationController extends Controller
{


    public function index(Request $request)
    {
        $validSorts = ['reference_number', 'full_name', 'created_at'];
        $sort = in_array($request->sort, $validSorts) ? $request->sort : 'created_at';
        $direction = in_array(strtolower($request->direction), ['asc', 'desc']) ? $request->direction : 'desc';

        $companyJobs = CompanyJob::all();

        $query = JobApplication::query();

        // Filter by company_job_id via reference_number prefix
        if ($request->filled('company_job_id')) {
            $companyJob = CompanyJob::where('company_job_id', $request->company_job_id)->first();
            if ($companyJob) {
                $jobCode = $companyJob->job_code;
                $query->where('reference_number', 'like', "{$jobCode}%");
            }
        }

        // Filter by date range (created_at)
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        // Filter by general search (reference_number or full_name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $applications = $query
            ->orderBy($sort, $direction)
            ->filter($request->all()) // Ensure this method exists in your model
            ->paginate($request->per_page ?? 10)
            ->appends($request->query());

        return view('job-applications.index', [
            'applications' => $applications,
            'companyJobs' => $companyJobs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companyJobs = CompanyJob::where('will_become_active_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('will_become_inactive_at')
                    ->orWhere('will_become_inactive_at', '>=', now());
            })
            ->get();

        return view('job-applications.create', compact('companyJobs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Section 1: Post & Personal Details
            'personal_details.post' => 'required|string|max:255',
            'personal_details.reference_number' => 'required|string|max:255',
            'personal_details.full_name' => 'required|string|max:255',
            'personal_details.date_of_birth' => 'required|date',
            'personal_details.email' => 'required|email|unique:job_applications,email',
            'personal_details.telephone_number' => 'required|string|max:20',

            // Section 2: Nationality & Residence
            'nationality_and_residence.nationality' => 'required|string|max:255',
            'nationality_and_residence.nin' => 'required|string|max:255',
            'nationality_and_residence.residency_type' => 'required|string|max:255',

            'family_background.marital_status' => 'nullable|string|max:255',

            'education_training.*.qualification' => 'nullable|string|max:255',
            'education_training.*.institution' => 'nullable|string|max:255',
            'education_training.*.year' => 'nullable|string',
            // Employment Record
            'employment_record' => 'nullable|array',
            'employment_record.*.period' => 'nullable|string',
            'employment_record.*.position' => 'nullable|string',
            'employment_record.*.details' => 'nullable|string',

            // Criminal History
            'criminalHistory' => 'required',
            'criminal_history_details' => 'required_if:criminalHistory,yes|nullable|string',

            // Availability & Salary
            'availability_if_appointed' => 'required|string|max:255',
            'minimum_salary_expected' => 'required|numeric',

            // References
            'reference' => 'nullable|array',
            'reference.*' => 'nullable|string',
            'recommender_name' => 'nullable|string|max:255',
            'recommender_title' => 'nullable|string|max:255',

            // Document Validation (Add this section)
            'academic_documents' => 'nullable|array|max:5',
            'academic_documents.*' => 'file|mimes:pdf|max:2048',
            'cv' => 'required|file|mimes:pdf|max:2048',
            'other_documents' => 'nullable|array|max:5',
            'other_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);



        // Handle Academic Documents
        $academicDocumentsPaths = [];
        if ($request->hasFile('academic_documents')) {
            foreach ($request->file('academic_documents') as $file) {
                $path = $file->store('academic_docs', 'public');
                $academicDocumentsPaths[] = $path;
            }
        }

        // Handle CV
        $cvPath = $request->file('cv')->store('cvs', 'public');

        // Handle Other Documents
        $otherDocumentsPaths = [];
        if ($request->hasFile('other_documents')) {
            foreach ($request->file('other_documents') as $file) {
                $path = $file->store('other_docs', 'public');
                $otherDocumentsPaths[] = $path;
            }
        }

        // Transform form data for database storage
        $applicationData = [
            // Section 1
            'post_applied' => $validated['personal_details']['post'],
            'reference_number' => $validated['personal_details']['reference_number'],
            'full_name' => $validated['personal_details']['full_name'],
            'date_of_birth' => $validated['personal_details']['date_of_birth'],
            'email' => $validated['personal_details']['email'],
            'telephone' => $validated['personal_details']['telephone_number'],

            // Section 2
            'nationality' => $validated['nationality_and_residence']['nationality'],
            'nin' => $validated['nationality_and_residence']['nin'],
            'residency_type' => $validated['nationality_and_residence']['residency_type'],

            // Section 4
            'marital_status' => $validated['family_background']['marital_status'] ?? null,

            // Education & Training
            'education_training' => $validated['education_training'] ?? null,


            // Employment
            'employment_record' => $validated['employment_record'],

            // Criminal History
            'criminal_convicted' => $validated['criminalHistory'] == "yes" ? true : false,
            'criminal_details' => $validated['criminal_history_details'] ?? null,

            // Salary & Availability
            'availability' => $validated['availability_if_appointed'],
            'salary_expectation' => $validated['minimum_salary_expected'],

            // References
            'references' => $validated['reference'] ?? null,
            'recommender_name' => $validated['recommender_name'] ?? null,
            'recommender_title' => $validated['recommender_title'] ?? null,

            'academic_documents' => $academicDocumentsPaths,
            'cv' => $cvPath,
            'other_documents' => $otherDocumentsPaths,
        ];



        $JobApplication = JobApplication::create($applicationData);

        $encoder = IdEncoderFactory::getDefaultEncoder();

        //encode the application ID in the reference number
        $encodedId = $encoder->encode($JobApplication->id); //pass this to thankyou route
        //generate url
        $url = route('job-applications.edit', ['application' => $encodedId]);
        Mail::to($validated['personal_details']['email'])
            ->send(new ApplicationReceivedMail($JobApplication, $validated['personal_details']['full_name'], $url));

        return to_route('thankyou')->with([
            'success' => 'Application submitted successfully!',
            'encodedId' => $encodedId,
        ]);
    }

    public function thankyou()
    {
        return view('job-applications.received');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $uncst_job_application)
    {
        $application = $uncst_job_application;
        return view('job-applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $application)
    {
        $companyJobs = CompanyJob::where('will_become_active_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('will_become_inactive_at')
                    ->orWhere('will_become_inactive_at', '>=', now());
            })
            ->get();
        $encoder = IdEncoderFactory::getDefaultEncoder();

        // decode the $application to get the exact application id
        $applicationId = $encoder->decode($application);
        $application = JobApplication::findOrFail($applicationId);
        return view('job-applications.edit', compact('application', 'companyJobs'));
    }



    public function update(Request $request, JobApplication $application)
    {
        $validated = $request->validate([
            // Section 1: Post & Personal Details
            'personal_details.post' => 'required|string|max:255',
            'personal_details.reference_number' => 'required|string|max:255',
            'personal_details.full_name' => 'required|string|max:255',
            'personal_details.date_of_birth' => 'required|date',
            'personal_details.email' => [
                'required',
                'email',
                Rule::unique('job_applications', 'email')->ignore($application->id)
            ],
            'personal_details.telephone_number' => 'required|string|max:20',

            // Section 2: Nationality & Residence
            'nationality_and_residence.nationality' => 'required|string|max:255',
            'nationality_and_residence.nin' => 'required|string|max:255',
            'nationality_and_residence.residency_type' => 'required|string|max:255',

            'family_background.marital_status' => 'nullable|string|max:255',

            // Education & Training
            'education_training' => 'nullable|array',
            'education_training.*.qualification' => 'nullable|string|max:255',
            'education_training.*.institution' => 'nullable|string|max:255',
            'education_training.*.year' => 'nullable|string',

            // Employment Record
            'employment_record' => 'nullable|array',
            'employment_record.*.period' => 'nullable|string',
            'employment_record.*.position' => 'nullable|string',
            'employment_record.*.details' => 'nullable|string',

            // Criminal History
            'criminalHistory' => 'required',
            'criminal_history_details' => 'required_if:criminalHistory,yes|nullable|string',

            // Availability & Salary
            'availability_if_appointed' => 'required|string|max:255',
            'minimum_salary_expected' => 'required|numeric',

            // References
            'reference' => 'nullable|array',
            'reference.*' => 'nullable|string',
            'recommender_name' => 'nullable|string|max:255',
            'recommender_title' => 'nullable|string|max:255',

            // Document Validation (Updated for update scenario)
            'academic_documents' => 'nullable|array|max:5',
            'academic_documents.*' => 'sometimes|file|mimes:pdf|max:2048',
            'cv' => 'sometimes|file|mimes:pdf|max:2048',
            'other_documents' => 'nullable|array|max:5',
            'other_documents.*' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $application = JobApplication::findOrFail($application->id);

        try {
            // Initialize file path variables
            $newAcademicPaths = [];
            $newCvPath = null;
            $newOtherDocPaths = [];

            // Process Academic Documents
            if ($request->hasFile('academic_documents')) {
                foreach ($request->file('academic_documents') as $file) {
                    $path = $file->store('academic_docs', 'public');
                    $newAcademicPaths[] = $path;
                }
            }

            // Process CV
            if ($request->hasFile('cv')) {
                $newCvPath = $request->file('cv')->store('cvs', 'public');
            }

            // Process Other Documents
            if ($request->hasFile('other_documents')) {
                foreach ($request->file('other_documents') as $file) {
                    $path = $file->store('other_docs', 'public');
                    $newOtherDocPaths[] = $path;
                }
            }

            // Remember old file paths for cleanup
            $oldAcademicPaths = $application->academic_documents ?? [];
            $oldCvPath = $application->cv;
            $oldOtherDocPaths = $application->other_documents ?? [];

            // Prepare update data
            $updateData = [
                'post_applied' => $validated['personal_details']['post'],
                'reference_number' => $validated['personal_details']['reference_number'],
                'full_name' => $validated['personal_details']['full_name'],
                'date_of_birth' => $validated['personal_details']['date_of_birth'],
                'email' => $validated['personal_details']['email'],
                'telephone' => $validated['personal_details']['telephone_number'],

                'nationality' => $validated['nationality_and_residence']['nationality'],
                'nin' => $validated['nationality_and_residence']['nin'],
                'residency_type' => $validated['nationality_and_residence']['residency_type'],

                'marital_status' => $validated['family_background']['marital_status'] ?? null,

                'education_training' => $validated['education_training'] ?? null,
                'employment_record' => $validated['employment_record'],

                'criminal_convicted' => $validated['criminalHistory'] == "yes" ? true : false,
                'criminal_details' => $validated['criminal_history_details'] ?? null,

                'availability' => $validated['availability_if_appointed'],
                'salary_expectation' => $validated['minimum_salary_expected'],

                'references' => $validated['reference'] ?? null,
                'recommender_name' => $validated['recommender_name'] ?? null,
                'recommender_title' => $validated['recommender_title'] ?? null,
            ];

            // Update file paths only if new files were uploaded
            if (!empty($newAcademicPaths)) {
                $updateData['academic_documents'] = $newAcademicPaths;
            }
            if ($newCvPath) {
                $updateData['cv'] = $newCvPath;
            }
            if (!empty($newOtherDocPaths)) {
                $updateData['other_documents'] = $newOtherDocPaths;
            }

            // Update the application
            $application->update($updateData);

            // Cleanup old files after successful update
            if (!empty($newAcademicPaths)) {
                foreach ($oldAcademicPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
            if ($newCvPath && $oldCvPath) {
                Storage::disk('public')->delete($oldCvPath);
            }
            if (!empty($newOtherDocPaths)) {
                foreach ($oldOtherDocPaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
            //decode the application ID in the reference number
            $encoder = IdEncoderFactory::getDefaultEncoder();
            $encodedId = $encoder->encode($application->id);
            return redirect()->route('job-applications.edit', $encodedId)
                ->with('success', 'Application updated successfully!');
        } catch (\Exception $e) {
            // Cleanup newly uploaded files on error
            foreach ($newAcademicPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            if ($newCvPath) {
                Storage::disk('public')->delete($newCvPath);
            }
            foreach ($newOtherDocPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return back()->withInput()
                ->with('error', 'Error updating application: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApplication $application)
    {
        try {
            $application->delete();
            return redirect()->route('uncst-job-applications.index')
                ->with('success', 'Application deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting application: ' . $e->getMessage());
        }
    }
}
