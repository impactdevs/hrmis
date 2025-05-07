<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationAccepted;
use App\Mail\ApplicationReceivedMail;
use App\Models\Application;
use App\Models\CompanyJob;
use App\Models\Entry;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Initialize the query for applications
        $query = Application::latest()->with('entry');

        // Filter by job if a job_id is provided
        if ($request->filled('job_id')) {
            $query->where('company_job_id', $request->input('job_id'));
        }

        // Paginate the results
        $applications = $query->paginate();

        // Get all jobs for the filter dropdown
        $company_jobs = CompanyJob::all();

        return view('applications.index', compact('applications', 'company_jobs'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Initialize cvPath as null
        $cvPath = null;

        // Handle the passport photo upload (if file is present)
        if ($request->hasFile('132')) {
            // Store the photo and get the path
            $cvPath = $request->file('132')->store('cvs', 'public');
        }

        // Convert the form fields to JSON, excluding certain keys
        $responses = json_encode($request->except('_token', 'form_id', 'company_job_id'));

        // If cvPath is not null, update the '132' key in responses
        if ($cvPath) {
            $responsesArray = json_decode($responses, true);
            $responsesArray['132'] = $cvPath;
            $responses = json_encode($responsesArray);
        }

        // Get the form ID from the request
        $form_id = $request->input('form_id');

        // Create a new entry
        $entry = new Entry();
        $entry->form_id = $form_id;
        $entry->responses = $responses;
        $entry->save();

        // Create a new job application
        $job_application = new Application();
        $job_application->company_job_id = $request->input('company_job_id');
        $job_application->entry_id = $entry->id;
        $job_application->save();

        // Send confirmation email
        $email = $request->input('97');
        $name = $request->input('93') ?? "";
        Mail::to($email)->send(new ApplicationReceivedMail($job_application, $name));

        return back()->with('success', 'Application submitted successfully! You should receive a confirmation email for your application.');
    }


    public function survey(Request $request)
    {
        // Application type
        $type = 'application';

        $company_jobs = CompanyJob::all();

        // Try to get the form
        $form = Form::where('uuid', '5b39330c-9bed-4289-a60b-d19947d5f5d9')->first();

        // If form doesn't exist, run the SQL script
        if (!$form) {
            $sqlPath = public_path('sql/forms.sql');

            if (File::exists($sqlPath)) {
                DB::unprepared(File::get($sqlPath));

                // Try fetching the form again
                $form = Form::where('uuid', '5b39330c-9bed-4289-a60b-d19947d5f5d9')->firstOrFail();
            } else {
                abort(500, 'SQL file not found.');
            }
        }

        // Load sections with their related fields
        $form->load(['sections.fields']);

        return view('applications.form', compact('form', 'company_jobs'));
    }


    public function approveOrReject(Request $request)
    {
        try {
            $approval_status = request()->input('status');
            $application = Application::find(request()->input('applications_id'));
            $application->approval_status = $approval_status;
            $application->save();

            $message = '';



            if ($approval_status == 'approve') {
                $email = json_decode($application->entry->responses)->{'97'} ?? "";
                $name = json_decode($application->entry->responses)->{'93'} ?? "";

                //check if email is valid and exists

                Mail::to($email)->send(new ApplicationAccepted($application, $name));
                $message = 'application request approved successfully.';
            }

            if ($approval_status == 'reject') {
                $application->rejection_reason = request()->input('reason');
                $message = 'application request rejected successfully.';
            }
            return response()->json([
                'status' => 'success',
                'message' => $message
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'approval failed',
            ], 500);
        }
    }
}
