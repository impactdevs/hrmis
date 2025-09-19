<?php

namespace App\Services;

use App\Models\Appraisal;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\EmployeeScope;

class AppraisalQueryService
{
    /**
     * Get appraisals query based on user role and filters
     */
    public function getAppraisalsQuery(User $user, ?string $dashboardFilter = null, ?string $search = null): Builder
    {
        $role = $user->getRoleNames()->first();
        
        switch ($role) {
            case 'HR':
                return $this->getHRAppraisalsQuery($dashboardFilter, $search);
            case 'Executive Secretary':
                return $this->getExecutiveSecretaryAppraisalsQuery($dashboardFilter, $search);
            case 'Head of Division':
                return $this->getHeadOfDivisionAppraisalsQuery($user, $search);
            case 'Staff':
                return $this->getStaffAppraisalsQuery($user, $search);
            default:
                return $this->getDefaultAppraisalsQuery($search);
        }
    }

    /**
     * Get HR appraisals query with dashboard filters
     */
    private function getHRAppraisalsQuery(?string $dashboardFilter, ?string $search): Builder
    {
        $query = Appraisal::withoutGlobalScopes()
            ->with('employee')
            ->join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
            ->where('appraisal_drafts.is_submitted', true)
            ->select('appraisals.*');

        switch ($dashboardFilter) {
            case 'submitted_to_es':
                $query->whereJsonContains('appraisals.appraisal_request_status', [
                    'Head of Division' => 'approved',
                    'HR' => 'approved'
                ]);
                break;

            case 'completed_appraisals':
                $query->whereJsonContains('appraisals.appraisal_request_status', [
                    'Executive Secretary' => 'approved'
                ]);
                break;

            case 'received_from_HoDs':
                $query->where(function ($q) {
                    $q->whereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'approved'])
                      ->whereJsonDoesntContain('appraisals.appraisal_request_status', ['HR' => 'approved'])
                      ->whereJsonDoesntContain('appraisals.appraisal_request_status', ['HR' => 'rejected']);
                });
                break;

