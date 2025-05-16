<?php

namespace App\Http\Controllers;

use App\Models\CompanyJob;
use Illuminate\Http\Request;

class CompanyJobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companyJobs = CompanyJob::paginate(10);
        return view('company-jobs.index', compact('companyJobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company-jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_code' => 'required',
            'job_title' => 'required'
        ]);
        CompanyJob::create($request->all());

        return redirect()->route('company-jobs.index')->with('success', 'Company Job created successfully.');
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
    public function edit(CompanyJob $companyJob)
    {
        return view('company-jobs.edit', compact('companyJob'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanyJob $companyJob)
    {
        //update
        $companyJob->update($request->all());

        return redirect()->route('company-jobs.index')->with('success', 'Company Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
