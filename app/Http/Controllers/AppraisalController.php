<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;

use App\Models\User;
use App\Notifications\AppraisalApproval;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Notification;

use Barryvdh\DomPDF\Facade\Pdf;

class AppraisalController extends Controller
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->employee) {
            return back()->with("success", "No Employee record found! Ask the human resource");
        }
        if (!auth()->user()->employee->department) {
            return back()->with("success", "Your department does not have a department head, so we cant determine a supervisor for you!.");
        }
        $data =  [
            "appraisal_start_date" => null,
            "appraisal_end_date" => null,
            'employee_id' => auth()->user()->employee->employee_id,
            "appraiser_id" => User::find(auth()->user()->employee->department->department_head)->employee->employee_id,
            "appraisal_period_accomplishment" => [
                [
                    "planned_activity" => null,
                    "output_results" => null,
                    "remarks" => null,
                ]
            ],
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
        ];

        $appraisal = Appraisal::create($data);

        return to_route('appraisals.edit', ['appraisal' => $appraisal->appraisal_id]);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appraisal $appraisal)
    {
        $users = User::whereHas('employee')->get();

        return view('appraisals.edit', compact('appraisal', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appraisal $appraisal)
    {
        $appraisal->update($request->all());

        return redirect()->back()->with('success', 'Appraisal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Appraisal $appraisal)
    {
        $appraisal->delete();
        return to_route('appraisals.index');
    }

    public function survey(Request $request)
    {
        //appraisal type
        $type = 'appraisal';
        //get the form
        $form = Form::where('uuid', '6156ada8-c020-4cdf-af47-9d6eaf1dd16c')->firstOrFail();

        // Load sections with their related fields
        $form->load(['sections.fields']);

        return view('appraisals.form', compact('form'));
    }


    public function approveOrReject(Request $request, Appraisal $appraisal)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Get the current appraisal status array (or initialize if empty)
        $appraisalRequestStatus = $appraisal->appraisal_request_status ?? [];

        // Update based on role
        if ($user->hasRole('HR')) {
            $appraisalRequestStatus['HR'] = $request->input('status');
        } elseif ($user->hasRole('Head of Division')) {
            $appraisalRequestStatus['Head of Division'] = $request->input('status');
        } elseif ($user->hasRole('Executive Secretary')) {
            $appraisalRequestStatus['Executive Secretary'] = $request->input('status');
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Set rejection reason if status is "rejected"
        if ($request->input('status') === 'rejected') {
            $appraisal->rejection_reason = $request->input('reason');
        } else {
            $appraisal->rejection_reason = null; // Clear reason if approved
        }

        // Save the updated status array
        $appraisal->appraisal_request_status = $appraisalRequestStatus;
        $appraisal->save();

        // Send notification only if Executive Secretary action
        if ($user->hasRole('Executive Secretary')) {
            $appraisalRequester = User::find($appraisal->employee->user_id); // No ->first()
            Notification::send($appraisalRequester, new AppraisalApproval(
                $appraisal,
                $appraisalRequester->employee->first_name,
                $appraisalRequester->employee->last_name
            ));
        }

        return response()->json([
            'message' => 'Appraisal status updated successfully.',
            'status' => $appraisal->appraisal_request_status
        ]);
    }
    public function downloadPDF(Appraisal $appraisal)
    {
        $users = User::all();
        $pdf = PDf::loadView('appraisals.pdf', compact('appraisal', 'users'));
        return $pdf->download("appraisal-{$appraisal->appraisal_id}.pdf");
    }
}
