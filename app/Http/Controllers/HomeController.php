<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Appraisal;
use App\Models\Attendance;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Scopes\EmployeeScope;
use App\Models\Training;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\User;
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

        $employee = auth()->user()->employee; // Assuming you have a relationship set up

        $appraisals = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->where('appraisal_drafts.is_submitted', true)
            ->count();

        $pendingAppraisals = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->where('appraisal_drafts.is_submitted', false)
            ->where(function ($query) {
                $query->where('appraisal_drafts.employee_id', auth()->user()->employee->employee_id);
            })
            ->count();



        //submitted appraisals to H.O.D
        $submittedAppraisalsBystaff = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->whereNull('appraisals.appraisal_request_status')
            ->where('appraisal_drafts.is_submitted', true)
            ->get();


        $submittedAppraisalsByHoD = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->where(function ($query) {
                $query->whereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'approved'])
                    ->orWhereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('employees')
                            ->join('users', 'users.id', '=', 'employees.user_id')
                            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                            ->whereColumn('employees.employee_id', 'appraisals.appraiser_id')
                            ->where('model_has_roles.model_type', User::class)
                            ->where('roles.name', 'HR')
                            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
                    });
            })
            ->whereNull("appraisals.appraisal_request_status->HR")
            ->where('appraisal_drafts.is_submitted', true)
            ->get();

        //submitte by all supervisors
        $submittedAppraisalsByallSupervisors = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereJsonContains('appraisal_request_status', [
                        'Head of Division' => 'approved',
                        'HR' => 'approved'
                    ])
                        ->whereNull("appraisals.appraisal_request_status->Executive Secretary");
                })
                    ->orWhereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('employees')
                            ->join('users', 'users.id', '=', 'employees.user_id')
                            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                            ->whereColumn('employees.employee_id', 'appraisals.employee_id')
                            ->where('model_has_roles.model_type', User::class)
                            ->where('roles.name', 'Head of Division');
                    })
                    ->orWhere('appraiser_id', auth()->user()->employee->employee_id);
            })
            ->where(function ($q) {
                $q->whereNull("appraisals.appraisal_request_status->Executive Secretary")
                    ->orWhereJsonDoesntContain('appraisal_request_status', [
                        'Executive Secretary' => 'approved',
                    ]);
            })
            ->where('appraisal_drafts.is_submitted', true)->get();


        $submittedAppraisalsByHR = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved'])
            ->where('appraisal_drafts.is_submitted', true)
            ->get();

        //get the Executive Secretary's account
        $executiveSecretary = User::role('Executive Secretary')->first();

        $employeeId = Employee::withoutGlobalScope(EmployeeScope::class)
            ->where('user_id', $executiveSecretary->id)
            ->value('employee_id');

        //complete appraisals
        $completeAppraisals = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved', 'Executive Secretary' => 'approved'])
            //or where the appraser is the Executive Secretary and has approved
            ->orWhere('appraiser_id', $employeeId)->whereJsonContains('appraisal_request_status', ['Executive Secretary' => 'approved'])

            ->where('appraisal_drafts.is_submitted', true);

        $ongoingAppraisals = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->where('appraisal_drafts.is_submitted', true)
            ->where(function ($query) {
                $query->where('appraisal_drafts.employee_id', auth()->user()->employee->employee_id);
            })
            ->get();

        // contracts
        $contracts = Contract::whereTodayOrAfter('end_date')->get();

        $runningContracts = Contract::where('end_date', '>=', Carbon::today())->count();

        $expiredContracts = Contract::where('end_date', '<', Carbon::today())->count();

        $leaveTypes = LeaveType::all()->keyBy('leave_type_id');



        $isAdmin = auth()->user()->isAdminOrSecretary;


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



        // Fetch leave requests where end date is greater than today
        $leaveRequests = Leave::where('end_date', '>', $today)->get();
        //number of employees
        $number_of_employees = Employee::count();
        $attendances = Attendance::whereDate('access_date_and_time', $today)->count();
        $available_leave = Leave::count();
        //count the number of clockins per hour
        $clockInCounts = DB::table('attendances')
            ->select(DB::raw('HOUR(access_time) as hour'), DB::raw('count(*) as count'))
            ->whereDate('access_date_and_time', $today)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        // Query to count clock-ins per hour for yesterday
        $yesterdayClockInCounts = DB::table('attendances')
            ->select(DB::raw('HOUR(access_time) as hour'), DB::raw('count(*) as count'))
            ->whereDate('access_date_and_time', $yesterday)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Query for late arrivals (e.g., after 9 AM)
        $lateArrivalCounts = DB::table('attendances')
            ->select(DB::raw('HOUR(access_time) as hour'), DB::raw('count(*) as count'))
            ->whereDate('access_date_and_time', $today)
            ->where('access_time', '>', Carbon::today()->setHour(9))
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
        $entries = Application::latest()->take(5)
            ->get();

        $appraisals = Appraisal::latest()
            ->take(5)
            ->get();


        //get the current leave requests
        $leaves = Leave::with('employee', 'leaveCategory')
            ->where('end_date', '>=', Carbon::today())
            ->where('user_id', auth()->user()->id)
            ->get();

        $totalLeaves = $leaves->count();
        $totalDays = $leaves->sum(function ($leave) {
            return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
        });
        $leavesPerCategory = $leaves->groupBy('leaveCategory.leave_type_name')->map->count();

        // Prepare leave approval progress data
        $leaveApprovalData = [];
        foreach ($leaves as $leave) {
            if ((($leave->leave_request_status != 'rejected') || ($leave->remainingLeaveDays() >= 0)) && (!$leave->is_cancelled)) {
                $progress = 0;
                $status = '';

                // Determine the approval status and progress
                if ($leave->leave_request_status === 'approved') {
                    $progress = 100;
                    $status = 'Approved';
                } elseif ($leave->leave_request_status === 'rejected') {
                    $progress = 0;
                    $status = 'Rejected';
                } else {
                    // Count stages based on status
                    if ($leave->leave_request_status["HR"] ?? "" === 'approved') {
                        $progress += 33;
                        $status = 'Awaiting HOD Approval';
                    }
                    if ($leave->leave_request_status["Head of Division"] ?? "" === 'approved') {
                        $progress += 33;
                        $status = 'Awaiting Executive Secretary Approval';
                    }
                    if ($leave->leave_request_status["Executive Secretary"] ?? "" === 'approved') {
                        $progress += 34;
                        $status = 'Your Leave request has been granted';
                    }
                }
                $leaveApprovalData[] = [
                    'leave' => $leave,
                    'daysRemaining' => $leave->remainingLeaveDays(),
                    'progress' => $progress,
                    'start_date' => $leave->start_date->format('Y-m-d'),
                    'end_date' => $leave->end_date->format('Y-m-d'),
                    'is_cancelled' => $leave->is_cancelled,
                    'status' => $status,
                    'hrStatus' => $leave->leave_request_status["HR"] ?? "" === 'approved' ? 'Approved' : 'Pending',
                    'hodStatus' => $leave->leave_request_status["Head of Division"] ?? "" === 'approved' ? 'Apprroved' : 'Pending',
                    'esStatus' => $leave->leave_request_status["Executive Secretary"] ?? "" === 'approved' ? 'Approved' : 'Pending',
                ];
            }
        }

        //birthdays
        $todayBirthdays = Employee::withoutGlobalScope(EmployeeScope::class)->whereMonth('date_of_birth', Carbon::today()->month)
            ->whereDay('date_of_birth', Carbon::today()->day)
            ->get();

        $user = User::find(auth()->id());
        $notifications = $user->unreadNotifications()->latest()->take(10)->get();

        return view('dashboard.index', compact('number_of_employees', 'submittedAppraisalsByallSupervisors','completeAppraisals', 'ongoingAppraisals', 'submittedAppraisalsBystaff', 'pendingAppraisals', 'submittedAppraisalsByHR', 'submittedAppraisalsByHoD', 'notifications', 'contracts', 'runningContracts', 'expiredContracts', 'attendances', 'available_leave', 'hours', 'todayCounts', 'yesterdayCounts', 'lateCounts', 'chartDataJson', 'leaveTypesJson', 'chartEmployeeDataJson', 'events', 'trainings', 'entries', 'appraisals', 'leaveApprovalData', 'totalLeaves', 'totalDays', 'todayBirthdays', 'isAdmin'));
    }

    public function agree()
    {
        // Store the agreement in the user's profile or session
        $user = auth()->user();
        $user->agreed_to_data_usage = true;
        $user->save();
        return redirect()->back()->with('success', 'Thank you for agreeing to data usage.');
    }
}
