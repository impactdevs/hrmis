<?php

namespace App\Http\Controllers;

use App\Mail\Summit;
use App\Models\Appraisal;
use App\Models\Contract;
use App\Models\User;
use App\Notifications\AppraisalApproval;
use App\Notifications\AppraisalApplication;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Scopes\EmployeeScope;
use Illuminate\Support\Facades\Notification;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AppraisalsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appraisals = Appraisal::with('employee')->latest()->paginate();
        return view('appraisals.index', compact('appraisals'));
    }

    /**
     * Show the form for creating a new resource controller
     */
    public function create()
    {
        if (!auth()->user()->employee) {
            return back()->with("success", "No Employee record found! Ask the human resource");
        }
        if (!auth()->user()->employee->department) {
            return back()->with("success", "Your department does not have a department head, so we cant determine a supervisor for you!Reach out to the administrator.");
        }
        $data =  [
            "appraisal_start_date" => null,
            "appraisal_end_date" => null,
            'employee_id' => auth()->user()->employee->employee_id,
            "appraiser_id" => User::find(auth()->user()->employee->department->department_head)->employee->employee_id,
            "if_no_job_compatibility" => null,
            "unanticipated_constraints" => null,
            "personal_initiatives" => null,
            "training_support_needs" => null,
            "appraisal_period_rate" => [
                [
                    "planned_activity" => null,
                    "output_results" => null,
                    "supervisee_score" => null,
                    "superviser_score" => null,
                ]
            ],
            "personal_attributes_assessment" => [
                "technical_knowledge" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "commitment" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "team_work" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "productivity" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "integrity" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "flexibility" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "attendance" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "appearance" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "interpersonal" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ],
                "initiative" =>  [
                    "appraisee_score" => null,
                    "appraiser_score" => null,
                    "agreed_score" => null,
                ]
            ],
            "performance_planning" =>  [
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
        $users = User::role('Head of Division')->whereHas('employee')->get();

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
                ->wherePast('end_date')
                ->whereNotIn('id', $contractAppraisals)
                ->orderBy('end_date', 'desc')
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
        $requestedData = $request->all();

        // Handle conditional nullifications
        if (!empty($requestedData['review_type']) && $requestedData['review_type'] !== 'end_of_contract') {
            $requestedData['contract_id'] = null;
        }

        if (!empty($requestedData['review_type_other']) && $requestedData['review_type_other'] !== 'other') {
            $requestedData['review_type_other'] = null;
        }

        // Default message
        $message = "Appraisal has been submitted successfully.";

        // Handle draft logic
        if (isset($requestedData['is_draft'])) {
            if ($requestedData['is_draft'] === 'not_draft') {
                // Final submission, just update is_submitted to true for this draft
                DB::table('appraisal_drafts')
                    ->where('appraisal_id', $uncst_appraisal->appraisal_id)
                    ->where('employee_id', auth()->user()->employee->employee_id)
                    ->update(['is_submitted' => true]);
                $uncst_appraisal->is_draft = false;
                $uncst_appraisal->save();
                $message = "Appraisal submitted successfully.";
            } elseif ($requestedData['is_draft'] === 'draft') {
                // Save as draft
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
                    $message = "Appraisal has been saved as a draft successfully.";
                }
            }

            unset($requestedData['is_draft']); // Remove flag before update
        } else {
            //check the role of the person submitting the appraisal, if its the Staff,
            if (auth()->user()->hasRole('Staff')) {
                $employeeAppraisor = \App\Models\Employee::withoutGlobalScope(EmployeeScope::class)
                    ->find($uncst_appraisal->appraiser_id);

                $employeeAppraisee = \App\Models\Employee::withoutGlobalScope(EmployeeScope::class)
                    ->where('email', auth()->user()->email)->first();

                $appraisorUser = User::find($employeeAppraisor->user_id);
                if ($appraisorUser) {
                    Notification::send($appraisorUser, new AppraisalApplication($uncst_appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name));
                }

                $hrUser = User::role('HR')->first();
                if ($hrUser) {
                    Notification::send($hrUser, new AppraisalApplication($uncst_appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name));
                }

                $esUser = User::role('Executive Secretary')->first();
                if ($esUser) {
                    Notification::send($esUser, new AppraisalApplication($uncst_appraisal, $employeeAppraisee->first_name, $employeeAppraisee->last_name));
                }
            }
            // If is_draft is not set, treat it as a normal submission
            DB::table('appraisal_drafts')
                ->where('appraisal_id', $uncst_appraisal->appraisal_id)
                ->where('employee_id', auth()->user()->employee->employee_id)
                ->update(['is_submitted' => true]);
            $uncst_appraisal->is_draft = false;
            $uncst_appraisal->save();
        }

        // Update appraisal with remaining request data
        $uncst_appraisal->update($requestedData);

        return redirect()->back()->with('success', $message);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appraisal $uncst_appraisal)
    {
        $uncst_appraisal->delete();
        return to_route('uncst-appraisals.index');
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

        // Update leave request based on the user's role and the input status
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
            }
        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                // Set leave status as approved for Executive Secretary
                $appraisalRequestStatus['Executive Secretary'] = 'approved';
                $appraisal->rejection_reason = null; // Clear reason if approved
            } else {
                // Set rejection status
                $appraisalRequestStatus['Executive Secretary'] = 'rejected';
                $appraisal->rejection_reason = $request->input('reason'); // Store rejection reason
            }

            // Get the user who requested the appraisal
            $employee = \App\Models\Employee::withoutGlobalScope(EmployeeScope::class)
                ->find($appraisal->employee_id);

            $appraisalRequester = User::find($employee->user_id); // Removed ->first()

            // Send notification to the User instance
            Notification::send($appraisalRequester, new AppraisalApproval($appraisal, $employee->first_name, $employee->last_name));
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appraisal->appraisal_request_status = $appraisalRequestStatus;
        $appraisal->save();

        return response()->json(['message' => 'Appraisal application approved successfully.', 'status' => $appraisal->leave_request_status]);
    }

    public function downloadPDF(Appraisal $appraisal)
    {
        $users = User::all();
        $pdf = PDf::loadView('appraisals.pdf', compact('appraisal', 'users'));
        return $pdf->download("appraisal-{$appraisal->appraisal_id}.pdf");
    }
}
