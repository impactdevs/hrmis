<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRoster;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveRosterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $leaveTypes = LeaveType::pluck('leave_type_name', 'leave_type_id')->toArray();
        $existingValuesArray = [];
        $users = User::pluck('name', 'id')->toArray();

        //departments
        $departments = Department::pluck('department_name', 'department_id')->toArray();

        return view('leave-roster.index', compact('leaveTypes', 'user_id', 'existingValuesArray', 'users', 'departments'));
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
        $employee_id = auth()->user()->employee->employee_id;
        $leaveRosterAdded = LeaveRoster::create([
                'employee_id' => $employee_id,
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'leave_title' => $request->input('leave_title'),
        ]);

        //load employee
        $leaveRosterAdded->load('employee');

        if ($leaveRosterAdded) {
            return response()->json(['success' => true, 'message' => 'Leave Roster added successfully', 'data' => $leaveRosterAdded]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to add Leave Roster']);
    }

    /*
     *get leave roster calendar data
     */
    public function leaveRosterCalendarData(Request $request)
    {

        $employee_id = auth()->user()->employee->employee_id;

        // Get the filters from the request, defaulting to 'all'
        $approvalStatus = $request->input('approval_status', 'all');
        $department = $request->input('department', 'all');

        // Start with the query to get the leave roster
        $leaveRosterQuery = LeaveRoster::with('employee'); // Eager load employee relationship

        // Filter by booking_approval_status status (Approved, Pending, Rejected)
        if ($approvalStatus !== 'all') {
            // Adjusting the approval status filtering logic based on the 'booking_approval_status' field
            if ($approvalStatus === 'Approved') {
                $leaveRosterQuery->where('booking_approval_status', 'Approved');
            } elseif ($approvalStatus === 'Rejected') {
                $leaveRosterQuery->where('booking_approval_status', 'Rejected');
            } elseif ($approvalStatus === 'Pending') {
                $leaveRosterQuery->where('booking_approval_status', 'Pending');
            }
        }

        // Filter by department if selected
        if ($department !== 'all') {
            $employeeIds = Employee::where('department_id', $department)->pluck('employee_id');
            $leaveRosterQuery->whereIn('employee_id', $employeeIds);
        }

        // Retrieve the filtered leave roster
        $leaveRoster = $leaveRosterQuery->get()->map(function ($leave) {
            return [
                'leave_roster_id' => $leave->leave_roster_id,
                'title' => $leave->leave_title,
                'start' => $leave->start_date->toIso8601String(),
                'end' => $leave->end_date->toIso8601String(),
                'bookingApprovalRequest' => $leave->booking_approval_status,  // This will show Approved, Pending, or Rejected
                'staffId' => $leave->employee->staff_id ?? null,
                'first_name' => $leave->employee->first_name ?? null,
                'last_name' => $leave->employee->last_name ?? null,
                'isApproved' => $leave->isApproved(),
                // Add additional employee or leave-related data if necessary
            ];
        });

        // Return the filtered leave roster data
        return response()->json(['success' => true, 'data' => $leaveRoster]);
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


    public function saveLeaveRosterData()
    {
        try {
            $data = request()->validate([
                'month' => 'required|integer|min:1|max:12',
                'employee_id' => 'required|exists:employees,employee_id',
                'year' => 'integer|required',  // Ensure year is required
                'number_of_leave_days' => 'required|integer',
            ]);

            // Find the leave roster record for the employee
            $leaveRoster = LeaveRoster::where('employee_id', $data['employee_id'])->first();

            //you have to first get this as a variable
            $months = $leaveRoster->months;
            // Ensure the month exists in the year, if not initialize it

            $months[$data['year']] = $months[$data['year']] ?? [];

            $months[$data['year']][$data['month']] = $data['number_of_leave_days'];

            $leaveRoster->months = $months;
            // Save the updated leave roster
            $leaveRoster->save();

            return response()->json(['success' => true, 'message' => 'Leave data saved successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'failed to save leave data', 'message' => $e->getMessage()]);
        }
    }
}
