<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\StaffRecruitment;
use App\Http\Requests\StoreStaffRecruitmentRequest;  // The request validation class
use App\Http\Requests\UpdateStaffRecruitmentRequest;
use App\Models\User;
use App\Notifications\StaffRecruitmentApplication;
use App\Notifications\StaffRecruitmentApproval;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
class StaffRecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Department carries a global scope that filters by the CURRENT
        // viewer's own employee-department — right for "which departments
        // can I see" queries, wrong here: this list shows every recruitment
        // request regardless of viewer, so each row needs its own actual
        // department, not one filtered by who's looking.
        $rectrutmentRequests = StaffRecruitment::with(['department' => function ($query) {
            $query->withoutGlobalScopes();
        }])->paginate();

        return view('staff-recruitment.index', compact('rectrutmentRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get departments with department_id as key and department_name as the value
        $departments = Department::pluck('department_name', 'department_id')->toArray();
        return view('staff-recruitment.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRecruitmentRequest $request)
    {

        // Create a new StaffRecruitment entry in the database
        $staffRecruitment = StaffRecruitment::create([
            'position' => $request->input('position'),
            'department_id' => $request->input('department_id'),
            'number_of_staff' => $request->input('number_of_staff'),
            'date_of_recruitment' => $request->input('date_of_recruitment'),
            'sourcing_method' => $request->input('sourcing_method'),
            'employment_basis' => $request->input('employment_basis'),
            'justification' => $request->input('justification'),
            'funding_budget' => $request->input('funding_budget'),
            'user_id' => auth()->user()->id
        ]);

        //get HR users using spatie
        $users = User::role('HR')->get();

        Notification::send($users, new StaffRecruitmentApplication($staffRecruitment));

        // Redirect back with a success message
        return redirect()->route('recruitments.index') // Adjust to the appropriate route
            ->with('success', 'Staff recruitment request has been submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffRecruitment $recruitment)
    {
        $recruitment->load(['department' => function ($query) {
            $query->withoutGlobalScopes();
        }]);
        return view('staff-recruitment.show', compact('recruitment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffRecruitment $recruitment)
    {
        $staffRecruitment = $recruitment;
        // Get departments with department_id as key and department_name as the value
        $departments = Department::pluck('department_name', 'department_id')->toArray();

        return view('staff-recruitment.edit', compact('staffRecruitment', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffRecruitmentRequest $request, StaffRecruitment $recruitment)
    {
        // Once any stage has recorded a decision, the request is in the
        // approval pipeline — editing it afterwards would let its details
        // (or the approval_status itself) be changed out from under an
        // approval that's already been given.
        if (!empty($recruitment->approval_status)) {
            return redirect()->back()->with('error', 'This request has already entered the approval process and can no longer be edited.');
        }

        $recruitment->update($request->validated());

        return redirect()->route('recruitments.index')->with('success', 'Recruitment Request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffRecruitment $recruitment)
    {
        try {
            // Delete the employee record
            $recruitment->delete();

            // Redirect to the employees index with a success message
            return redirect()->route('recruitments.index')->with('success', 'Recruitment Request Deleted');
        } catch (Exception $exception) {
            // Log the error for debugging
            \Log::error('Error deleting recruitment request: ' . $exception->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Problem Deleting the Recruitment Request');
        }
    }

    public function approveOrReject(Request $request, StaffRecruitment $recruitment)
    {
        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();
        $status = $request->input('status');

        $recruitmentRequestStatus = $recruitment->approval_status ?: [];

        if ($user->hasRole('HR')) {
            $recruitmentRequestStatus['HR'] = $status;
            $recruitment->rejection_reason = $status === 'approved' ? null : $request->input('reason');
        } elseif ($user->hasRole('Head of Division')) {
            // Department::query() carries a global scope that filters by the
            // ACTING user's own employee-department — fine for "list the
            // departments I can see", wrong here, where we need this specific
            // recruitment's department regardless of who's asking.
            $department = Department::withoutGlobalScopes()->find($recruitment->department_id);

            // A Head of Division may only decide on requests for their own
            // department — not any division's request.
            if (!$department || (string) $department->department_head !== (string) $user->id) {
                return response()->json(['error' => 'You are not the Head of Division for this request\'s department.'], 403);
            }

            $recruitmentRequestStatus['Head of Division'] = $status;
            $recruitment->rejection_reason = $status === 'approved' ? null : $request->input('reason');
        } elseif ($user->hasRole('Executive Secretary')) {
            $hrApproved = ($recruitmentRequestStatus['HR'] ?? null) === 'approved';
            $hodApproved = ($recruitmentRequestStatus['Head of Division'] ?? null) === 'approved';

            // The Executive Secretary gives the final decision — it can't be
            // given until HR and the Head of Division have both approved.
            // Rejecting is always allowed, since that just ends the request.
            if ($status === 'approved' && !($hrApproved && $hodApproved)) {
                return response()->json([
                    'error' => 'This request cannot be approved yet — it is still awaiting HR and/or Head of Division approval.',
                ], 422);
            }

            $recruitmentRequestStatus['Executive Secretary'] = $status;
            $recruitment->rejection_reason = $status === 'approved' ? null : $request->input('reason');
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $recruitment->approval_status = $recruitmentRequestStatus;
        $recruitment->save();

        $requester = User::find($recruitment->user_id);

        if ($status === 'rejected') {
            // A rejection at any stage ends the request — let the requester
            // know immediately rather than leaving them waiting on a request
            // that's already dead.
            Notification::send($requester, new StaffRecruitmentApproval($recruitment, $user, 'rejected'));
        } elseif ($user->hasRole('Executive Secretary')) {
            Notification::send($requester, new StaffRecruitmentApproval($recruitment, $user, 'approved'));
        } else {
            $this->notifyNextApprover($recruitment, $recruitmentRequestStatus);
        }

        return response()->json(['message' => 'Recruitment application updated successfully.', 'status' => $recruitment->approval_status]);
    }

    /**
     * Once HR and the Head of Division have each recorded a decision, let
     * whoever's turn it is next know the request is waiting on them —
     * otherwise it only ever surfaces if they think to check the list.
     */
    private function notifyNextApprover(StaffRecruitment $recruitment, array $statuses): void
    {
        $hrApproved = ($statuses['HR'] ?? null) === 'approved';
        $hodApproved = ($statuses['Head of Division'] ?? null) === 'approved';

        if ($hrApproved && !$hodApproved) {
            $department = Department::withoutGlobalScopes()->find($recruitment->department_id);
            $hod = $department?->department_head
                ? User::find($department->department_head)
                : null;

            if ($hod && $hod->hasRole('Head of Division')) {
                Notification::send($hod, new StaffRecruitmentApplication($recruitment));
            }
        }

        if ($hrApproved && $hodApproved && !isset($statuses['Executive Secretary'])) {
            $executiveSecretaries = User::role('Executive Secretary')->get();
            Notification::send($executiveSecretaries, new StaffRecruitmentApplication($recruitment));
        }
    }
}
