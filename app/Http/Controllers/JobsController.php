<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceivedMail;
use App\Models\CompanyJob;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class JobsController extends Controller
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
        $companyJobs = CompanyJob::all();
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
            'personal_details.email' => 'required|email',
            'personal_details.telephone_number' => 'required|string|max:20',

            // Section 2: Nationality & Residence
            'nationality_and_residence.nationality' => 'required|string|max:255',
            'nationality_and_residence.home_district' => 'required|string|max:255',
            'nationality_and_residence.sub_county' => 'required|string|max:255',
            'nationality_and_residence.village' => 'required|string|max:255',
            'nationality_and_residence.nin' => 'required|string|max:255',
            'nationality_and_residence.residency_type' => 'required|string|max:255',

            'university.name' => 'required|string|max:255',
            'university.course' => 'required|string|max:255',
            'university.start_date' => 'required|date',
            'university.end_date' => 'required|date|after_or_equal:university.start_date',
            // 'university.cgpa' => 'required|numeric|between:0,5',

            // Section 3: Work Background
            'work_background.present_department' => 'nullable|string|max:255',
            'work_background.present_post' => 'nullable|string|max:255',
            'work_background.date_of_appointment_to_present_post' => 'nullable|date',
            'work_background.terms_of_employment' => 'nullable|string|max:255',

            'family_background.marital_status' => 'nullable|string|max:255',


            // Section 6: UCE Details
            'uce.passed' => 'required|in:yes,no',
            'uce.year' => 'required_if:uce.passed,yes|nullable|digits:4',
            'uce.scores' => 'required_if:uce.passed,yes|array',

            // These rules only apply to rows that are actually filled
            'uce.scores.*.subject' => 'nullable|required_with:uce.scores.*.grade|string',
            'uce.scores.*.grade' => 'nullable|required_with:uce.scores.*.subject|string',

            'uace.passed' => 'required|in:yes,no',
            'uace.year' => 'required_if:uace.passed,yes|nullable|digits:4',
            'uace.scores' => 'required_if:uace.passed,yes|array',

            // Only require subject if grade is present, and vice versa
            'uace.scores.*.subject' => 'nullable|required_with:uace.scores.*.grade|string',
            'uace.scores.*.grade' => 'nullable|required_with:uace.scores.*.subject|string',


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
        if ($request->hasFile('academic_documents')){
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
            'home_district' => $validated['nationality_and_residence']['home_district'],
            'sub_county' => $validated['nationality_and_residence']['sub_county'],
            'village' => $validated['nationality_and_residence']['village'],
            'nin' => $validated['nationality_and_residence']['nin'],
            'residency_type' => $validated['nationality_and_residence']['residency_type'],

            // Section 3
            'present_department' => $validated['work_background']['present_department'],
            'present_post' => $validated['work_background']['present_post'],
            'date_of_appointment_present_post' => $validated['work_background']['date_of_appointment_to_present_post'],
            'terms_of_employment' => $validated['work_background']['terms_of_employment'] ?? null,

            // Section 4
            'marital_status' => $validated['family_background']['marital_status'] ?? null,


            // Education
            'education_history' => $validated['education_history']??null,
            'uce_details' => $validated['uce'],
            'uace_details' => $validated['uace'],
            'university_details' => [
                'name' => $validated['university']['name'] ?? null,
                'course' => $validated['university']['course'] ?? null,
                'start_date' => $validated['university']['start_date'] ?? null,
                'end_date' => $validated['university']['end_date'] ?? null,
                'cgpa' => $validated['university']['cgpa'] ?? null,
            ],

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

        Mail::to($validated['personal_details']['email'])
            ->send(new ApplicationReceivedMail($JobApplication, $validated['personal_details']['full_name']));
        return view('job-applications.received');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $application)
    {
        return view('job-applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobApplication $application)
    {
        return view('job-applications.edit', compact('application'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobApplication $application)
    {
        // Similar validation to store method
        $validated = $request->validate([
            // Section 1: Post & Personal Details
            'personal_details.post' => 'required|string|max:255',
            'personal_details.reference_number' => 'required|string|max:255',
            'personal_details.full_name' => 'required|string|max:255',
            'personal_details.date_of_birth' => 'required|date',
            'personal_details.email' => 'required|email',
            'personal_details.telephone_number' => 'required|string|max:20',

            // Section 2: Nationality & Residence
            'nationality_and_residence.nationality' => 'required|string|max:255',
            'nationality_and_residence.home_district' => 'required|string|max:255',
            'nationality_and_residence.sub_county' => 'required|string|max:255',
            'nationality_and_residence.village' => 'required|string|max:255',
            'nationality_and_residence.nin' => 'required|string|max:255',
            'nationality_and_residence.residency_type' => 'required|in:temporary,permanent',

            // Section 3: Work Background
            'work_background.present_department' => 'required|string|max:255',
            'work_background.present_post' => 'required|string|max:255',
            'work_background.date_of_appointment_to_present_post' => 'required|date',
            'work_background.terms_of_employment' => 'required|in:temp,contract,probation,perm',

            // Section 4: Family Background
            'family_background.marital_status' => 'required|in:married,single,widowed,divorced,separated',

            // Sections 6 & 7: UCE/UACE Details
            'uce.passed' => 'required|in:yes,no',
            // 'uce.year' => 'required_if:uce.passed,yes|nullable|digits:4',
            // 'uce.scores' => 'required_if:uce.passed,yes|array',
            'uce.scores.*.subject' => 'required_with:uce.scores|string',
            'uce.scores.*.grade' => 'required_with:uce.scores|string',

            'uace.passed' => 'required|in:yes,no',
            // 'uace.year' => 'required_if:uace.passed,yes|nullable|digits:4',
            // 'uace.scores' => 'required_if:uace.passed,yes|array',
            'uace.scores.*.subject' => 'required_with:uace.scores|string',
            'uace.scores.*.grade' => 'required_with:uace.scores|string',

            // Employment Record
            'employment_record' => 'required|array',
            'employment_record.*.period' => 'required|string',
            'employment_record.*.position' => 'required|string',
            'employment_record.*.details' => 'required|string',

            // Criminal History
            'criminalHistory' => 'required|in:Yes,No',
            // 'criminal_history_details' => 'required_if:criminalHistory,Yes|nullable|string',

            // Availability & Salary
            'availability_if_appointed' => 'required|string|max:255',
            'minimum_salary_expected' => 'required|numeric',

            // References
            'reference' => 'nullable|array',
            'reference.*' => 'string',
            'recommender_name' => 'nullable|string|max:255',
            'recommender_title' => 'nullable|string|max:255',

            'academic_documents' => 'required|array|max:5',
            'academic_documents.*' => 'file|mimes:pdf|max:2048',
            'cv' => 'required|file|mimes:pdf|max:2048',
            'other_documents' => 'nullable|array|max:5',
            'other_documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);


        try {
            $application->update($validated);
            return redirect()->route('uncst-job-applications.show', $application)
                ->with('success', 'Application updated successfully!');
        } catch (\Exception $e) {
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
