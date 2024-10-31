<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CompanyJob;
use App\Models\Entry;
use App\Models\Form;
use Illuminate\Http\Request;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //convert the form fields to json
        $responses = json_encode($request->except('_token', 'form_id', 'company_job_id'));
        $form_id = $request->input('form_id');

        $entry = new Entry();
        $entry->form_id = $form_id;
        $entry->responses = $responses;

        $entry->save();

        $job_application = new Application();
        $job_application->company_job_id = $request->input('company_job_id');
        $job_application->entry_id = $entry->id;
        $job_application->save();

        return back()->with('success', 'Application submitted successfully! Thank you for your response.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function survey(Request $request)
    {
        //appraisal type
        $type = 'application';

        $company_jobs = CompanyJob::all();
        //get the form
        $form = Form::where('uuid', '5b39330c-9bed-4289-a60b-d19947d5f5d9')->firstOrFail();

        // Load sections with their related fields
        $form->load(['sections.fields']);

        return view('applications.form', compact('form', 'company_jobs'));
    }
}
