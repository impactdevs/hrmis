<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Appraisal;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Entry;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Training;
use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $tomorrow = Carbon::tomorrow();
        $hours = [];
        $todayCounts = [];
        $yesterdayCounts = [];
        $lateCounts = [];
        $allocatedLeaveDays = [];
        $leaveTypes = LeaveType::all()->keyBy('leave_type_id');

        //events and trainings
        $events = Event::where(function ($query) use ($today, $tomorrow) {
            $query->whereBetween('event_start_date', [$today, $tomorrow])
                ->orWhere(function ($q) {
                    $q->where('event_start_date', '<', now())
                        ->where('event_end_date', '>', now());
                });
        })->get();

        $trainings = Training::where(function ($query) use ($today, $tomorrow) {
            $query->whereBetween('training_start_date', [$today, $tomorrow])
                ->orWhere(function ($q) {
                    $q->where('training_start_date', '<', now())
                        ->where('training_end_date', '>', now());
                });
        })->get();



        // Fetch leave requests
        $leaveRequests = Leave::all();
        //number of employees
        $number_of_employees = Employee::count();
        $attendances = Attendance::whereDate('attendance_date', $today)->count();
        $available_leave = Leave::count();
        //count the number of clockins per hour
        $clockInCounts = DB::table('attendances')
            ->select(DB::raw('HOUR(clock_in) as hour'), DB::raw('count(*) as count'))
            ->whereDate('attendance_date', $today)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        // Query to count clock-ins per hour for yesterday
        $yesterdayClockInCounts = DB::table('attendances')
            ->select(DB::raw('HOUR(clock_in) as hour'), DB::raw('count(*) as count'))
            ->whereDate('attendance_date', $yesterday)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Query for late arrivals (e.g., after 9 AM)
        $lateArrivalCounts = DB::table('attendances')
            ->select(DB::raw('HOUR(clock_in) as hour'), DB::raw('count(*) as count'))
            ->whereDate('attendance_date', $today)
            ->where('clock_in', '>', Carbon::today()->setHour(9))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        // Initialize counts for each hour (0-23)
        for ($i = 0; $i < 24; $i++) {
            $hours[] = Carbon::today()->setHour($i)->toISOString();
            $todayCounts[] = 0;
            $yesterdayCounts[] = 0;
            $lateCounts[] = 0;
        }

        // Populate today's counts
        foreach ($clockInCounts as $record) {
            $todayCounts[$record->hour] = $record->count;
        }

        // Populate yesterday's counts
        foreach ($yesterdayClockInCounts as $record) {
            $yesterdayCounts[$record->hour] = $record->count;
        }

        // Populate late arrival counts
        foreach ($lateArrivalCounts as $record) {
            $lateCounts[$record->hour] = $record->count;
        }

        // Assuming you already have $leaveRequests
        $allocatedLeaveDays = [];

        foreach ($leaveRequests as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            $leaveTypeId = $leave->leave_type_id;

            $daysAllocated = $startDate->diffInDays($endDate) + 1;

            if (!isset($allocatedLeaveDays[$leaveTypeId])) {
                $allocatedLeaveDays[$leaveTypeId] = 0;
            }
            $allocatedLeaveDays[$leaveTypeId] += $daysAllocated;
        }

        // Prepare data for ECharts
        $chartData = [];
        foreach ($leaveTypes as $leaveType) {
            $chartData[] = $allocatedLeaveDays[$leaveType->leave_type_id] ?? 0;
        }

        // Convert to JSON for JavaScript
        $chartDataJson = json_encode($chartData);
        $leaveTypesJson = json_encode($leaveTypes->pluck('leave_type_name')->toArray());


        // Get the number of employees per department with department names
        $employeeCounts = DB::table('employees')
            ->join('departments', 'employees.department_id', '=', 'departments.department_id')
            ->select('departments.department_name', DB::raw('count(*) as total'))
            ->groupBy('departments.department_name')
            ->get();

        // Prepare data for ECharts
        $chartEmployeeData = [];
        foreach ($employeeCounts as $count) {
            $chartEmployeeData[] = [
                'value' => $count->total,
                'name' => $count->department_name, // Use department name here
            ];
        }

        // Convert to JSON for JavaScript
        $chartEmployeeDataJson = json_encode($chartEmployeeData);

        //applications
        $entries = Application::with('entry', 'job')->whereDate('created_at', $today)
            ->latest()
            ->get();

        $appraisals = Appraisal::with('entry')->whereDate('created_at', $today)
            ->latest()
            ->get();


        return view('dashboard.index', compact('number_of_employees', 'attendances', 'available_leave', 'hours', 'todayCounts', 'yesterdayCounts', 'lateCounts', 'chartDataJson', 'leaveTypesJson', 'chartEmployeeDataJson', 'events', 'trainings', 'entries', 'appraisals'));
    }
}