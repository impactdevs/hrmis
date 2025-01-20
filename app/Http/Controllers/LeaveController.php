<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveRoster;
use App\Models\LeaveType;
use App\Models\PublicHoliday;
use App\Models\User;
use App\Notifications\LeaveApplied;
use App\Notifications\LeaveApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaves = Leave::with('employee', 'leaveCategory')->get();
        $totalLeaves = $leaves->count();
        //get all the roles in the system except Staff
        $roles = Role::whereNotIn('name', ['Assistant Executive Secretary', 'Staff'])->pluck('name')->toArray();

        $totalDays = $leaves->sum(function ($leave) {
            return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
        });
        $leavesPerCategory = $leaves->groupBy('leaveCategory.leave_type_name')->map->count();

        $users = User::pluck('name', 'id')->toArray();

        //get the number leave days allocated to the employee
        $user = auth()->user()->id;
        //current year
        $currentYear = Carbon::now()->year;
        $employee = Employee::where('user_id', $user)->first();
        $totalLeaveDaysAllocated = $employee->totalLeaveRosterDays();
        $useDays = $employee->totalLeaveDays();
        //departments
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        return view('leaves.index', compact('leaves', 'totalLeaves', 'totalDays', 'leavesPerCategory', 'users', 'totalLeaveDaysAllocated', 'useDays', 'departments', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //get the logged in user email
        $user_id = auth()->user()->id;
        $leaveTypes = LeaveType::pluck('leave_type_name', 'leave_type_id')->toArray();
        $holidays = PublicHoliday::pluck('holiday_date')->toArray();
        $existingValuesArray = [];
        $users = User::pluck('name', 'id')->toArray();
        return view('leaves.create', compact('leaveTypes', 'user_id', 'existingValuesArray', 'users', 'holidays'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function applyForLeave(LeaveRoster $leaveRoster)
    {
        //get the logged in user email
        $user_id = auth()->user()->id;
        $leaveTypes = LeaveType::pluck('leave_type_name', 'leave_type_id')->toArray();
        $existingValuesArray = [];
        $users = User::pluck('name', 'id')->toArray();
        return view('leaves.create', compact('leaveTypes', 'user_id', 'existingValuesArray', 'users', 'leaveRoster'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $requestData = $request->all();


        // Check if the handover_note_file is present in the request
        if ($request->hasFile('handover_note_file')) {
            // Store the file and get the file path
            $filePath = $request->file('handover_note_file')->store('handover_notes', 'public');

            // Add the file path to the validated data
            $requestData['handover_note_file'] = $filePath;
        }
        // Create the leave record
        $leaveCreated = Leave::create($requestData);

        if ($leaveCreated) {
            //notify people who will stand in for the person on leave, $leaveCreated->my_work_will_be_done_by is a string separated by commas
            $myWorkWillBeDoneBy = explode(',', $leaveCreated->my_work_will_be_done_by['users']);
            $doneBy = User::whereIn('id', $myWorkWillBeDoneBy)->get();
            // Get users with the superadmin role
            $users = User::role('HR')->get();

            //HEAD OF DEPARTMENT
            $user = auth()->user();
            $headOfDepartment = $user->employee->department->department_head;
            $hod = User::where('id', $headOfDepartment)->first();

            //add hod to users array
            $users->push($hod);
            $users->push($doneBy);
            // Send notifications to those users
            Notification::send($users, new LeaveApplied($leaveCreated));
        }

        return redirect()->route('leaves.index')->with('success', 'Leave submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Leave $leaf)
    {
        return view('leaves.show', compact('leaf'));
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

        // Retrieve current leave_request_status (it will be an array due to casting)
        $leaveRequestStatus = $leave->leave_request_status ?: []; // Default to an empty array if null

        // Update leave request based on the user's role and the input status
        if ($user->hasRole('HR')) {
            if ($request->input('status') === 'approved') {
                // Set HR status to approved
                $leaveRequestStatus['HR'] = 'approved';
                $leave->rejection_reason = null; // Clear reason if approved
            } else {
                // Set HR status to rejected
                $leaveRequestStatus['HR'] = 'rejected';
                $leave->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Head of Division')) {
            if ($request->input('status') === 'approved') {
                // Set Head of Division status to approved
                $leaveRequestStatus['Head of Division'] = 'approved';
                $leave->rejection_reason = null; // Clear reason if approved
            } else {
                // Set Head of Division status to rejected
                $leaveRequestStatus['Head of Division'] = 'rejected';
                $leave->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                // Set leave status as approved for Executive Secretary
                $leaveRequestStatus['Executive Secretary'] = 'approved';
                $leave->rejection_reason = null; // Clear reason if approved
            } else {
                // Set rejection status
                $leaveRequestStatus['Executive Secretary'] = 'rejected';
                $leave->rejection_reason = $request->input('reason'); // Store rejection reason
            }

            // Send notification
            $leaveRequester = User::find($leave->user_id); // Get the user who requested the leave
            $approver = User::where('id', auth()->user()->id)->first();
            Notification::send($leaveRequester, new LeaveApproval($leave, $approver)); // Notify with the approver
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Save the updated leave_request_status
        $leave->leave_request_status = $leaveRequestStatus;
        $leave->save();

        return response()->json(['message' => 'Leave application updated successfully.', 'status' => $leave->leave_request_status]);
    }

    public function leaveManagement()
    {
        //get all employees
        $employees = Employee::latest()->paginate();
        return view('leaves.leave-management', compact('employees'));
    }
    public function getLeaveManagementData(Request $request)
    {
        // Get search parameter from the request
        $search = $request->get('search', '');

        // Get pagination parameters (offset and limit)
        $offset = $request->get('offset', 0);  // Number of records to skip
        $limit = $request->get('limit', 10);   // Number of records per page

        // Query the Employee model with search functionality and manual offset/limit
        $employees = Employee::where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orderBy('first_name', 'asc')
            ->skip($offset)  // Skip the number of records specified by offset
            ->take($limit)   // Take the number of records specified by limit
            ->get();  // Execute the query to fetch the employees

        // Add numeric IDs and calculate total leave days and balance for each employee
        $startIndex = $offset + 1;

        $employees->transform(function ($employee, $index) use ($startIndex) {
            $totalLeaveDays = $employee->totalLeaveDays();
            $totalLeaveRosterDays = $employee->entitled_leave_days;

            $balance = $totalLeaveRosterDays - $totalLeaveDays;

            $employee->numeric_id = $startIndex + $index; // Add sequential numeric ID
            $employee->total_leave_days = $totalLeaveDays;
            $employee->total_leave_roster_days = $totalLeaveRosterDays;
            $employee->leave_balance = $balance;

            return $employee;
        });

        // Get the total number of records (for pagination)
        $total = Employee::with('leaveRoster')
            ->where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->count();  // Count the total number of records matching the search

        // Return the response in the format expected by the table
        return response()->json([
            'total' => $total,   // Total number of records (for pagination)
            'rows' => $employees, // Records for the current page
        ]);
    }

    public function leaveData(Request $request)
    {
        $department = $request->input('department', 'all');

        // Query to get the leave roster with employee and leave relationships
        $leaveRosterQuery = LeaveRoster::with(['employee', 'leave', 'leave.leaveCategory'])
        ->whereHas('leave', function($query) {
            $query->where('end_date', '>=', now());
        });


        // Filter by department if selected
        if ($department !== 'all') {
            $employeeIds = Employee::where('department_id', $department)->pluck('employee_id');
            $leaveRosterQuery->whereIn('employee_id', $employeeIds);
        }

        // Retrieve the filtered leave roster
        $leaveRoster = $leaveRosterQuery->get()->map(function ($leave, $index) {
            return [
                'numeric_id' => $index + 1,
                'leave_roster_id' => $leave->leave_roster_id,
                'title' => $leave->leave_title,
                'start' => $leave->start_date->toIso8601String(),
                'end' => $leave->end_date->toIso8601String(),
                'staffId' => $leave->employee->staff_id ?? null,
                'first_name' => $leave->employee->first_name ?? null,
                'last_name' => $leave->employee->last_name ?? null,
                'leave' => $leave->leave,
                // Add duration by calling the durationForLeave method
                'duration' => $leave->durationForLeave() // This will return the duration excluding weekends and holidays
            ];
        });

        // Query to find leaves that are not in the leave roster
        $orphanedLeaves = Leave::whereNull('leave_roster_id')
            ->with('employee')
            ->get()
            ->map(function ($leave, $index) use ($leaveRoster) {
                $indexOffset = $leaveRoster->count(); // Continue numeric ID from leaveRoster
                return [
                    'numeric_id' => $indexOffset + $index + 1,
                    'leave_roster_id' => null, // Not in leave roster
                    'title' => $leave->leave_title,
                    'start' => $leave->start_date->toIso8601String(),
                    'end' => $leave->end_date->toIso8601String(),
                    'staffId' => $leave->employee->staff_id ?? null,
                    'first_name' => $leave->employee->first_name ?? null,
                    'last_name' => $leave->employee->last_name ?? null,
                    'leave' => $leave,
                    // Add duration for orphaned leaves as well
                    'duration' => $leave->durationForLeave()
                ];
            });

        // Combine the leaveRoster and orphanedLeaves
        $combinedLeaves = $leaveRoster->merge($orphanedLeaves);

        // Return the combined leave data
        return response()->json(['success' => true, 'data' => $combinedLeaves]);
    }


}
