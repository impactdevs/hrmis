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
use App\Services\LeaveService;
use App\Models\LeaveHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use AuthorizesRequests;
    public function index()
    {
        // Base query - use a query builder instead of immediate get()
        $leavesQuery = Leave::with('employee', 'leaveCategory');

        // Check if filter parameter is present for approved current leaves
        if (request()->has('filter') && request('filter') == 'approved_current') {
            $leavesQuery->where('is_cancelled', false)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->whereJsonContains('leave_request_status->Executive Secretary', 'approved')
                ->count();
        }

        // Get the leaves based on the query
        $leaves = $leavesQuery->get();

        // Temporary workaround: show user's leaves without global scope if they can't see any
        if ($leaves->isEmpty() && auth()->user()) {
            $userLeavesCount = Leave::withoutGlobalScopes()->where('user_id', auth()->id())->count();
            if ($userLeavesCount > 0) {
                $userLeavesQuery = Leave::withoutGlobalScopes()
                    ->with('employee', 'leaveCategory')
                    ->where('user_id', auth()->id());

                // Apply the same filter to user's leaves if needed
                if (request()->has('filter') && request('filter') == 'approved_current') {
                    $userLeavesQuery->where('is_cancelled', false)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->where(function ($q) {
                            $q->where('leave_request_status', 'like', '%"Executive Secretary":"approved"%')
                                ->orWhere('leave_request_status', 'like', '%Executive Secretary%approved%');
                        });
                }

                $leaves = $userLeavesQuery->get();
            }
        }

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

        // Handle case where user doesn't have an employee record
        if ($employee) {
            $totalLeaveDaysAllocated = $employee->totalLeaveRosterDays();
            $useDays = $employee->totalLeaveDays();
        } else {
            $totalLeaveDaysAllocated = 0;
            $useDays = 0;
        }
        //departments
        $departments = Department::pluck('department_name', 'department_id')->toArray();

        // Pass filter status to view
        $activeFilter = request()->has('filter') ? request('filter') : null;

        return view('leaves.index', compact('leaves', 'totalLeaves', 'totalDays', 'leavesPerCategory', 'users', 'totalLeaveDaysAllocated', 'useDays', 'departments', 'roles', 'activeFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->employee->department) {
            return back()->with("error", "No department head found. Contact admin.");
        }
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
        $holidays = PublicHoliday::pluck('holiday_date')->toArray();
        $existingValuesArray = [];
        $users = User::pluck('name', 'id')->toArray();
        return view('leaves.create', compact('leaveTypes', 'user_id', 'existingValuesArray', 'users', 'leaveRoster', 'holidays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, LeaveService $leaveService)
    {
        $requestData = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,leave_type_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'handover_note' => 'required|string|max:5000',
            'handover_note_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'my_work_will_be_done_by' => 'required|array',
            'leave_address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'other_contact_details' => 'nullable|string',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Validate maternity leave restrictions
        $maternityError = $leaveService->validateMaternityLeave(
            $requestData['leave_type_id'],
            $user->id,
            $requestData['start_date']
        );

        if ($maternityError) {
            return back()
                ->withErrors(['leave_type_id' => $maternityError])
                ->withInput();
        }

        //Validate paternity leave restrictions
        $paternityError = $leaveService->validatePaternityLeave(
            $requestData['leave_type_id'],
            $user->id,
            $requestData['start_date']
        );

        if ($paternityError) {
            return back()
                ->withErrors(['leave_type_id' => $paternityError])
                ->withInput();
        }

        // Validate handover note requirements
        $handoverError = $leaveService->validateHandoverNote(
            $requestData['handover_note'],
            $request->hasFile('handover_note_file')
        );

        if ($handoverError) {
            return back()
                ->withErrors(['handover_note_file' => $handoverError])
                ->withInput();
        }

        //Handle file upload
        if ($request->hasFile('handover_note_file')) {
            // Store the file in storage/app/public/handover_notes/
            $path = $request->file('handover_note_file')->store('handover_notes', 'public');
            \Log::info('Handover file uploaded', [
                'original_name' => $request->file('handover_note_file')->getClientOriginalName(),
                'stored_path' => $path,
            ]);
            $requestData['handover_note_file'] = $path;
        }

        // Create the leave request with history tracking
        $leave = $leaveService->createLeaveRequest($requestData, $user->id);

        // Send notifications
        $leaveService->sendNotifications($leave);

        return redirect()->route('leaves.index')->with('success', 'Leave submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Leave $leave)
    {
        $users = User::pluck('name', 'id')->toArray() ?? [];

        // Keep the options separate for later use if needed
        $options = [
            'users' => $users,
        ];

        return view('leaves.show', compact('leave', 'options'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leave $leave)
    {
        $leaveTypes = LeaveType::pluck('leave_type_name', 'leave_type_id')->toArray();
        $users = User::pluck('name', 'id')->toArray();
        $holidays = PublicHoliday::pluck('holiday_date')->toArray();
        $user_id = auth()->user()->id;

        return view('leaves.edit', compact('leave', 'leaveTypes', 'users', 'holidays', 'user_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leave $leave, LeaveService $leaveService)
    {
        // Validate the request data
        $requestData = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,leave_type_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'handover_note' => 'required|string|max:5000',
            'handover_note_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'my_work_will_be_done_by' => 'required|array',
            'leave_address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'other_contact_details' => 'nullable|string',
            'reason' => 'nullable|string',
        ]);

        // Check if user can edit this leave
        if (!$leave->canBeEdited()) {
            return back()->with('error', 'You cannot edit this leave request.');
        }

        $user = auth()->user();

        // Validate maternity leave restrictions
        $maternityError = $leaveService->validateMaternityLeave(
            $requestData['leave_type_id'],
            $user->id,
            $requestData['start_date']
        );

        if ($maternityError) {
            return back()
                ->withErrors(['leave_type_id' => $maternityError])
                ->withInput();
        }

        //Validate paternity leave restrictions
        $paternityError = $leaveService->validatePaternityLeave(
            $requestData['leave_type_id'],
            $user->id,
            $requestData['start_date']
        );

        if ($paternityError) {
            return back()
                ->withErrors(['leave_type_id' => $paternityError])
                ->withInput();
        }


        // Validate handover note requirements
        $handoverError = $leaveService->validateHandoverNote(
            $requestData['handover_note'],
            $request->hasFile('handover_note_file')
        );

        if ($handoverError) {
            return back()
                ->withErrors(['handover_note_file' => $handoverError])
                ->withInput();
        }

        // Update the leave request with history tracking
        $leaveService->updateLeaveRequest($leave, $requestData);

        return redirect()->route('leaves.index')->with('success', 'Leave updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leave $leave)
    {
        \Log::info('Delete attempt', [
            'leave_id' => $leave->leave_id,
            'user_id' => $leave->user_id,
            'auth_id' => auth()->id(),
            'is_owner' => (string) $leave->user_id === (string) auth()->id(),
            'current_status' => $leave->getCurrentStatus()
        ]);

        // Check if user can delete this leave
        if ((string) $leave->user_id !== (string) auth()->id()) {
            return response()->json([
                'error' => 'You can only delete your own leave requests.',
                'debug' => [
                    'leave_user_id' => $leave->user_id,
                    'auth_user_id' => auth()->id()
                ]
            ], 403);
        }

        // Check if any approval has been given
        $status = $leave->leave_request_status ? json_decode($leave->leave_request_status, true) : [];
        foreach ($status as $role => $roleStatus) {
            if ($roleStatus === 'approved') {
                return response()->json([
                    'error' => 'Cannot delete an approved leave request.',
                    'debug' => ['approval_status' => $status]
                ], 400);
            }
        }

        try {
            // Remove from any leave roster
            if ($leave->leave_roster_id) {
                $leave->leave_roster_id = null;
                $leave->save();
            }

            // Delete associated history
            LeaveHistory::where('leave_id', $leave->leave_id)->delete();

            $leave->delete();

            return response()->json([
                'message' => 'Leave deleted successfully.',
                'leave_id' => $leave->leave_id
            ]);
        } catch (\Exception $e) {
            \Log::error('Leave deletion failed: ' . $e->getMessage(), [
                'leave_id' => $leave->leave_id,
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to delete leave. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    /**
     * Debug method to troubleshoot leave visibility issues
     */
    public function debug()
    {
        $user = auth()->user();
        $roles = $user ? $user->getRoleNames() : collect();

        $debugInfo = [
            'authenticated' => auth()->check(),
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : null,
            'user_roles' => $roles->toArray(),
            'primary_role' => $roles->first(),
            'has_employee_record' => $user && $user->employee ? true : false,
        ];

        if ($user && $user->employee) {
            $debugInfo['employee_id'] = $user->employee->employee_id;
            $debugInfo['employee_name'] = $user->employee->first_name . ' ' . $user->employee->last_name;
            $debugInfo['department_id'] = $user->employee->department_id;
        }

        // Check leaves with and without scope
        $debugInfo['leaves_with_scope'] = Leave::count();
        $debugInfo['total_leaves_in_db'] = Leave::withoutGlobalScopes()->count();
        $debugInfo['user_leaves_in_db'] = $user ? Leave::withoutGlobalScopes()->where('user_id', $user->id)->count() : 0;

        // Get actual leave data
        $userLeaves = $user ? Leave::withoutGlobalScopes()->where('user_id', $user->id)->get() : collect();
        $debugInfo['user_leave_details'] = $userLeaves->map(function ($leave) {
            return [
                'leave_id' => $leave->leave_id,
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'leave_type' => $leave->leaveCategory ? $leave->leaveCategory->leave_type_name : 'Unknown',
                'status' => $leave->getCurrentStatus(),
                'is_cancelled' => $leave->is_cancelled
            ];
        });

        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Debug method to troubleshoot leave editing permissions
     */
    public function debugEdit(Leave $leave)
    {
        $user = auth()->user();

        $debugInfo = [
            'leave_details' => [
                'leave_id' => $leave->leave_id,
                'user_id' => $leave->user_id,
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'is_cancelled' => $leave->is_cancelled,
                'leave_request_status' => $leave->leave_request_status,
                'current_status' => $leave->getCurrentStatus()
            ],
            'user_details' => [
                'authenticated' => auth()->check(),
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : null,
                'user_roles' => $user ? $user->getRoleNames()->toArray() : [],
                'is_owner' => $user && (string) $user->id === (string) $leave->user_id
            ],
            'edit_permissions' => [
                'can_be_edited' => $leave->canBeEdited(),
                'reasons' => []
            ]
        ];

        // Check each condition for why editing might be blocked
        if (!auth()->check()) {
            $debugInfo['edit_permissions']['reasons'][] = 'User not authenticated';
        }

        if ($user && (string) $user->id !== (string) $leave->user_id) {
            $debugInfo['edit_permissions']['reasons'][] = 'User is not the owner of this leave request';
        }

        if ($leave->is_cancelled) {
            $debugInfo['edit_permissions']['reasons'][] = 'Leave request has been cancelled';
        }

        $status = $leave->leave_request_status ?? [];
        foreach ($status as $role => $roleStatus) {
            if ($roleStatus === 'approved') {
                $debugInfo['edit_permissions']['reasons'][] = "Leave already approved by {$role}";
            }
        }

        if (empty($debugInfo['edit_permissions']['reasons'])) {
            $debugInfo['edit_permissions']['reasons'][] = 'Leave should be editable';
        }

        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }

    public function approveOrReject(Request $request, Leave $leave, LeaveService $leaveService)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:approved,rejected',
                'reason' => 'nullable|string',
            ]);

            $user = auth()->user();
            $status = $request->input('status');
            $reason = $request->input('reason');

            // Debug information to help troubleshoot
            $debugInfo = [
                'user_id' => $user ? $user->id : null,
                'user_roles' => $user ? $user->getRoleNames()->toArray() : [],
                'leave_id' => $leave->leave_id,
                'leave_status' => $leave->leave_request_status,
                'next_approver' => $leave->getNextApproverRole(),
                'current_stage' => $leave->getCurrentApprovalStage(),
                'can_user_approve' => $leave->canUserApprove()
            ];

            \Log::info('Leave approval attempt', $debugInfo);

            // Check if user is authenticated
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Check if user has any approval role
            if (!$user->hasAnyRole(['HR', 'Head of Division', 'Executive Secretary'])) {
                return response()->json([
                    'error' => 'Unauthorized - User does not have required approval role',
                    'user_roles' => $user->getRoleNames()->toArray(),
                    'debug' => $debugInfo
                ], 403);
            }

            // Check if this user can approve at this stage (sequential workflow)
            if (!$leave->canUserApprove()) {
                $nextApprover = $leave->getNextApproverRole();
                $currentStage = $leave->getCurrentApprovalStage();

                return response()->json([
                    'error' => "This leave request is currently at the '{$currentStage}' stage. Only '{$nextApprover}' can approve it at this time.",
                    'debug' => $debugInfo
                ], 403);
            }

            // Use the service to handle approval/rejection with history tracking
            $leaveService->approveOrRejectLeave($leave, $status, $reason);

            // Send notification to leave requester when fully approved or rejected
            if ($status === 'rejected' || $leave->isFullyApproved()) {
                $leaveRequester = User::find($leave->user_id);
                $approver = $user;
                try {
                    Notification::send($leaveRequester, new LeaveApproval($leave, $approver));
                } catch (\Exception $notificationError) {
                    \Log::warning('Failed to send notification', ['error' => $notificationError->getMessage()]);
                    // Continue execution even if notification fails
                }
            }

            $message = $status === 'approved' ? 'Leave approved successfully.' : 'Leave rejected successfully.';

            // Add additional context about next steps
            $additionalInfo = '';
            if ($status === 'approved' && !$leave->isFullyApproved()) {
                $nextApprover = $leave->getNextApproverRole();
                if ($nextApprover) {
                    $additionalInfo = " The request has been forwarded to {$nextApprover} for further approval.";
                }
            }

            return response()->json([
                'message' => $message . $additionalInfo,
                'status' => $leave->leave_request_status,
                'current_stage' => $leave->getCurrentApprovalStage(),
                'next_approver' => $leave->getNextApproverRole(),
                'success' => true
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Leave approval failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'leave_id' => $leave->leave_id ?? null
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred while processing the approval',
                'message' => $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : 'Enable debug mode for more details'
            ], 500);
        }
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

        // Check if offset and limit are set
        $hasOffset = $request->has('offset');
        $hasLimit = $request->has('limit');

        // Get pagination parameters if they exist
        $offset = $hasOffset ? (int) $request->get('offset', 0) : null;
        $limit = $hasLimit ? (int) $request->get('limit', 10) : null;

        // Base query
        $query = Employee::where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
            ->orderBy('first_name', 'asc');

        // Clone the query to count total before applying offset/limit
        $total = (clone $query)->count();

        // Apply offset and limit only if they are provided
        if (!is_null($offset) && !is_null($limit)) {
            $query->skip($offset)->take($limit);
        }

        // Get the employees
        $employees = $query->get();

        // Add numeric IDs and leave information
        $startIndex = $offset ?? 0;

        $employees->transform(function ($employee, $index) use ($startIndex) {
            $totalLeaveDays = $employee->totalLeaveDays();
            $totalLeaveRosterDays = $employee->entitled_leave_days;

            $balance = $totalLeaveRosterDays - $totalLeaveDays;

            $employee->numeric_id = $startIndex + $index + 1; // 1-based index
            $employee->total_leave_days = $totalLeaveDays;
            $employee->total_leave_roster_days = $totalLeaveRosterDays;
            $employee->leave_balance = $balance;

            return $employee;
        });

        // Return JSON response
        return response()->json([
            'total' => $total,
            'rows' => $employees,
        ]);
    }


    public function leaveData(Request $request)
{
    $department = $request->input('department', 'all');
    $approvalStatus = $request->input('approval_status', 'all'); // Get approval status filter
    $year = $request->input('year', 'all'); // Get year filter

    \Log::info('LeaveData Filters', [
        'approval_status' => $approvalStatus,
        'department' => $department,
        'year' => $year
    ]);

    if (auth()->user()->isAdminOrSecretary) {
        $leaveRosterQuery = LeaveRoster::with(['employee', 'leave', 'leave.leaveCategory'])
            ->whereHas('leave');
    } else {
        $leaveRosterQuery = LeaveRoster::with(['employee', 'leave', 'leave.leaveCategory']);
    }

    // Filter by department if selected
    if ($department !== 'all') {
        $employeeIds = Employee::where('department_id', $department)->pluck('employee_id');
        $leaveRosterQuery->whereIn('employee_id', $employeeIds);
    }

    // Get all leave roster data first
    $leaveRoster = $leaveRosterQuery->get();

    // Process leave roster data
    $leaveRosterData = $leaveRoster->map(function ($leave, $index) {
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
            'is_cancelled' => $leave->leave->is_cancelled ?? false,
            'duration' => $leave->durationForLeave()
        ];
    });

    // Query orphaned leaves
    $orphanedLeavesQuery = Leave::with('leaveCategory')
        ->whereNull('leave_roster_id')
        ->with('employee');

    $orphanedLeaves = $orphanedLeavesQuery->get();

    // Process orphaned leaves data
    $orphanedLeavesData = $orphanedLeaves->map(function ($leave, $index) use ($leaveRosterData) {
        $indexOffset = $leaveRosterData->count();
        return [
            'numeric_id' => $indexOffset + $index + 1,
            'leave_roster_id' => null,
            'title' => $leave->leave_title,
            'start' => $leave->start_date->toIso8601String(),
            'end' => $leave->end_date->toIso8601String(),
            'staffId' => $leave->employee->staff_id ?? null,
            'first_name' => $leave->employee->first_name ?? null,
            'last_name' => $leave->employee->last_name ?? null,
            'leave' => $leave,
            'is_cancelled' => $leave->is_cancelled,
            'duration' => $leave->durationForLeave()
        ];
    });

    // Combine all data
    $combinedLeaves = collect($leaveRosterData)->merge($orphanedLeavesData);

    // Apply approval status filter
    if ($approvalStatus !== 'all') {
        $combinedLeaves = $combinedLeaves->filter(function ($item) use ($approvalStatus) {
            $leave = $item['leave'];
            if (!$leave) return $approvalStatus === 'pending'; // No leave application = pending

            switch ($approvalStatus) {
                 case 'active':
                    // Active = currently ongoing and fully approved
                    $now = Carbon::now();
                    return $leave->isFullyApproved() &&
                           $leave->start_date <= $now &&
                           $leave->end_date >= $now;
                case 'approved':
                    return $leave->isFullyApproved();
                case 'pending':
                    // Pending = not fully approved AND not rejected AND not cancelled
                    return !$leave->isFullyApproved() &&
                           !$leave->isRejected() &&
                           !$leave->is_cancelled;
                case 'rejected':
                    return $leave->isRejected();
                default:
                    return true;
            }
        });
    }

    // Apply year filter on the combined collection
    if ($year !== 'all') {
        $combinedLeaves = $combinedLeaves->filter(function ($item) use ($year) {
            return Carbon::parse($item['start'])->year == $year;
        });
    }

    // Remove cancelled leaves from all views except when specifically filtered
    if ($approvalStatus !== 'all') {
        $combinedLeaves = $combinedLeaves->filter(function ($item) {
            return !$item['is_cancelled'];
        });
    }

    // Sort the final data
    $combinedLeaves = $combinedLeaves->sortByDesc(function ($item) {
        return $item['created_at'] ?? $item['start'];
    })->values();

    \Log::info('LeaveData Final Results', [
        'total_records' => $combinedLeaves->count(),
        'approval_status_filter' => $approvalStatus,
        'department_filter' => $department,
        'year_filter' => $year
    ]);

    return response()->json(['success' => true, 'data' => $combinedLeaves]);
}

    public function cancel(Request $request, Leave $leave, LeaveService $leaveService)
    {
        \Log::info('Cancel attempt', [
            'leave_id' => $leave->leave_id,
            'user_id' => $leave->user_id,
            'auth_id' => auth()->id(),
            'is_owner' => (string) $leave->user_id === (string) auth()->id(),
            'current_status' => $leave->getCurrentStatus(),
            'is_cancelled' => $leave->is_cancelled
        ]);

        // Check if user can cancel this leave
        if ((string) $leave->user_id !== (string) auth()->id()) {
            return response()->json([
                'error' => 'You can only cancel your own leave requests.',
                'debug' => [
                    'leave_user_id' => $leave->user_id,
                    'auth_user_id' => auth()->id()
                ]
            ], 403);
        }

        // Check if leave is already cancelled
        if ($leave->is_cancelled) {
            return response()->json([
                'error' => 'This leave request is already cancelled.',
                'debug' => ['is_cancelled' => true]
            ], 400);
        }

        // Check if any approval has been given
        $status = $leave->leave_request_status ?? [];
        foreach ($status as $role => $roleStatus) {
            if ($roleStatus === 'approved') {
                return response()->json([
                    'error' => 'Cannot cancel a leave request that has been approved.',
                    'debug' => ['approval_status' => $status]
                ], 400);
            }
        }

        $reason = $request->input('reason', 'User requested cancellation');
        $leaveService->cancelLeave($leave, $reason);

        return response()->json([
            'message' => 'Leave cancelled successfully.',
            'is_cancelled' => true,
            'leave_id' => $leave->leave_id
        ]);
    }
    /**
     * Get leave history for a specific leave request
     */
    public function history(Leave $leave, LeaveService $leaveService)
    {
        $history = $leaveService->getLeaveHistory($leave->leave_id);

        return response()->json(['history' => $history]);
    }

    /**
     * Show leave history page
     */
    public function showHistory(Leave $leave, LeaveService $leaveService)
    {
        $history = $leaveService->getLeaveHistory($leave->leave_id);

        return view('leaves.history', compact('leave', 'history'));
    }



    // app/Http/Controllers/LeaveController.php

    private function getHandoverFileData(Leave $leave): array
    {
        try {
            if (empty($leave->handover_note_file)) {
                throw new \Exception('No handover file found for leave ID: ' . $leave->leave_id);
            }

            $filePath = ltrim($leave->handover_note_file, '/');
            $fileName = basename($filePath);

            $possiblePaths = [
                storage_path('app/public/' . $filePath),
                storage_path('app/' . $filePath),
                public_path('storage/' . $filePath),
            ];

            \Log::info('Checking handover file paths', [
                'leave_id' => $leave->leave_id,
                'file_path' => $filePath,
                'possible_paths' => $possiblePaths,
            ]);

            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    \Log::info('Handover file found', ['path' => $path]);
                    return [
                        'path' => $path,
                        'name' => $fileName,
                    ];
                }
            }

            throw new \Exception('Handover file does not exist on server: ' . $filePath);
        } catch (\Exception $e) {
            \Log::error('Error retrieving handover file data', [
                'leave_id' => $leave->leave_id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    /**
     * Display the handover file
     */
    // app/Http/Controllers/LeaveController.php

    public function viewHandoverFile(Leave $leave)
    {
        try {
            \Log::info('Checking authorization for viewHandoverFile', [
                'user_id' => auth()->id(),
                'leave_id' => $leave->leave_id,
                'is_owner' => (string) auth()->id() === (string) $leave->user_id,
                'roles' => auth()->user()->roles->pluck('name')->toArray(),
            ]);

            $this->authorize('viewHandoverFile', $leave);

            $attachment = $this->getHandoverFileData($leave);
            $filePath = $attachment['path'];
            $fileName = $attachment['name'];
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $mimeType = mime_content_type($filePath);

            \Log::info('Viewing handover file', [
                'leave_id' => $leave->leave_id,
                'file_path' => $filePath,
                'user_id' => auth()->id(),
            ]);

            if (strtolower($fileExtension) === 'pdf' || $mimeType === 'application/pdf') {
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                ]);
            }

            if (strpos($mimeType, 'image/') === 0) {
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                ]);
            }

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Handover file view error', [
                'error' => $e->getMessage(),
                'leave_id' => $leave->leave_id,
                'user_id' => auth()->id(),
            ]);

            // Redirect to leave details page instead of back()
            return redirect()->route('leaves.show', ['leave' => $leave->leave_id])
                ->withErrors('Could not view the handover file: ' . $e->getMessage());
        }
    }

    public function downloadHandoverFile(Leave $leave)
    {
        try {
            \Log::info('Checking authorization for downloadHandoverFile', [
                'user_id' => auth()->id(),
                'leave_id' => $leave->leave_id,
                'is_owner' => (string) auth()->id() === (string) $leave->user_id,
                'roles' => auth()->user()->roles->pluck('name')->toArray(),
            ]);

            $this->authorize('viewHandoverFile', $leave);

            $attachment = $this->getHandoverFileData($leave);
            $filePath = $attachment['path'];
            $fileName = $attachment['name'];
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $mimeType = mime_content_type($filePath);

            \Log::info('Downloading handover file', [
                'leave_id' => $leave->leave_id,
                'file_path' => $filePath,
                'user_id' => auth()->id(),
            ]);

            return response()->download($filePath, $fileName, [
                'Content-Type' => $mimeType,
            ]);
        } catch (\Exception $e) {
            \Log::error('Handover file download error', [
                'error' => $e->getMessage(),
                'leave_id' => $leave->leave_id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('leaves.show', ['leave' => $leave->leave_id])
                ->withErrors('Could not download the handover file: ' . $e->getMessage());
        }
    }

    // Add this method to your LeaveController
    public function currentlyOnLeave()
    {
        try {
            // Get all currently active and approved leaves with employee data
            $currentLeaves = Leave::with(['employee', 'employee.department', 'leaveCategory'])
                ->where('is_cancelled', false)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get()
                ->filter(function ($leave) {
                    return $leave->isFullyApproved();
                });

            // For now, return JSON to test
            return response()->json([
                'success' => true,
                'total_employees' => $currentLeaves->count(),
                'employees' => $currentLeaves->map(function ($leave, $index) {
                    return [
                        'number' => $index + 1,
                        'name' => $leave->employee ? $leave->employee->first_name . ' ' . $leave->employee->last_name : 'Unknown',
                        'staff_id' => $leave->employee->staff_id ?? 'N/A',
                        'department' => $leave->employee->department->department_name ?? 'N/A',
                        'leave_type' => $leave->leaveCategory->leave_type_name ?? 'Unknown',
                        'start_date' => $leave->start_date->format('M d, Y'),
                        'end_date' => $leave->end_date->format('M d, Y'),
                        'status' => $leave->getCurrentStatus(),
                    ];
                }),
                'message' => 'Found ' . $currentLeaves->count() . ' employees currently on approved leave'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function getEmployeesOnLeave(Request $request)
{
    $today = now()->format('Y-m-d');

    // Get leaves that are currently active (today is between start_date and end_date)
    $activeLeaves = Leave::with(['employee.department', 'leaveCategory'])
        ->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)
        ->where('is_cancelled', false)
        ->where(function($query) {
            // Only include approved leaves (check if all required approvals are given)
            $query->where(function($q) {
                $q->whereJsonContains('leave_request_status->HR', 'approved')
                  ->whereJsonContains('leave_request_status->Head of Division', 'approved')
                  ->whereJsonContains('leave_request_status->Executive Secretary', 'approved');
            })->orWhere('leave_request_status', null); // Or include newly submitted leaves if needed
        })
        ->get();

    // Transform the data for the response
    $employeesOnLeave = $activeLeaves->map(function($leave) {
        return [
            'first_name' => $leave->employee->first_name ?? 'Unknown',
            'last_name' => $leave->employee->last_name ?? 'Employee',
            'staff_id' => $leave->employee->staff_id ?? 'N/A',
            'department_name' => $leave->employee->department->department_name ?? 'N/A',
            'leave_type' => $leave->leaveCategory->leave_type_name ?? 'Unknown',
            'leave_start_date' => $leave->start_date->format('Y-m-d'),
            'leave_end_date' => $leave->end_date->format('Y-m-d'),
            'duration' => $leave->durationForLeave(\App\Models\PublicHoliday::pluck('holiday_date')->toArray()),
        ];
    });

    return response()->json([
        'total_count' => $employeesOnLeave->count(),
        'employees' => $employeesOnLeave
    ]);
}
}