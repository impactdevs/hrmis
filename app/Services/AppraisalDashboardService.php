<?php

namespace App\Services;

use App\Models\Appraisal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppraisalDashboardService
{
    /**
     * Get comprehensive appraisal statistics
     */
    public function getAppraisalStatistics(User $user): array
    {
        $role = $user->getRoleNames()->first();
        
        switch ($role) {
            case 'HR':
            case 'Executive Secretary':
                return $this->getAdminStatistics();
            case 'Head of Division':
                return $this->getSupervisorStatistics($user);
            case 'Staff':
                return $this->getStaffStatistics($user);
            default:
                return $this->getBasicStatistics();
        }
    }

    /**
     * Get statistics for HR/Executive Secretary
     */
    private function getAdminStatistics(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        
        return [
            'overview' => [
                'total_appraisals' => Appraisal::withoutGlobalScopes()->count(),
                'pending_appraisals' => Appraisal::withoutGlobalScopes()->where('current_stage', '!=', 'Completed')->count(),
                'completed_this_month' => Appraisal::withoutGlobalScopes()
                    ->where('current_stage', 'Completed')
                    ->where('updated_at', '>=', $currentMonth)
                    ->count(),
                'overdue_appraisals' => $this->getOverdueCount(),
            ],
            'by_stage' => $this->getAppraisalsByStage(),
            'by_department' => $this->getAppraisalsByDepartment(),
            'recent_activity' => $this->getRecentActivity(10),
            'deadline_alerts' => $this->getDeadlineAlerts(),
            'performance_metrics' => $this->getPerformanceMetrics(),
            'trends' => $this->getAppraisalTrends(),
        ];
    }

    /**
     * Get statistics for Head of Division
     */
    private function getSupervisorStatistics(User $user): array
    {
        $departmentId = $user->employee->department_id ?? null;
        
        if (!$departmentId) {
            return $this->getBasicStatistics();
        }

        $baseQuery = Appraisal::withoutGlobalScopes()
            ->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });

        return [
            'overview' => [
                'total_appraisals' => $baseQuery->count(),
                'pending_review' => $baseQuery->whereJsonContains('appraisal_request_status', ['Head of Division' => null])->count(),
                'pending_approval' => $baseQuery->where('current_stage', 'Head of Division')->count(),
                'completed_appraisals' => $baseQuery->where('current_stage', 'Completed')->count(),
            ],
            'team_performance' => $this->getTeamPerformanceMetrics($departmentId),
            'recent_submissions' => $this->getRecentSubmissions($departmentId),
            'deadline_alerts' => $this->getDeadlineAlertsForDepartment($departmentId),
        ];
    }

    /**
     * Get statistics for Staff
     */
    private function getStaffStatistics(User $user): array
    {
        $employeeId = $user->employee->employee_id ?? null;
        
        if (!$employeeId) {
            return $this->getBasicStatistics();
        }

        $userAppraisals = Appraisal::withoutGlobalScopes()
            ->where('employee_id', $employeeId);

        return [
            'overview' => [
                'total_appraisals' => $userAppraisals->count(),
                'pending_appraisals' => $userAppraisals->where('current_stage', '!=', 'Completed')->count(),
                'completed_appraisals' => $userAppraisals->where('current_stage', 'Completed')->count(),
                'draft_appraisals' => $userAppraisals->where('is_draft', true)->count(),
            ],
            'current_appraisal' => $this->getCurrentAppraisalStatus($employeeId),
            'appraisal_history' => $this->getAppraisalHistory($employeeId),
            'next_deadline' => $this->getNextDeadline($employeeId),
        ];
    }

    /**
     * Get basic statistics fallback
     */
    private function getBasicStatistics(): array
    {
        return [
            'overview' => [
                'message' => 'Limited access to appraisal statistics',
                'total_appraisals' => 0,
                'pending_appraisals' => 0,
                'completed_appraisals' => 0,
            ]
        ];
    }

    /**
     * Get appraisals grouped by current stage
     */
    private function getAppraisalsByStage(): array
    {
        return Appraisal::withoutGlobalScopes()
            ->select('current_stage', DB::raw('count(*) as total'))
            ->groupBy('current_stage')
            ->pluck('total', 'current_stage')
            ->toArray();
    }

    /**
     * Get appraisals grouped by department
     */
    private function getAppraisalsByDepartment(): array
    {
        return Appraisal::withoutGlobalScopes()
            ->join('employees', 'appraisals.employee_id', '=', 'employees.employee_id')
            ->join('departments', 'employees.department_id', '=', 'departments.department_id')
            ->select('departments.department_name', DB::raw('count(*) as total'))
            ->groupBy('departments.department_id', 'departments.department_name')
            ->pluck('total', 'department_name')
            ->toArray();
    }

    /**
     * Get recent appraisal activity
     */
    private function getRecentActivity(int $limit = 10): array
    {
        return Appraisal::withoutGlobalScopes()
            ->with(['employee', 'history' => function ($query) {
                $query->latest()->take(1);
            }])
            ->latest('updated_at')
            ->take($limit)
            ->get()
            ->map(function ($appraisal) {
                $latestHistory = $appraisal->history->first();
                return [
                    'appraisal_id' => $appraisal->appraisal_id,
                    'employee_name' => $appraisal->employee->first_name . ' ' . $appraisal->employee->last_name,
                    'current_stage' => $appraisal->current_stage,
                    'last_action' => $latestHistory ? $latestHistory->action_description : 'Created',
                    'updated_at' => $appraisal->updated_at->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Get deadline alerts
     */
    private function getDeadlineAlerts(): array
    {
        $appraisals = Appraisal::withoutGlobalScopes()
            ->with('employee')
            ->where('current_stage', '!=', 'Completed')
            ->whereNotNull('staff_deadline')
            ->get();

        return $appraisals->filter(function ($appraisal) {
                return $appraisal->is_overdue || $appraisal->days_until_deadline <= 2;
            })
            ->map(function ($appraisal) {
                $status = $appraisal->deadline_status;
                return [
                    'appraisal_id' => $appraisal->appraisal_id,
                    'employee_name' => $appraisal->employee->first_name . ' ' . $appraisal->employee->last_name,
                    'current_stage' => $appraisal->current_stage,
                    'status' => $status['status'],
                    'message' => $status['message'],
                    'color' => $status['color'],
                    'days' => $status['days'],
                ];
            })
            ->sortBy('days')
            ->values()
            ->toArray();
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        $totalAppraisals = Appraisal::withoutGlobalScopes()->count();
        
        if ($totalAppraisals === 0) {
            return [
                'completion_rate' => 0,
                'average_completion_time' => 0,
                'on_time_completion_rate' => 0,
            ];
        }

        $completedAppraisals = Appraisal::withoutGlobalScopes()
            ->where('current_stage', 'Completed')
            ->count();

        $onTimeCompletions = Appraisal::withoutGlobalScopes()
            ->where('current_stage', 'Completed')
            ->whereColumn('updated_at', '<=', 'final_deadline')
            ->count();

        $avgCompletionTime = Appraisal::withoutGlobalScopes()
            ->where('current_stage', 'Completed')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');

        return [
            'completion_rate' => round(($completedAppraisals / $totalAppraisals) * 100, 1),
            'average_completion_time' => round($avgCompletionTime ?? 0, 1),
            'on_time_completion_rate' => $completedAppraisals > 0 ? round(($onTimeCompletions / $completedAppraisals) * 100, 1) : 0,
        ];
    }

    /**
     * Get appraisal trends over time
     */
    private function getAppraisalTrends(): array
    {
        $months = collect(range(0, 11))->map(function ($monthsBack) {
            return Carbon::now()->subMonths($monthsBack)->format('Y-m');
        })->reverse();

        $trends = [];
        foreach ($months as $month) {
            $startOfMonth = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endOfMonth = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            
            $created = Appraisal::withoutGlobalScopes()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
                
            $completed = Appraisal::withoutGlobalScopes()
                ->where('current_stage', 'Completed')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
                ->count();
            
            $trends[] = [
                'month' => $startOfMonth->format('M Y'),
                'created' => $created,
                'completed' => $completed,
            ];
        }

        return $trends;
    }

    /**
     * Get overdue count
     */
    private function getOverdueCount(): int
    {
        return Appraisal::withoutGlobalScopes()
            ->where('current_stage', '!=', 'Completed')
            ->whereNotNull('staff_deadline')
            ->get()
            ->filter(function ($appraisal) {
                return $appraisal->is_overdue;
            })
            ->count();
    }

    /**
     * Get team performance metrics for department
     */
    private function getTeamPerformanceMetrics(string $departmentId): array
    {
        $teamAppraisals = Appraisal::withoutGlobalScopes()
            ->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });

        $total = $teamAppraisals->count();
        $completed = $teamAppraisals->where('current_stage', 'Completed')->count();
        $pending = $teamAppraisals->where('current_stage', '!=', 'Completed')->count();

        return [
            'total_team_appraisals' => $total,
            'completed_team_appraisals' => $completed,
            'pending_team_appraisals' => $pending,
            'team_completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get recent submissions for department
     */
    private function getRecentSubmissions(string $departmentId, int $limit = 5): array
    {
        return Appraisal::withoutGlobalScopes()
            ->with('employee')
            ->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->latest('updated_at')
            ->take($limit)
            ->get()
            ->map(function ($appraisal) {
                return [
                    'appraisal_id' => $appraisal->appraisal_id,
                    'employee_name' => $appraisal->employee->first_name . ' ' . $appraisal->employee->last_name,
                    'current_stage' => $appraisal->current_stage,
                    'updated_at' => $appraisal->updated_at->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    /**
     * Get deadline alerts for specific department
     */
    private function getDeadlineAlertsForDepartment(string $departmentId): array
    {
        return Appraisal::withoutGlobalScopes()
            ->with('employee')
            ->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->where('current_stage', '!=', 'Completed')
            ->whereNotNull('staff_deadline')
            ->get()
            ->filter(function ($appraisal) {
                return $appraisal->is_overdue || $appraisal->days_until_deadline <= 3;
            })
            ->map(function ($appraisal) {
                $status = $appraisal->deadline_status;
                return [
                    'appraisal_id' => $appraisal->appraisal_id,
                    'employee_name' => $appraisal->employee->first_name . ' ' . $appraisal->employee->last_name,
                    'status' => $status['status'],
                    'message' => $status['message'],
                    'color' => $status['color'],
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get current appraisal status for staff member
     */
    private function getCurrentAppraisalStatus(string $employeeId): ?array
    {
        $currentAppraisal = Appraisal::withoutGlobalScopes()
            ->where('employee_id', $employeeId)
            ->where('current_stage', '!=', 'Completed')
            ->latest()
            ->first();

        if (!$currentAppraisal) {
            return null;
        }

        return [
            'appraisal_id' => $currentAppraisal->appraisal_id,
            'current_stage' => $currentAppraisal->current_stage,
            'stage_progress' => $currentAppraisal->stage_progress,
            'deadline_status' => $currentAppraisal->deadline_status,
            'next_approver' => $currentAppraisal->getNextApproverRole(),
            'can_edit' => !isset($currentAppraisal->appraisal_request_status['Head of Division']),
        ];
    }

    /**
     * Get appraisal history for staff member
     */
    private function getAppraisalHistory(string $employeeId): array
    {
        return Appraisal::withoutGlobalScopes()
            ->where('employee_id', $employeeId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($appraisal) {
                return [
                    'appraisal_id' => $appraisal->appraisal_id,
                    'current_stage' => $appraisal->current_stage,
                    'created_at' => $appraisal->created_at->format('M d, Y'),
                    'updated_at' => $appraisal->updated_at->format('M d, Y'),
                ];
            })
            ->toArray();
    }

    /**
     * Get next deadline for staff member
     */
    private function getNextDeadline(string $employeeId): ?array
    {
        $appraisal = Appraisal::withoutGlobalScopes()
            ->where('employee_id', $employeeId)
            ->where('current_stage', '!=', 'Completed')
            ->whereNotNull('staff_deadline')
            ->first();

        if (!$appraisal) {
            return null;
        }

        $deadline = $appraisal->current_deadline;
        if (!$deadline) {
            return null;
        }

        return [
            'deadline' => $deadline->format('M d, Y'),
            'days_until' => $appraisal->days_until_deadline,
            'status' => $appraisal->deadline_status,
        ];
    }
}
