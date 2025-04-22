<?php

namespace App\Http\Controllers;

use App\Events\RosterUpdate;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRoster;
use App\Models\LeaveType;
use App\Models\PublicHoliday;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRosterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->hasRole('HR')) {
            $user_id = auth()->user()->id;
            $leaveTypes = LeaveType::pluck('leave_type_name', 'leave_type_id')->toArray();
            $existingValuesArray = [];
            $users = User::pluck('name', 'id')->toArray();
            $public_holidays = PublicHoliday::pluck('holiday_date')->toArray();

            //departments
            $departments = Department::pluck('department_name', 'department_id')->toArray();

            return view('leave-roster.index', compact('leaveTypes', 'user_id', 'existingValuesArray', 'users', 'departments', 'public_holidays'));
        } else {
            $public_holidays = PublicHoliday::pluck('holiday_date')->toArray();
            $users = User::whereHas('employee')->with('employee')->get();
            return view('leave-roster.tabular', compact('users', 'public_holidays'));
        }
    }

    public function getLeaveRoster(Request $request)
    {
        return view('leave-roster.tabular');
    }


    public function getLeaveRosterData(Request $request)
    {
        // Get search parameter from the request
        $search = $request->get('search', '');

        // Get pagination parameters (offset and limit)
        $offset = $request->get('offset', 0);  // Number of records to skip
        $limit = $request->get('limit', 100);   // Number of records per page

        // Query the Employee model with search functionality and manual offset/limit
        $employees = Employee::with('leaveRoster')
            ->where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orderBy('first_name', 'asc')
            ->skip($offset)  // Skip the number of records specified by offset
            ->take($limit)   // Take the number of records specified by limit
            ->get();  // Execute the query to fetch the employees

        // Add numeric IDs and calculate total leave days and balance for each employee
        $startIndex = $offset + 1;

        $employees->transform(function ($employee, $index) use ($startIndex) {
            $totalLeaveDays = $employee->totalLeaveDays();
            $totalLeaveRosterDays = $employee->overallRosterDays();

            $balance = $totalLeaveRosterDays - $totalLeaveDays;

            $employee->numeric_id = $startIndex + $index; // Add sequential numeric ID
            $employee->total_leave_days = $totalLeaveDays;
            $employee->total_leave_roster_days = $employee->entitled_leave_days;
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


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validate, the request, the start date should be less than the end date
        $request->validate([
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date',
            'leave_title' => 'required|string',
            'user_id'=> 'nullable|exists:users,id'
        ]);

        if (!auth()->user()->hasRole('HR')) {
            $user = auth()->user();
        } else {
            $user = User::find($request->user_id);
        }

        $employee_id = $user->employee->employee_id;
        $leaveRosterAdded = LeaveRoster::create([
            'employee_id' => $employee_id,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'leave_title' => $request->input('leave_title'),
        ]);
        //load employee
        $leaveRosterAdded->load('employee');

        if ($leaveRosterAdded) {
            $entitledDays = $user->employee->entitled_leave_days ?? 0;
            $scheduledDays = $user->employee->overallRosterDays();
            $leaveRosterAdded['scheduled_days'] = $scheduledDays;
            RosterUpdate::dispatch($employee_id, $entitledDays, $scheduledDays);
            return response()->json(['success' => true, 'message' => 'Leave Roster added successfully', 'data' => $leaveRosterAdded]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to add Leave Roster']);
    }

    public function leaveRosterCalendarData(Request $request)
    {
        $department = $request->input('department', 'all');

        // Start with the query to get the leave roster
        $leaveRosterQuery = LeaveRoster::with('employee', 'leave'); // Eager load employee relationship
        // Filter by department if selected
        if ($department !== 'all') {
            $employeeIds = Employee::where('department_id', $department)->pluck('employee_id');
            $leaveRosterQuery->whereIn('employee_id', $employeeIds);
        }

        // Retrieve the filtered leave roster
        $leaveRoster = $leaveRosterQuery->get()->map(function ($leave, $index) { // $index is the numeric ID
            return [
                'numeric_id' => $index + 1, // Add a numeric ID starting from 1
                'leave_roster_id' => $leave->leave_roster_id,
                'title' => $leave->leave_title,
                'start' => $leave->start_date->toIso8601String(),
                'end' => $leave->end_date->toIso8601String(),
                'staffId' => $leave->employee->staff_id ?? null,
                'first_name' => $leave->employee->first_name ?? null,
                'last_name' => $leave->employee->last_name ?? null,
                'leave' => $leave->leave
            ];
        });
        // Return the filtered leave roster data
        return response()->json(['success' => true, 'data' => $leaveRoster]);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $leaveRoster = LeaveRoster::findOrFail($id);  // Find the event by its leave_roster_id

        //only update what was modified
        $leaveRoster->update($request->all());

        return response()->json(['success' => true, 'data' => $leaveRoster]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $leaveRoster = LeaveRoster::findOrFail($id);  // Find the event by its leave_roster_id
        $leaveRoster->delete();

        return response()->json(['success' => true]);
    }
}
