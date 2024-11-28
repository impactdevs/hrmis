<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\Employee;
use App\Models\Entry;
use App\Models\Scopes\EmployeeScope;
use App\Models\User;
use App\Notifications\AppraisalApplication;
use App\Notifications\AppraisalApproval;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Notification;

class AppraisalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appraisals = Appraisal::with('employee')->latest()->paginate();
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

        //get the head of department for the logged in user
        $user = auth()->user();
        $headOfDepartment = $user->employee->department->department_head;
        $email = User::where('id', $headOfDepartment)->first();

        //send an email to the head of department
        Notification::send($email, new AppraisalApplication($appraisal, $user->employee->first_name, $user->employee->last_name));
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

    public function approveOrReject(Request $request)
    {
        try {
            $approval_status = request()->input('status');
            $appraisal = Appraisal::find(request()->input('appraisals_id'));
            //logeed in employee
            $loggedInEmployee = auth()->user()->employee;
            $employee = Employee::withoutGlobalScope(EmployeeScope::class)->where('employee_id', $appraisal->employee_id)->first();
            $appraisal->approval_status = $approval_status;
            $appraisal->save();
            $employeeUser = $employee->user_id;
            $email = User::where('id', $employeeUser)->first();

            $message = '';

            if ($approval_status == 'approve') {
                //send an email to the head of department
                Notification::send($email, new AppraisalApproval($appraisal, $loggedInEmployee ->first_name, $loggedInEmployee ->last_name));
                $message = 'Appraisal request approved successfully.';
            }
            if ($approval_status == 'reject') {
                $appraisal->rejection_reason = request()->input('reason');
                Notification::send($email, new AppraisalApproval($appraisal, $loggedInEmployee ->first_name, $loggedInEmployee ->last_name));
                $message = 'Appraisal request rejected successfully.';
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
