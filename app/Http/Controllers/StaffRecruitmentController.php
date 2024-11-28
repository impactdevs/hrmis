<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\StaffRecruitment;
use App\Http\Requests\StoreStaffRecruitmentRequest;  // The request validation class
use App\Models\User;
use App\Notifications\StaffRecruitmentApplication;
use App\Notifications\StaffRecruitmentApproval;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
class StaffRecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rectrutmentRequests = StaffRecruitment::with('department')->paginate();
        return view('staff-recruitment.index', compact('rectrutmentRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get departments with department_id as key and department_name as the value
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        return view('staff-recruitment.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRecruitmentRequest $request)
    {

        // Create a new StaffRecruitment entry in the database
        $staffRecruitment = StaffRecruitment::create([
            'position' => $request->input('position'),
            'department_id' => $request->input('department_id'),
            'number_of_staff' => $request->input('number_of_staff'),
            'date_of_recruitment' => $request->input('date_of_recruitment'),
            'sourcing_method' => $request->input('sourcing_method'),
            'employment_basis' => $request->input('employment_basis'),
            'justification' => $request->input('justification'),
            'funding_budget' => $request->input('funding_budget'),
            'user_id' => auth()->user()->id
        ]);

        //get HR users using spatie
        $users = User::role('HR')->get();

        Notification::send($users, new StaffRecruitmentApplication($staffRecruitment));

        // Redirect back with a success message
        return redirect()->route('recruitments.index') // Adjust to the appropriate route
            ->with('success', 'Staff recruitment request has been submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffRecruitment $recruitment)
    {
        $recruitment->load('department');
        return view('staff-recruitment.show', compact('recruitment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffRecruitment $recruitment)
    {
        $staffRecruitment = $recruitment;
        // Get departments with department_id as key and department_name as the value
        $departments = Department::pluck('department_name', 'department_id')->toArray();

        return view('staff-recruitment.edit', compact('staffRecruitment', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaffRecruitment $recruitment)
    {
        $recruitment->update($request->all());

        return redirect()->route('recruitments.index')->with('success', 'Recruitment Request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffRecruitment $recruitment)
    {
        try {
            // Delete the employee record
            $recruitment->delete();

            // Redirect to the employees index with a success message
            return redirect()->route('recruitments.index')->with('success', 'Recruitment Request Deleted');
        } catch (Exception $exception) {
            // Log the error for debugging
            \Log::error('Error deleting recruitment request: ' . $exception->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Problem Deleting the Recruitment Request');
        }
    }

    public function approveOrReject(Request $request, StaffRecruitment $recruitment)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Retrieve current leave_request_status (it will be an array due to casting)
        $recruitmentRequestStatus = $recruitment->approval_status ?: []; // Default to an empty array if null

        // Update leave request based on the user's role and the input status
        if ($user->hasRole('HR')) {
            if ($request->input('status') === 'approved') {
                // Set HR status to approved
                $recruitmentRequestStatus['HR'] = 'approved';
                $recruitment->rejection_reason = null; // Clear reason if approved
            } else {
                // Set HR status to rejected
                $recruitmentRequestStatus['HR'] = 'rejected';
                $recruitment->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Head of Division')) {
            if ($request->input('status') === 'approved') {
                // Set Head of Division status to approved
                $recruitmentRequestStatus['Head of Division'] = 'approved';
                $recruitment->rejection_reason = null; // Clear reason if approved
            } else {
                // Set Head of Division status to rejected
                $recruitmentRequestStatus['Head of Division'] = 'rejected';
                $recruitment->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                // Set leave status as approved for Executive Secretary
                $recruitmentRequestStatus['Executive Secretary'] = 'approved';
                $recruitment->rejection_reason = null; // Clear reason if approved
            } else {
                // Set rejection status
                $recruitmentRequestStatus['Executive Secretary'] = 'rejected';
                $recruitment->rejection_reason = $request->input('reason'); // Store rejection reason
            }

            // Send notification
            $leaveRequester = User::find($recruitment->user_id); // Get the user who requested the leave
            $approver = User::find(auth()->user()->id);
            Notification::send($leaveRequester, new StaffRecruitmentApproval($recruitment, $approver));

        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // Save the updated leave_request_status
        $recruitment->approval_status = $recruitmentRequestStatus;
        $recruitment->save();

        // Save the updated leave status
        $recruitment->save();

        return response()->json(['message' => 'Recruitment application updated successfully.', 'status' => $recruitment->approval_status]);
    }
}
