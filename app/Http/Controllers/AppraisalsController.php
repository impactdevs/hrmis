<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\User;
use App\Models\AppraisalHistory;
use App\Notifications\AppraisalApproval;
use App\Notifications\AppraisalApplication;
use App\Notifications\AppraisalWithdrawn;
use App\Notifications\AppraisalResubmitted;
use Illuminate\Http\Request;
use App\Models\Scopes\EmployeeScope;
use Illuminate\Support\Facades\Notification;
use App\Services\AppraisalService;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use setasign\Fpdi\Fpdi;


use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AppraisalsController extends Controller
{

    protected $appraisalService;

    public function __construct(AppraisalService $appraisalService)
    {
        $this->appraisalService = $appraisalService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        if (auth()->user()->hasRole('HR')) {

            $dashboard_filter = $request->get('dashboard_filter');

            //if the filter is submitted_to_es

            //to the E.S
            $submittedAppraisalsByHR = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved'])
                ->where('appraisal_drafts.is_submitted', true)
                ->get();

            if (!is_null($dashboard_filter) && $dashboard_filter == 'submitted_to_es') {
                //these are the appraisals that the H.R submitted to the E.S.
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved'])
                    ->where('appraisal_drafts.is_submitted', true)
                    ->latest('appraisals.created_at');
            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'completed_appraisals') {
                //get the Executive Secretary's account
                $executiveSecretary = User::role('Executive Secretary')->first();

                $employeeId = Employee::withoutGlobalScope(EmployeeScope::class)
                    ->where('user_id', $executiveSecretary->id)
                    ->value('employee_id');
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved', 'Executive Secretary' => 'approved'])
                    //or where the appraser is the Executive Secretary and has approved
                    ->orWhere('appraiser_id', $employeeId)->whereJsonContains('appraisal_request_status', ['Executive Secretary' => 'approved'])
                    ->where('appraisal_drafts.is_submitted', true)
                    ->latest('appraisals.created_at');
            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'received_from_HoDs') {
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
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
                    ->latest('appraisals.created_at');

            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'awaiting_for_me') {

                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->where(function ($query) {
                        $query->whereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'approved'])
                            ->orWhereJsonContains('appraisals.appraisal_request_status', ['Executive Secretary' => 'approved'])
                            ->orWhereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'rejected'])
                            ->orWhereJsonContains('appraisals.appraisal_request_status', ['Executive Secretary' => 'rejected'])
                            ->orWhereExists(function ($subQuery) {
                                $subQuery->select(DB::raw(1))
                                    ->from('employees')
                                    ->join('users', 'users.id', '=', 'employees.user_id')
                                    ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                    ->whereColumn('employees.employee_id', 'appraisals.appraiser_id')
                                    ->where('model_has_roles.model_type', User::class)
                                    ->where('roles.name', 'HR')
                                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
                            })
                            // Include resubmissions where HR is the appraiser (after resubmission, status is cleared)
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('appraiser_id', auth()->user()->employee->employee_id)
                                    ->where('appraisal_request_status', null); // Status cleared after resubmission
                            });
                    })
                    ->where(function ($q) {
                        $q->whereNull("appraisals.appraisal_request_status->HR")
                            ->orWhere('appraisal_request_status', null); // Include cleared status after resubmission
                    })
                    ->where('appraisal_drafts.is_submitted', true)
                    ->latest('appraisals.created_at');
            } else {
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->where(function ($query) {
                        $query->whereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'approved'])
                            ->orWhereJsonContains('appraisals.appraisal_request_status', ['Executive Secretary' => 'approved'])
                            ->orWhereJsonContains('appraisals.appraisal_request_status', ['Head of Division' => 'rejected'])
                            ->orWhereJsonContains('appraisals.appraisal_request_status', ['Executive Secretary' => 'rejected'])
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
                    ->latest('appraisals.created_at');
            }

            if ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('staff_id', 'like', "%{$search}%");
                });
            }

            $appraisals = $query->paginate();
        } else if (auth()->user()->hasRole('Executive Secretary')) {

            $dashboard_filter = $request->get('dashboard_filter');

            if (!is_null($dashboard_filter) && $dashboard_filter == 'submitted_to_es') {
                //these are the appraisals that the H.R submitted to the E.S.
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved'])

                    ->where('appraisal_drafts.is_submitted', true)
                    ->latest('appraisals.created_at');
            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'completed_appraisals') {

                //get the Executive Secretary's account
                $executiveSecretary = User::role('Executive Secretary')->first();

                $employeeId = Employee::withoutGlobalScope(EmployeeScope::class)
                    ->where('user_id', $executiveSecretary->id)
                    ->value('employee_id');
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved', 'Executive Secretary' => 'approved'])
                    ->orWhere('appraiser_id', $employeeId)->whereJsonContains('appraisal_request_status', ['Executive Secretary' => 'approved'])

                    ->where('appraisal_drafts.is_submitted', true)
                    ->latest('appraisals.created_at');
            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'from_all_supervisors') {
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
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
                    ->where('appraisal_drafts.is_submitted', true)
                    ->latest('appraisals.created_at');

            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'pending_appraisals') {
                //get the logged in person's drafts
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->where('appraisal_drafts.is_submitted', false)
                    ->where(function ($query) {
                        $query->where('appraisal_drafts.employee_id', auth()->user()->employee->employee_id);
                    })
                    ->latest('appraisals.created_at');

            } else {
                $query = Appraisal::join('appraisal_drafts', 'appraisal_drafts.appraisal_id', '=', 'appraisals.appraisal_id')
                    ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved', 'HR' => 'approved'])
                    ->orWhereExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('employees')
                            ->join('users', 'users.id', '=', 'employees.user_id')
                            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                            ->whereColumn('employees.employee_id', 'appraisals.employee_id')
                            ->where('model_has_roles.model_type', User::class)
                            ->where('roles.name', 'Head of Division')
                            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id');
                    })
                    //or where the ES is the appraiser
                    ->orWhere('appraiser_id', auth()->user()->employee->employee_id)
                    ->where('appraisal_drafts.is_submitted', true)
                    ->latest('appraisals.created_at');
            }

            if ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('staff_id', 'like', "%{$search}%");
                });
            }

            $appraisals = $query->paginate();
        } else if (auth()->user()->hasRole('Head of Division')) {
            $dashboard_filter = $request->get('dashboard_filter');
            $hodEmployeeId = auth()->user()->employee->employee_id;

            if (!is_null($dashboard_filter) && $dashboard_filter == 'pending_approval') {
                // ONLY appraisals waiting for HOD approval (excluding drafts)
                $query = Appraisal::where('appraiser_id', $hodEmployeeId)
                    ->where(function ($q) {
                        $q->whereNull('appraisal_request_status->Head of Division')
                            ->orWhere('appraisal_request_status', null)
                            ->orWhereJsonDoesntContain('appraisal_request_status', ['Head of Division']);
                    })
                    ->where('is_draft', false)->latest(); // Exclude drafts

            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'approved_by_me') {
                // ONLY appraisals approved by this HOD
                $query = Appraisal::where('appraiser_id', $hodEmployeeId)
                    ->whereJsonContains('appraisal_request_status', ['Head of Division' => 'approved'])
                    ->latest();

            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'approved_by_es') {
                // ONLY appraisals approved by Executive Secretary
                $query = Appraisal::where('appraiser_id', $hodEmployeeId)
                    ->whereJsonContains('appraisal_request_status', ['Executive Secretary' => 'approved'])
                    ->latest();

            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'submitted_to_hr') {
                // ONLY appraisals submitted to HR (approved by all previous stages)
                $query = Appraisal::where('appraiser_id', $hodEmployeeId)
                    ->where(function ($q) {
                        // Assuming HR is the final stage - adjust based on your workflow
                        $q->whereJsonContains('appraisal_request_status', ['HR' => 'pending'])
                            ->orWhereJsonContains('appraisal_request_status', ['HR' => 'approved'])
                            ->orWhere('current_stage', 'HR');
                    })
                    ->where('is_draft', false)
                    ->latest();

            } else if (!is_null($dashboard_filter) && $dashboard_filter == 'drafts') {
                // ONLY draft appraisals (both HOD's own drafts and team drafts where HOD is appraiser)
                $query = Appraisal::where(function ($q) use ($hodEmployeeId) {
                    $q->where('appraiser_id', $hodEmployeeId) // Team drafts
                        ->orWhere('employee_id', $hodEmployeeId); // HOD's own drafts
                })
                    ->where('is_draft', true)
                    ->latest();

            } else {
                // Default: All appraisals where HOD is involved (as appraiser or appraisee)
                $query = Appraisal::where(function ($q) use ($hodEmployeeId) {
                    $q->where('appraiser_id', $hodEmployeeId)
                        ->orWhere('employee_id', $hodEmployeeId);
                })
                ->latest();
            }

            // Search functionality
            if ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('staff_id', 'like', "%{$search}%");
                });
            }

            $appraisals = $query->paginate();
        } else {
            $query = Appraisal::with('employee')->latest();

            if ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('staff_id', 'like', "%{$search}%");
                });
            }

            $appraisals = $query->paginate();
        }

        $appraisals->appends(['search' => $search]);

        return view('appraisals.index', compact('appraisals'));
    }

    /**
     * Show the form for creating a new resource controller
     */
    public function create()
    {
        //get the role of the logged in user
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        //if  the user is staff, then we can create an appraisal
        if ($role == 'Staff') {
            if (!auth()->user()->employee) {
                return back()->with("success", "No Employee record found! Ask the human resource");
            }
            if (!auth()->user()->employee->department) {
                return back()->with("success", "You do not belong to a department, contact the H.R to assign you to a department.");
            }

            // if the is no department user, return to the previous page with an error message
            if (!User::find(auth()->user()->employee->department->department_head)) {
                return back()->with("success", "Your department does not have a department head, so we cant determine a supervisor for you!Reach out to the administrator.");
            } else {
                $role = User::find(auth()->user()->employee->department->department_head)->hasRole('Head of Division') || User::find(auth()->user()->employee->department->department_head)->hasRole('HR');

                if (!$role)
                    return back()->with("success", "Appraisal creation failed, contact the HR.");
            }

            $appraser_id = User::find(auth()->user()->employee->department->department_head)->employee->employee_id;
        } else if ($role == 'Head of Division') {
            //get the user with the role of Secretary
            $user = User::role('Executive Secretary')->whereHas('employee')->first();

            //get the employee_id of the user
            if (!$user || !$user->employee) {
                return back()->with("success", "No Employee record found for the Executive Secretary! Ask the human resource");
            }

            $appraser_id = $user->employee->employee_id;
        }

        if (blank($appraser_id)) {
            return back()->with("success", "Un able to assign you a supervisor!");
        }
        $data = [
            "appraisal_start_date" => null,
            "appraisal_end_date" => null,
            'employee_id' => auth()->user()->employee->employee_id,
            "appraiser_id" => $appraser_id,
            "if_no_job_compatibility" => null,
            "unanticipated_constraints" => null,
            "personal_initiatives" => null,
            "training_support_needs" => null,
            "suggestions" => null,
            "appraisal_period_rate" => [
                [
                    "planned_activity" => null,
                    "output_results" => null,
                    "supervisee_score" => null,
                    "superviser_score" => null,
                ]
            ],
            "personal_attributes_assessment" => [
                "technical_knowledge" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "commitment" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "team_work" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "productivity" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "integrity" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "flexibility" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "attendance" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "appearance" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "interpersonal" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "initiative" => [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ]
            ],
            "performance_planning" => [
                [
                    "description" => null,
                    "performance_target" => null,
                    "target_date" => null,
                ]
            ],
            "employee_strength" => null,
            "employee_improvement" => null,
            "superviser_overall_assessment" => null,
            "recommendations" => null,
            "panel_comment" => null,
            "panel_recommendation" => null,
            "overall_assessment" => null,
            "executive_secretary_comments" => null,
            'is_draft' => true,
            'current_stage' => 'Staff',
        ];

        $appraisal = Appraisal::create($data);

        // $employeeAppraisor = \App\Models\Employee::withoutGlobalScope(EmployeeScope::class)
        //     ->find($appraisal->appraiser_id);

        // $employeeAppraisee = \App\Models\Employee::withoutGlobalScope(EmployeeScope::class)
        //     ->where('email', auth()->user()->email)->first();

        // $appraisorUser = User::find($employeeAppraisor->user_id);
        // if ($appraisorUser) {
        //     Notification::send($appraisorUser, new AppraisalApplication($appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name));
        // }

        // $hrUser = User::role('HR')->first();
        // if ($hrUser) {
        //     Notification::send($hrUser, new AppraisalApplication($appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name));
        // }

        // $esUser = User::role('Executive Secretary')->first();
        // if ($esUser) {
        //     Notification::send($esUser, new AppraisalApplication($appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name));
        // }

        // add to appraisal drafts using query builder
        DB::table('appraisal_drafts')->insert([
            'appraisal_id' => $appraisal->appraisal_id,
            'employee_id' => $appraisal->employee_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('uncst-appraisals.edit', ['uncst_appraisal' => $appraisal->appraisal_id]);
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $data = $request->all();

        $data['employee_id'] = auth()->user()->employee->employee_id;
        Appraisal::create($data);

        return redirect()->back()->with('success', 'Appraisal submitted successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Appraisal $appraisal)
    {
        return to_route('uncst-appraisals.edit', ['appraisal' => $appraisal->appraisal_id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appraisal $uncst_appraisal)
    {
        $appraisal = $uncst_appraisal;
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Head of Division');
        })->orWhereHas('roles', function ($query) {
            $query->where('name', 'Executive Secretary');
        })->orWhereHas('roles', function ($query) {
            $query->where('name', 'HR');
        })->get();

        // get any draft with this appraisal and logged in employee
        $draft = DB::table('appraisal_drafts')
            ->where('appraisal_id', $appraisal->appraisal_id)
            ->where('employee_id', auth()->user()->employee->employee_id)
            ->first();

        if ($uncst_appraisal->contract_id != null) {
            $expiredContract = Contract::find($uncst_appraisal->contract_id);
        } else {
            $contractAppraisals = Appraisal::where('employee_id', $appraisal->employee_id)->whereNotNull('contract_id')->pluck('contract_id')->toArray();
            // Get the most recent contract for the user that has not been appraised
            $expiredContract = Contract::where('employee_id', $appraisal->employee_id)
                // ->wherePast('end_date')
                ->whereNotIn('id', $contractAppraisals)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        return view('appraisals.edit', compact('appraisal', 'users', 'expiredContract', 'draft'));
    }


    public function previewAppraisalDetails(Appraisal $appraisal)
    {
        $users = User::whereHas('employee')->get();

        // Load the appraisal with its related employee and appraiser
        $appraisal->load(['employee', 'appraiser']);

        return view('appraisals.appraisal_preview', compact('appraisal', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appraisal $uncst_appraisal)
    {
        ini_set('max_execution_time', 1200);
        ini_set('memory_limit', '-1');
        $requestedData = $request->all();

        // Handle conditional nullifications
        if (!empty($requestedData['review_type']) && $requestedData['review_type'] !== 'end_of_contract') {
            $requestedData['contract_id'] = null;
        }

        if (!empty($requestedData['review_type_other']) && ($requestedData['review_type'] != 'other')) {
            $requestedData['review_type_other'] = null;
        }

        // Default message
        $message = "Appraisal has been submitted successfully.";

        // Handle relevant documents - FIXED VERSION
        if (isset($requestedData['relevant_documents']) && is_array($requestedData['relevant_documents'])) {
            foreach ($requestedData['relevant_documents'] as $key => $value) {
                // Ensure $value is an array
                if (!is_array($value)) {
                    continue;
                }

                // Handle file uploads
                if ($request->hasFile("relevant_documents.$key.proof")) {
                    $file = $request->file("relevant_documents.$key.proof");

                    if ($file && $file->isValid()) {
                        $filePath = $file->store('relevant_documents', 'public');
                        $requestedData['relevant_documents'][$key]['proof'] = $filePath;

                        // Also store the original file name
                        $requestedData['relevant_documents'][$key]['original_name'] = $file->getClientOriginalName();

                        // Debug logging
                        \Log::info('File uploaded successfully', [
                            'original_name' => $file->getClientOriginalName(),
                            'stored_path' => $filePath,
                            'full_path' => storage_path('app/public/' . $filePath),
                            'file_exists' => file_exists(storage_path('app/public/' . $filePath))
                        ]);
                    } else {
                        \Log::error('Invalid file upload', [
                            'key' => $key,
                            'file_error' => $file ? $file->getError() : 'No file'
                        ]);
                    }

                } else {
                    // Keep existing file if no new file uploaded
                    if (isset($uncst_appraisal['relevant_documents'][$key]['proof'])) {
                        $requestedData['relevant_documents'][$key]['proof'] = $uncst_appraisal['relevant_documents'][$key]['proof'];
                    }

                    // Keep existing original name if it exists
                    if (isset($uncst_appraisal['relevant_documents'][$key]['original_name'])) {
                        $requestedData['relevant_documents'][$key]['original_name'] = $uncst_appraisal['relevant_documents'][$key]['original_name'];
                    }
                }
            }
        } else {
            // If no relevant_documents in request, keep existing ones
            $requestedData['relevant_documents'] = $uncst_appraisal['relevant_documents'] ?? [];
        }

        /**
         * ✅ Handle submission (new or resubmission)
         */
        $isSubmission = $request->has('submit') ||
            ($request->has('is_draft') && $requestedData['is_draft'] === 'not_draft');

        $previousStage = $uncst_appraisal->current_stage;

        // ✅ FIX: Correct stage determination based on your workflow
        $appraiser = Employee::find($uncst_appraisal->appraiser_id);
        $appraiserUser = $appraiser ? User::find($appraiser->user_id) : null;
        $appraiserRole = $appraiserUser ? $this->appraisalService->getUserRoleForApproval($appraiserUser) : null;

        $appraisee = $uncst_appraisal->employee;
        $appraiseeUser = $appraisee->user ?? null;

        // Determine the correct first stage
        $firstStage = $this->getSubmissionStage($uncst_appraisal);

        // Check if this is a resubmission after rejection
        $status = $uncst_appraisal->appraisal_request_status ?? [];
        $hasRejection = !empty($status) && collect($status)->contains(fn($s) => $s === 'rejected');

        // Handle draft logic
        if (isset($requestedData['is_draft'])) {
            if ($requestedData['is_draft'] === 'not_draft') {
                // Final submission - Handle resubmission logic
                if ($hasRejection || $uncst_appraisal->is_draft) {
                    // Simple - treat as fresh submission
                    $uncst_appraisal->rejection_reason = null;
                    $uncst_appraisal->resubmission_notes = $request->input('resubmission_notes', null);
                    $uncst_appraisal->resubmitted_at = now();

                    // Restart workflow
                    $uncst_appraisal->appraisal_request_status = [];
                    $uncst_appraisal->is_draft = false;

                    // Set the correct initial stage based on appraisee role
                    $appraisee = $uncst_appraisal->employee;
                    $appraiseeUser = $appraisee?->user;

                    if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
                        $uncst_appraisal->current_stage = 'Head of Division';
                    } else {
                        $uncst_appraisal->current_stage = 'Staff';
                    }

                    $uncst_appraisal->save();

                    // Update draft table
                    DB::table('appraisal_drafts')
                        ->where('appraisal_id', $uncst_appraisal->appraisal_id)
                        ->where('employee_id', auth()->user()->employee->employee_id)
                        ->update(['is_submitted' => true, 'updated_at' => now()]);

                    // Determine who to notify based on the new current stage
                    $nextStage = $uncst_appraisal->current_stage;

                    // Log the action
                    $actionType = $hasRejection ? AppraisalHistory::ACTION_RESUBMITTED : AppraisalHistory::ACTION_SUBMITTED;
                    $actionMessage = $hasRejection ? 'Appraisal resubmitted after addressing rejection feedback' : 'Appraisal submitted';

                    // Log
                    AppraisalHistory::logAction(
                        $uncst_appraisal->appraisal_id,
                        $actionType,
                        $previousStage,
                        $nextStage,
                        $actionMessage
                    );

                    // Notifications - only notify the next approver, not everyone
                    $this->sendSubmissionNotifications($uncst_appraisal, $nextStage);

                    $message = $hasRejection ?
                        "Appraisal resubmitted successfully and sent for review." :
                        "Appraisal submitted successfully.";

                } else {
                    // Normal submission flow - NO auto-approvals
                    $uncst_appraisal->appraisal_request_status = []; // Clear previous status
                    // Set the correct initial stage based on appraisee role
                    $appraisee = $uncst_appraisal->employee;
                    $appraiseeUser = $appraisee?->user;

                    if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
                        $uncst_appraisal->current_stage = 'Head of Division';
                    } else {
                        $uncst_appraisal->current_stage = 'Staff';
                    }
                    $uncst_appraisal->is_draft = false;
                    $uncst_appraisal->save();

                    DB::table('appraisal_drafts')
                        ->where('appraisal_id', $uncst_appraisal->appraisal_id)
                        ->where('employee_id', auth()->user()->employee->employee_id)
                        ->update(['is_submitted' => true]);

                    // Notifications - only notify the next approver
                    $this->sendSubmissionNotifications($uncst_appraisal, $uncst_appraisal->current_stage);

                    $message = "Appraisal submitted successfully.";
                }
            } elseif ($requestedData['is_draft'] === 'draft') {
                // Save as draft
                $uncst_appraisal->appraisal_request_status = $uncst_appraisal->appraisal_request_status ?? [];
                $uncst_appraisal->is_draft = true;
                $uncst_appraisal->save();

                // Check if draft already exists
                $draftExists = DB::table('appraisal_drafts')
                    ->where('appraisal_id', $uncst_appraisal->appraisal_id)
                    ->where('employee_id', auth()->user()->employee->employee_id)
                    ->exists();

                if (!$draftExists) {
                    DB::table('appraisal_drafts')->insert([
                        'appraisal_id' => $uncst_appraisal->appraisal_id,
                        'employee_id' => auth()->user()->employee->employee_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                AppraisalHistory::logAction(
                    $uncst_appraisal->appraisal_id,
                    AppraisalHistory::ACTION_EDITED,
                    $uncst_appraisal->current_stage,
                    $uncst_appraisal->current_stage,
                    'Appraisal saved as draft'
                );

                $message = "Appraisal saved as draft successfully.";
            }

            unset($requestedData['is_draft']); // Remove flag before update
        } else {
            // If is_draft is not set, treat it as a normal submission
            // Handle notifications based on current user role
            $currentUser = auth()->user();

            if ($currentUser->hasRole('Staff')) {
                // Staff submission - notify their appraiser (HOD)
                $employeeAppraiser = Employee::withoutGlobalScope(EmployeeScope::class)
                    ->find($uncst_appraisal->appraiser_id);

                $employeeAppraisee = Employee::withoutGlobalScope(EmployeeScope::class)
                    ->where('email', $currentUser->email)->first();

                if ($employeeAppraiser && $employeeAppraisee && $employeeAppraiser->user) {
                    Notification::send(
                        $employeeAppraiser->user,
                        new AppraisalApplication($uncst_appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name)
                    );
                }
            }

            // Normal submission without draft flag
            DB::table('appraisal_drafts')
                ->where('appraisal_id', $uncst_appraisal->appraisal_id)
                ->where('employee_id', auth()->user()->employee->employee_id)
                ->update(['is_submitted' => true]);

            $uncst_appraisal->is_draft = false;
            $uncst_appraisal->save();

            $message = "Appraisal submitted successfully.";
        }

        // ✅ Finally update remaining fields (excluding appraisal_request_status and current_stage if they were set above)
        $fieldsToExclude = ['appraisal_request_status', 'current_stage', 'is_draft', 'rejection_reason', 'resubmission_notes', 'resubmitted_at'];
        $updateData = array_diff_key($requestedData, array_flip($fieldsToExclude));

        if (!empty($updateData)) {
            $uncst_appraisal->update($updateData);
        }

        return redirect()->back()->with('success', $message);
    }


    protected function getSubmissionStage(Appraisal $appraisal): string
    {
        // Simple logic: determine stage based on appraisee role
        $appraisee = $appraisal->employee;
        $appraiseeUser = $appraisee?->user;

        if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
            return 'Head of Division'; // HOD appraisee starts with HOD stage
        }

        return 'Staff'; // Regular staff starts with Staff stage
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appraisal $uncst_appraisal)
    {
        $uncst_appraisal->delete();
        return to_route('uncst-appraisals.index');
    }

    // In AppraisalsController.php, add this method:
    protected function canBeReviewed(Appraisal $appraisal): bool
    {
        $status = $appraisal->appraisal_request_status ?? [];

        // Check if current user's role has already made a decision
        $user = auth()->user();
        $userRole = $this->getUserRoleFromUser($user);

        if (isset($status[$userRole])) {
            // This role has already made a decision
            return false;
        }

        // Check if appraisal is at the correct stage for this user
        return $appraisal->current_stage === $userRole;
    }

    /**
     * Withdraw an appraisal before it's viewed by the appraiser
     */
    public function withdraw(Appraisal $appraisal)
    {

        \Log::debug('Withdrawal debug info:', $appraisal->withdrawal_debug_info);

        $user = auth()->user();

        if ($user->employee->employee_id !== $appraisal->employee_id) {
            return back()->with('error', 'You can only withdraw your own appraisals.');
        }

        // Check if appraisal can be withdrawn using the model's attribute
        if (!$appraisal->can_be_withdrawn) {
            return back()->with('error', 'Cannot withdraw appraisal. It may have already been viewed by the appraiser.');
        }

        try {
            \Log::info('Attempting to withdraw appraisal', [
                'appraisal_id' => $appraisal->appraisal_id,
                'user_id' => $user->id,
                'employee_id' => $user->employee->employee_id
            ]);

            // Use the model's withdraw method
            $appraisal->withdraw($user);

            \Log::info('Appraisal withdrawn successfully', [
                'appraisal_id' => $appraisal->appraisal_id
            ]);

            // Log the withdrawal action
            AppraisalHistory::logAction(
                $appraisal->appraisal_id,
                AppraisalHistory::ACTION_WITHDRAWN,
                null,
                null,
                'Appraisal withdrawn by appraisee'
            );

            // Notify relevant users about withdrawal
            $this->notifyAppraisalWithdrawal($appraisal);

            return back()->with('success', 'Appraisal withdrawn successfully. You can edit and resubmit it later.');
        } catch (\Exception $e) {
            \Log::error('Error withdrawing appraisal', [
                'appraisal_id' => $appraisal->appraisal_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error withdrawing appraisal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notifications to the appropriate next approver only
     */
    protected function sendSubmissionNotifications(Appraisal $appraisal, string $nextStage)
    {
        $appraisee = $appraisal->employee;

        if (!$appraisee) {
            return;
        }

        // Notify the next approver based on the stage
        $nextApprovers = User::role($nextStage)->get();

        foreach ($nextApprovers as $nextApprover) {
            Notification::send(
                $nextApprover,
                new AppraisalApplication(
                    $appraisal,
                    $appraisee->first_name ?? '',
                    $appraisee->last_name ?? ''
                )
            );
        }

        // Also notify the appraiser specifically if they're different from the next stage approver
        $appraiser = Employee::find($appraisal->appraiser_id);
        if ($appraiser && $appraiser->user) {
            $appraiserRole = $this->getUserRoleFromUser($appraiser->user);
            if ($appraiserRole !== $nextStage) {
                Notification::send(
                    $appraiser->user,
                    new AppraisalApplication(
                        $appraisal,
                        $appraisee->first_name ?? '',
                        $appraisee->last_name ?? ''
                    )
                );
            }
        }
    }

    public function approveOrReject(Request $request, Appraisal $appraisal)
    {

        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        $isHR = $appraisal->appraiser_id == auth()->user()->employee->employee_id;

        // Retrieve current leave_request_status (it will be an array due to casting)
        $appraisalRequestStatus = $appraisal->appraisal_request_status ?: []; // Default to an empty array if null

        // Update appraisal request based on the user's role and the input status
        if ($user->hasRole('HR')) {
            if ($request->input('status') === 'approved') {
                // Set HR status to approved
                $appraisalRequestStatus['HR'] = 'approved';
                if ($isHR) {
                    // Approve for Head of Division too if HR is also the appraiser
                    $appraisalRequestStatus['Head of Division'] = 'approved';
                }
                $appraisal->rejection_reason = null;
            } else {
                $appraisalRequestStatus['HR'] = 'rejected';
                if ($isHR) {
                    // Reject for Head of Division too if HR is also the appraiser
                    $appraisalRequestStatus['Head of Division'] = 'rejected';
                }
                $appraisal->rejection_reason = $request->input('reason');

                // Reset to draft for fresh resubmission
                $this->resetToDraftForResubmission($appraisal);
            }

        } elseif ($user->hasRole('Head of Division')) {
            if ($request->input('status') === 'approved') {
                // Set Head of Division status to approved
                $appraisalRequestStatus['Head of Division'] = 'approved';
                $appraisal->rejection_reason = null; // Clear reason if approved
            } else {
                // Set Head of Division status to rejected
                $appraisalRequestStatus['Head of Division'] = 'rejected';
                $appraisal->rejection_reason = $request->input('reason'); // Store rejection reason

                // Reset to draft for fresh resubmission
                $this->resetToDraftForResubmission($appraisal);
            }

        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                // Set leave status as approved for Executive Secretary
                $appraisalRequestStatus['Executive Secretary'] = 'approved';
                $appraisal->rejection_reason = null; // Clear reason if approved

                $appraisal->appraisal_request_status = $appraisalRequestStatus;
                $appraisal->save();
            } else {
                // Set rejection status
                $appraisalRequestStatus['Executive Secretary'] = 'rejected';
                $appraisal->rejection_reason = $request->input('reason'); // Store rejection reason

                // Save the rejection status first
                $appraisal->appraisal_request_status = $appraisalRequestStatus;
                $appraisal->save();

                // Reset to draft for fresh resubmission
                $this->resetToDraftForResubmission($appraisal);
            }

            // Get the user who requested the appraisal
            $employee = Employee::withoutGlobalScope(EmployeeScope::class)->find($appraisal->employee_id);

            if ($employee && $employee->user_id) {
                $appraisalRequester = User::find($employee->user_id);
                if ($appraisalRequester) {
                    Notification::send($appraisalRequester, new AppraisalApproval($appraisal, $employee->first_name, $employee->last_name));
                }
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // ✅ Save rejection reason FIRST before updating status
        if ($request->input('status') === 'rejected') {
            $appraisal->rejection_reason = $request->input('reason');
        } else {
            $appraisal->rejection_reason = null;
        }


        $appraisal->appraisal_request_status = $appraisalRequestStatus;
        $appraisal->save();

        $message = $request->input('status') === 'approved'
            ? 'Appraisal application approved successfully.'
            : 'Appraisal application rejected successfully.';


        return response()->json(['message' => $message, 'status' => $appraisal->approval_status]);
    }


    /**
     * Reset appraisal to draft status for fresh resubmission (for both withdrawal and rejection)
     */
    protected function resetToDraftForResubmission(Appraisal $appraisal): void
    {
        // Clear approval status but keep rejection reason for reference
        $appraisal->appraisal_request_status = [];


        // Mark as draft
        $appraisal->is_draft = true;

        // Reset to appropriate initial stage based on appraisee role
        $appraisee = $appraisal->employee;
        $appraiseeUser = $appraisee?->user;

        if ($appraiseeUser && $appraiseeUser->hasRole('Head of Division')) {
            $appraisal->current_stage = 'Head of Division';
        } else {
            $appraisal->current_stage = 'Staff';
        }

        // Mark draft as not submitted
        DB::table('appraisal_drafts')
            ->where('appraisal_id', $appraisal->appraisal_id)
            ->where('employee_id', $appraisal->employee_id)
            ->update(['is_submitted' => false, 'updated_at' => now()]);

        $appraisal->save();

        // Log the reset action
        AppraisalHistory::logAction(
            $appraisal->appraisal_id,
            AppraisalHistory::ACTION_RESET_TO_DRAFT,
            null,
            $appraisal->current_stage,
            'Appraisal reset to draft for fresh resubmission'
        );
    }


    /**
     * Advance appraisal to next stage based on current approval
     */
    protected function advanceAppraisalStage(Appraisal $appraisal, string $approvedByRole)
    {
        $appraisee = $appraisal->employee;
        $appraiseeUser = $appraisee->user ?? null;
        $isAppraiseeHod = $appraiseeUser && $appraiseeUser->hasRole('Head of Division');

        $nextStage = null;

        if ($isAppraiseeHod) {
            // HOD appraisee: Staff → Executive Secretary (skip HR)
            if ($approvedByRole === 'Staff') {
                $nextStage = 'Executive Secretary';
            }
        } else {
            // Regular staff: Staff → Head of Division → HR → Executive Secretary
            switch ($approvedByRole) {
                case 'Staff':
                    $nextStage = 'Head of Division';
                    break;
                case 'Head of Division':
                    $nextStage = 'HR';
                    break;
                case 'HR':
                    $nextStage = 'Executive Secretary';
                    break;
                case 'Executive Secretary':
                    $nextStage = 'Completed';
                    $appraisal->approval_status = 'approved';
                    break;
            }
        }

        if ($nextStage) {
            $appraisal->current_stage = $nextStage;
            $appraisal->save();
        }
    }

    /**
     * Get next approver role
     */
    protected function getNextApproverRole(Appraisal $appraisal): ?string
    {
        if ($appraisal->current_stage === 'Completed') {
            return null;
        }

        return $appraisal->current_stage;
    }


    /**
     * Resubmit a rejected appraisal
     */
    public function resubmit(Request $request, Appraisal $appraisal)
    {
        $user = auth()->user();


        // Determine the appropriate stage to resubmit to
        $resubmitStage = $this->getResubmissionStage($appraisal);

        // Update appraisal for resubmission
        $appraisal->update([
            'is_draft' => false,
            'appraisal_request_status' => [], // Clear previous status for fresh review
            'resubmission_notes' => $request->input('resubmission_notes'),
            'resubmitted_at' => now(),
            'rejection_reason' => null, // Clear rejection reason
        ]);

        // Update draft status
        DB::table('appraisal_drafts')
            ->where('appraisal_id', $appraisal->appraisal_id)
            ->where('employee_id', $user->employee->employee_id)
            ->update(['is_submitted' => true]);

        // Log the resubmission action
        AppraisalHistory::logAction(
            $appraisal->appraisal_id,
            AppraisalHistory::ACTION_RESUBMITTED,
            'Staff', // From appraisee status
            $resubmitStage,
            'Appraisal resubmitted after rejection: ' . $request->input('resubmission_notes', 'No notes provided')
        );

        // Notify relevant users about resubmission
        $this->notifyAppraisalResubmission($appraisal);

        return back()->with('success', 'Appraisal resubmitted successfully.');
    }

    /**
     * Determine the appropriate stage to resubmit to
     */
    protected function getResubmissionStage(Appraisal $appraisal): string
    {
        $status = $appraisal->appraisal_request_status ?? [];

        // Find which stage rejected the appraisal
        $rejectingStage = collect($status)->search(fn($s) => $s === 'rejected');

        if ($rejectingStage) {
            return $rejectingStage;
        }


        // Get appraiser details
        $appraiser = Employee::find($appraisal->appraiser_id);
        $appraiserUser = $appraiser ? User::find($appraiser->user_id) : null;
        $appraiserRole = $appraiserUser ? $this->getUserRoleFromUser($appraiserUser) : null;

        // Get appraisee details
        $appraisee = $appraisal->employee;
        $appraiseeUser = $appraisee->user ?? null;
        $isAppraiseeHod = $appraiseeUser && $appraiseeUser->hasRole('Head of Division');

        if ($isAppraiseeHod) {
            return 'Executive Secretary';
        }
        // For regular staff, determine stage based on appraiser's role
        if ($appraiserRole === 'HR') {
            return 'HR';
        } elseif ($appraiserRole === 'Head of Division') {
            return 'Head of Division';
        } elseif ($appraiserRole === 'Executive Secretary') {
            return 'Executive Secretary';
        }

        // Default fallback - should not normally happen
        return 'Head of Division';
    }

    protected function determineResubmissionStage(Appraisal $appraisal): string
    {
        // Get appraiser details
        $appraiser = Employee::find($appraisal->appraiser_id);
        $appraiserUser = $appraiser ? User::find($appraiser->user_id) : null;
        $appraiserRole = $appraiserUser ? $this->getUserRoleFromUser($appraiserUser) : null;

        // Get appraisee details
        $appraisee = $appraisal->employee;
        $appraiseeUser = $appraisee->user ?? null;
        $isAppraiseeHod = $appraiseeUser && $appraiseeUser->hasRole('Head of Division');

        if ($isAppraiseeHod) {
            // HOD appraisee goes directly to Executive Secretary
            return 'Executive Secretary';
        }

        // Regular staff: determine stage based on appraiser role
        return match ($appraiserRole) {
            'HR' => 'HR',
            'Executive Secretary' => 'Executive Secretary',
            default => 'Head of Division', // Default for HOD and others
        };
    }

    /**
     * Get user role from User model (helper method)
     */
    protected function getUserRoleFromUser(User $user): string
    {
        $roles = $user->getRoleNames();
        return $roles->first() ?? 'Unknown';
    }
    /**
     * Notify users about appraisal withdrawal
     */
    protected function notifyAppraisalWithdrawal(Appraisal $appraisal): void
    {
        $appraisee = $appraisal->employee;

        // Notify the appraiser
        $appraiser = Employee::withoutGlobalScope(EmployeeScope::class)
            ->find($appraisal->appraiser_id);

        if ($appraiser && $appraiser->user) {
            Notification::send(
                $appraiser->user,
                new AppraisalWithdrawn($appraisal, $appraisee->first_name, $appraisee->last_name)
            );
        }

        // Notify HR if applicable
        $hrUsers = User::role('HR')->get();
        foreach ($hrUsers as $hrUser) {
            Notification::send(
                $hrUser,
                new AppraisalWithdrawn($appraisal, $appraisee->first_name, $appraisee->last_name)
            );
        }
    }

    /**
     * Notify users about appraisal resubmission
     */
    protected function notifyAppraisalResubmission(Appraisal $appraisal): void
    {
        $appraisee = $appraisal->employee;

        // Notify the next approver based on the resubmission stage
        $nextApprovers = User::role($appraisal->current_stage)->get();

        foreach ($nextApprovers as $approver) {
            Notification::send(
                $approver,
                new AppraisalResubmitted($appraisal, $appraisee->first_name, $appraisee->last_name)
            );
        }

        // Also notify HR for visibility
        $hrUsers = User::role('HR')->get();
        foreach ($hrUsers as $hrUser) {
            Notification::send(
                $hrUser,
                new AppraisalResubmitted($appraisal, $appraisee->first_name, $appraisee->last_name)
            );
        }
    }


    /**
     * Send appropriate notifications
     */
    protected function sendAppraisalNotifications(Appraisal $appraisal, string $status, User $approver)
    {
        $appraisee = $appraisal->employee;

        if (!$appraisee || !$appraisee->user) {
            return;
        }

        // Notify appraisee of decision
        Notification::send(
            $appraisee->user,
            new AppraisalApproval(
                $appraisal,
                $status,
                $approver->name,
                $this->appraisalService->getUserRoleForApproval($approver)
            )
        );

        // If approved and not completed, notify next approver
        if ($status === 'approved' && $appraisal->current_stage !== 'Completed') {
            $nextApproverRole = $this->getNextApproverRole($appraisal);

            if ($nextApproverRole) {
                $nextApprovers = User::role($nextApproverRole)->get();

                foreach ($nextApprovers as $nextApprover) {
                    Notification::send(
                        $nextApprover,
                        new AppraisalApplication(
                            $appraisal,
                            $appraisee->first_name,
                            $appraisee->last_name
                        )
                    );
                }
            }
        }
    }


    private function getAttachmentData(Appraisal $appraisal, $index): array
    {
        $documents = $appraisal->relevant_documents ?? [];
        if (!isset($documents[$index])) {
            throw new \Exception("Attachment not found for index: {$index}");
        }

        $document = $documents[$index];
        $filePath = $document['proof'] ?? '';

        if (empty($filePath)) {
            throw new \Exception("No file path found for attachment at index: {$index}");
        }

        // Clean the file path
        $filePath = ltrim($filePath, '/');

        // Get file name from multiple possible sources
        $fileName = $document['original_name'] ??
            $document['name'] ??
            $document['title'] ??
            basename($filePath);

        // Check multiple possible storage locations
        $possiblePaths = [
            storage_path('app/public/' . $filePath),
            storage_path('app/public/relevant_documents/' . basename($filePath)),
            storage_path('app/public/proof_documents/' . basename($filePath)),
            storage_path('app/' . $filePath),
            public_path('storage/' . $filePath),
            public_path('storage/relevant_documents/' . basename($filePath)),
            public_path('storage/proof_documents/' . basename($filePath)),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                return [
                    'path' => $path,
                    'name' => pathinfo($fileName, PATHINFO_FILENAME), // Name without extension
                    'original_name' => $fileName,
                    'size' => filesize($path),
                ];
            }
        }

        // Log the attempted paths for debugging
        \Log::error('File not found in any expected location', [
            'appraisal_id' => $appraisal->appraisal_id,
            'index' => $index,
            'stored_path' => $filePath,
            'attempted_paths' => $possiblePaths,
        ]);

        throw new \Exception("File does not exist on server: {$filePath}. Please check if the file was uploaded correctly.");
    }

    public function downloadPDF(Appraisal $appraisal)
    {
        $users = User::all();

        // Step 1: Generate the main appraisal PDF with dompdf
        $dompdfPdf = Pdf::loadView('appraisals.pdf', compact('appraisal', 'users')) // <-- updated here
            ->setPaper('A4', 'portrait')
            ->output();

        // Save temporary dompdf file
        $mainPdfPath = storage_path("app/temp/appraisal-main-{$appraisal->appraisal_id}.pdf");
        if (!file_exists(dirname($mainPdfPath))) {
            mkdir(dirname($mainPdfPath), 0755, true);
        }
        file_put_contents($mainPdfPath, $dompdfPdf);

        // Step 2: Collect all PDFs (main + PDF attachments)
        $pdfFiles = [$mainPdfPath];
        foreach (($appraisal->relevant_documents ?? []) as $doc) {
            if (!empty($doc['proof'])) {
                $filePath = storage_path('app/public/' . $doc['proof']);
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                if (file_exists($filePath) && $extension === 'pdf') {
                    $pdfFiles[] = $filePath;
                }
            }
        }

        // Step 3: Merge PDFs
        $pdf = new Fpdi();
        foreach ($pdfFiles as $file) {
            $pageCount = $pdf->setSourceFile($file);

            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplIdx);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx);
            }
        }

        $finalFileName = "appraisal-{$appraisal->appraisal_id}-with-attachments.pdf";

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename={$finalFileName}");
    }



    private function authorizeAttachmentAccess($user, Appraisal $appraisal)
    {
        $isAppraiser = $user->employee && $user->employee->employee_id === $appraisal->appraiser_id;
        $isAppraisee = $user->employee && $user->employee->employee_id === $appraisal->employee_id;
        $isHR = $user->hasRole('HR');
        $isExecutiveSecretary = $user->hasRole('Executive Secretary');

        if (!$isAppraiser && !$isAppraisee && !$isHR && !$isExecutiveSecretary) {
            abort(403, 'You do not have permission to access attachments in this appraisal.');
        }
    }


    public function viewAttachment(Appraisal $appraisal, $index)
    {
        try {
            $user = auth()->user();
            $this->authorizeAttachmentAccess($user, $appraisal);

            // Retrieve attachment path and name
            $attachment = $this->getAttachmentData($appraisal, $index);
            $filePath = $attachment['path'];
            $fileName = $attachment['name'];

            // Determine MIME type and extension
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $mimeType = mime_content_type($filePath);

            // Display PDFs inline
            if (strtolower($fileExtension) === 'pdf' || $mimeType === 'application/pdf') {
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '.pdf"'
                ]);
            }

            // Display images inline
            if (strpos($mimeType, 'image/') === 0) {
                return response()->file($filePath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '.' . $fileExtension . '"'
                ]);
            }

            // All other file types: display inline fallback
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '.' . $fileExtension . '"'
            ]);

        } catch (\Exception $e) {
            \Log::error('Attachment view error', [
                'error' => $e->getMessage(),
                'appraisal_id' => $appraisal->id,
                'index' => $index,
                'user_id' => auth()->id()
            ]);

            return back()->withErrors('Could not view the attachment.');
        }
    }


    public function downloadAttachment(Appraisal $appraisal, $index)
    {
        try {
            $user = auth()->user();
            $this->authorizeAttachmentAccess($user, $appraisal);

            // Retrieve attachment path and name
            $attachment = $this->getAttachmentData($appraisal, $index);
            $filePath = $attachment['path'];
            $fileName = $attachment['name'];

            // Get file extension & MIME type
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            $mimeType = mime_content_type($filePath);

            return response()->download($filePath, $fileName . '.' . $fileExtension, [
                'Content-Type' => $mimeType
            ]);

        } catch (\Exception $e) {
            \Log::error('Attachment download error', [
                'error' => $e->getMessage(),
                'appraisal_id' => $appraisal->id,
                'index' => $index,
                'user_id' => auth()->id()
            ]);

            return back()->withErrors('Could not download the attachment.');
        }
    }


    public function downloadAllAttachments(Appraisal $appraisal)
    {
        try {
            $user = auth()->user();
            $this->authorizeAttachmentAccess($user, $appraisal);

            $documents = $appraisal->relevant_documents ?? [];

            if (empty($documents)) {
                return back()->with('error', 'No attachments found for this appraisal.');
            }

            // Create temporary ZIP file
            $zipFileName = "appraisal-{$appraisal->appraisal_id}-attachments-" . now()->format('Y-m-d-H-i-s') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            if (!file_exists(dirname($zipPath))) {
                mkdir(dirname($zipPath), 0755, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
                return back()->with('error', 'Could not create ZIP file.');
            }

            foreach ($documents as $index => $document) {
                try {
                    $attachment = $this->getAttachmentData($appraisal, $index);
                    $filePath = $attachment['path'];
                    $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $attachment['name']);

                    $zip->addFile($filePath, $fileName);
                } catch (\Exception $e) {
                    // Skip missing or inaccessible files
                    continue;
                }
            }

            if ($zip->numFiles === 0) {
                $zip->close();
                return back()->with('error', 'No valid attachments found for download.');
            }

            $zip->close();

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Download all attachments error', [
                'error' => $e->getMessage(),
                'appraisal_id' => $appraisal->id,
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Could not create attachment archive: ' . $e->getMessage());
        }
    }


    /**
     * Show print preview for appraisal.
     */
    public function printPreview(Appraisal $appraisal)
    {
        $users = User::whereHas('employee')->get();

        // Load the appraisal with its related employee and appraiser
        $appraisal->load(['employee', 'appraiser']);

        // Get attachment access info for the current user
        $user = auth()->user();
        $attachmentAccessInfo = $this->appraisalService->getAttachmentAccessInfo($appraisal, $user);

        return view('appraisals.print-preview', compact('appraisal', 'users', 'attachmentAccessInfo'));
    }

    public function printAppraisal(Appraisal $appraisal)
    {
        $users = User::all();
        return view('appraisals.pdf', compact('appraisal', 'users'));
    }
}