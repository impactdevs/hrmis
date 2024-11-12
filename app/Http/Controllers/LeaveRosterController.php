<?php

namespace App\Http\Controllers;

use App\Models\LeaveRoster;
use Illuminate\Http\Request;

class LeaveRosterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the first day of the month and determine what day of the week it starts on
        $firstDayOfMonth = \Carbon\Carbon::createFromDate(date('Y'), date('m'), 1)->dayOfWeek;
        $daysInMonth = \Carbon\Carbon::createFromDate(date('Y'), date('m'), 1)->daysInMonth; // Total days in the month
        $day = 1; // Starting day of the month
        $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $weeksInMonth = ceil($daysInMonth / 7);
        $year = date('Y');
        $month = date('m');
        //get Departments
        $departments = \App\Models\Department::with('employees', 'employees.leaveRoster', 'employees')->get();
        return view('leave-roster.index', compact('firstDayOfMonth', 'daysInMonth', 'day', 'weekdays', 'weeksInMonth', 'departments', 'year', 'month'));
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
        //
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

    public function getcalender(Request $request)
    {
        $user = auth()->user();
        $employee_id = $user->employee->employee_id;
        $leaveRoster = LeaveRoster::where('employee_id', $employee_id)->first();

        // Initialize an array to store the total leave days for each month
        $leaveDaysPerMonth = array_fill(1, 12, 0); // Initialize with 0 for each month

        // Loop through the months and calculate the total leave days for each month
        foreach ($leaveRoster->months as $year => $months) {
            foreach ($months as $month => $days) {
                // Add the leave days for this month to the total (days is an array)
                $leaveDaysPerMonth[$month] += $days; // Count the number of leave days for this month
            }
        }

        // Return the leave days data
        return response()->json([
            'leaveDaysPerMonth' => $leaveDaysPerMonth // Include total leave days for each month
        ]);
    }





}
