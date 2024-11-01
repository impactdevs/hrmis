<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveApplied;
use App\Notifications\LeaveApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaves = Leave::with('employee', 'leaveCategory')->get();
        $leaves = Leave::all();
        $totalLeaves = $leaves->count();
        $totalDays = $leaves->sum(function ($leave) {
            return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
        });
        $leavesPerCategory = $leaves->groupBy('leaveCategory.leave_type_name')->map->count();

        $users = User::pluck('name', 'id')->toArray();

        return view('leaves.index', compact('leaves', 'totalLeaves', 'totalDays', 'leavesPerCategory', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //get the logged in user email
        $user_id = auth()->user()->id;
        $leaveTypes = LeaveType::pluck('leave_type_name', 'leave_type_id')->toArray();
        $existingValuesArray = [];
        $users = User::pluck('name', 'id')->toArray();
        return view('leaves.create', compact('leaveTypes', 'user_id', 'existingValuesArray', 'users'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Create the leave record
        $leaveCreated = Leave::create($request->all());

        if ($leaveCreated) {
            // Get users with the superadmin role
            $users = User::role('Super Admin')->get();

            // Send notifications to those users
            Notification::send($users, new LeaveApplied($leaveCreated));
        }

        return redirect()->route('leaves.index')->with('success', 'Leave created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Leave $leave)
    {
        return view('leaves.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leave $leave)
    {
        $leaveTypes = LeaveType::pluck('leave_type_name', 'leave_type_id')->toArray();

        return view('leaves.edit', compact('leave', 'leaveTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leave $leave)
    {
        $leave->update($request->all());

        return redirect()->route('leaves.index')->with('success', 'Leave updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leave $leave)
    {
        $leave->delete();

        return redirect()->route('leaves.index')->with('success', 'Leave deleted successfully.');
    }


    public function approveOrReject(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Update leave request based on the user's role and the input status
        if ($user->hasRole('Super Admin')) {
            if ($request->input('status') === 'approved') {
                $leave->leave_request_status = 'Super Admin';
                $leave->rejection_reason = null; // Clear reason if approved
            } else {
                $leave->leave_request_status = 'rejected';
                $leave->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Head of Division')) {
            if ($request->input('status') === 'approved') {
                $leave->leave_request_status = 'Head of Division';
                $leave->rejection_reason = null; // Clear reason if approved
            } else {
                $leave->leave_request_status = 'rejected';
                $leave->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                $leave->leave_request_status = 'approved';
                $leave->rejection_reason = null; // Clear reason if approved
            } else {
                $leave->leave_request_status = 'rejected';
                $leave->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Save the updated leave status
        $leave->save();

        // Send notification
        $leaveRequester = User::find($leave->user_id); // Get the user who requested the leave
        $approver = User::find(auth()->user()->id);
        Notification::send($leaveRequester, new LeaveApproval($leave, $approver));// Notify with the approver

        return response()->json(['message' => 'Leave application updated successfully.', 'status' => $leave->leave_request_status]);
    }



}
