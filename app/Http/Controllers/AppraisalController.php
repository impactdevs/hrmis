<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\Entry;
use Illuminate\Http\Request;
use App\Models\Form;

class AppraisalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appraisals = Appraisal::latest()->paginate();
        return view('appraisals.index', compact('appraisals'));
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
        $responses = json_encode($request->except('_token', 'form_id'));
        $form_id = $request->input('form_id');

        $entry = new Entry();
        $entry->form_id = $form_id;
        $entry->responses = $responses;
        $entry->save();

        $appraisal = new Appraisal();
        $appraisal->entry_id = $entry->id;
        $appraisal->employee_id = auth()->user()->employee->employee_id;
        $appraisal->save();

        return back()->with('success', 'Appraisal submitted successfully! Thank you for your response.');
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
        $type = 'appraisal';
        //get the form
        $form = Form::where('uuid', '6156ada8-c020-4cdf-af47-9d6eaf1dd16c')->firstOrFail();

        // Load sections with their related fields
        $form->load(['sections.fields']);

        return view('appraisals.form', compact('form'));
    }
}