            case 'awaiting_for_me':
                $query->where(function ($q) {
                    $q->whereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'approved'])
                      ->whereJsonDoesntContain('appraisals.appraisal_request_status', ['HR' => 'approved'])
                      ->whereJsonDoesntContain('appraisals.appraisal_request_status', ['HR' => 'rejected']);
                });
                break;

            default:
                // Show all appraisals that HR can see
                $query->where(function ($q) {
                    $q->whereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'approved'])
                      ->orWhereJsonContains('appraisals.appraisal_request_status', ['HR' => 'approved'])
                      ->orWhereJsonContains('appraisals.appraisal_request_status', ['HR' => 'rejected']);
                });
                break;
        }

        return $this->addEmployeeSearch($query, $search);
    }

    /**
     * Get Executive Secretary appraisals query with dashboard filters
     */
    private function getExecutiveSecretaryAppraisalsQuery(?string $dashboardFilter, ?string $search): Builder
    {
        $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id');

        switch ($dashboardFilter) {
            case 'submitted_to_es':
                $query->whereJsonContains('appraisal_request_status', [
                    'Head of Division' => 'approved',
                    'HR' => 'approved'
                ])->where('appraisal_drafts.is_submitted', true);
                break;

            case 'completed_appraisals':
                $executiveSecretary = User::role('Executive Secretary')->first();
                $employeeId = $executiveSecretary ? 
                    Employee::withoutGlobalScope(EmployeeScope::class)
                        ->where('user_id', $executiveSecretary->id)
                        ->value('employee_id') : null;

                $query->where(function ($q) use ($employeeId) {
                    $q->whereJsonContains('appraisal_request_status', [
                        'Head of Division' => 'approved',
                        'HR' => 'approved',
                        'Executive Secretary' => 'approved'
                    ]);
                    
                    if ($employeeId) {
                        $q->orWhere('appraiser_id', $employeeId)
                          ->whereJsonContains('appraisal_request_status', ['Executive Secretary' => 'approved']);
                    }
                })->where('appraisal_drafts.is_submitted', true);
                break;

            case 'from_all_supervisors':
                $query->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereJsonContains('appraisal_request_status', [
                            'Head of Division' => 'approved',
                            'HR' => 'approved'
                        ])->whereNull("appraisals.appraisal_request_status->Executive Secretary");
                    })
                    ->orWhereExists(function ($subQuery) {
                        $this->addHoDEmployeeExistsClause($subQuery);
                    })
                    ->orWhere('appraiser_id', auth()->user()->employee->employee_id);
                })
                ->where(function ($q) {
                    $q->whereNull("appraisals.appraisal_request_status->Executive Secretary")
                      ->orWhereJsonDoesntContain('appraisal_request_status', ['Executive Secretary' => 'approved']);
                })
                ->where('appraisal_drafts.is_submitted', true);
                break;

            case 'pending_appraisals':
                $query->where('appraisal_drafts.is_submitted', false)
                      ->where('appraisal_drafts.employee_id', auth()->user()->employee->employee_id);
                break;

            default:
                $query->where(function ($q) {
                    $q->whereJsonContains('appraisal_request_status', [
                        'Head of Division' => 'approved',
                        'HR' => 'approved'
                    ])
                    ->orWhereExists(function ($subQuery) {
                        $this->addHoDEmployeeExistsClause($subQuery);
                    })
                    ->orWhere('appraiser_id', auth()->user()->employee->employee_id);
                })->where('appraisal_drafts.is_submitted', true);
                break;
        }

        return $this->addEmployeeSearch($query, $search);
    }

    /**
     * Get Head of Division appraisals query
     */
    private function getHeadOfDivisionAppraisalsQuery(User $user, ?string $search): Builder
    {
        $query = Appraisal::with('employee')->latest();
        
        // Filter by department if the HoD has a department
        if ($user->employee && $user->employee->department_id) {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('department_id', $user->employee->department_id);
            });
        }

        return $this->addEmployeeSearch($query, $search);
    }

    /**
     * Get Staff appraisals query
     */
    private function getStaffAppraisalsQuery(User $user, ?string $search): Builder
    {
        $query = Appraisal::with('employee')->latest();
        
        // Staff can only see their own appraisals (handled by AppraisalScope)
        return $this->addEmployeeSearch($query, $search);
    }

    /**
     * Get default appraisals query
     */
    private function getDefaultAppraisalsQuery(?string $search): Builder
    {
        $query = Appraisal::with('employee')->latest();
        return $this->addEmployeeSearch($query, $search);
    }

    /**
     * Add employee search to query
     */
    private function addEmployeeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Add HR appraisal exists clause
     */
    private function addHRAppraisalExistsClause($subQuery): void
    {
        $subQuery->select(DB::raw(1))
                 ->from('employees')
                 ->join('users', 'users.id', '=', 'employees.user_id')
                 ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                 ->whereColumn('employees.employee_id', 'appraisals.appraiser_id')
                 ->where('model_has_roles.model_type', User::class)
                 ->where('roles.name', 'HR')
                 ->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
    }

    /**
     * Add Head of Division employee exists clause
     */
    private function addHoDEmployeeExistsClause($subQuery): void
    {
        $subQuery->select(DB::raw(1))
                 ->from('employees')
                 ->join('users', 'users.id', '=', 'employees.user_id')
                 ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                 ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                 ->whereColumn('employees.employee_id', 'appraisals.employee_id')
                 ->where('model_has_roles.model_type', User::class)
                 ->where('roles.name', 'Head of Division');
    }

    /**
     * Get appraisals that need action from the current user
     */
    public function getAppraisalsRequiringAction(User $user): Builder
    {
        $role = $user->getRoleNames()->first();
        $employeeId = $user->employee->employee_id ?? null;

        switch ($role) {
            case 'Head of Division':
                return Appraisal::withoutGlobalScopes()
                    ->where('current_stage', 'Head of Division')
                    ->whereJsonDoesntContain('appraisal_request_status', ['Head of Division' => 'approved'])
                    ->whereJsonDoesntContain('appraisal_request_status', ['Head of Division' => 'rejected']);

            case 'HR':
                return Appraisal::withoutGlobalScopes()
                    ->where('current_stage', 'HR')
                    ->whereJsonDoesntContain('appraisal_request_status', ['HR' => 'approved'])
                    ->whereJsonDoesntContain('appraisal_request_status', ['HR' => 'rejected']);

            case 'Executive Secretary':
                return Appraisal::withoutGlobalScopes()
                    ->where('current_stage', 'Executive Secretary')
                    ->whereJsonDoesntContain('appraisal_request_status', ['Executive Secretary' => 'approved'])
                    ->whereJsonDoesntContain('appraisal_request_status', ['Executive Secretary' => 'rejected']);

            case 'Staff':
                return Appraisal::where('employee_id', $employeeId)
                    ->where('current_stage', 'Staff')
                    ->where('is_draft', true);

            default:
                return Appraisal::whereRaw('1 = 0'); // Return empty query
        }
    }

    /**
     * Get overdue appraisals for the user
     */
    public function getOverdueAppraisals(User $user): Builder
    {
        $query = $this->getAppraisalsRequiringAction($user);
        
        return $query->whereNotNull('staff_deadline')
                    ->get()
                    ->filter(function ($appraisal) {
                        return $appraisal->is_overdue;
                    });
    }
}
