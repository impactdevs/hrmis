<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\User;
use App\Notifications\SalaryAdvanceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;


class SalaryAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salarydvances = SalaryAdvance::paginate();
        return view('salary-advances.index', compact('salarydvances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = auth()->user()->getRoleNames()->first();
        
        return view('salary-advances.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount_applied_for' => 'required|numeric|min:1',
            'reasons' => 'required|string',
            'repayment_start_date' => 'required|date|after_or_equal:today',
            'repayment_end_date' => 'required|date|after:repayment_start_date',
            'date_of_contract_expiry' => 'nullable|date|after_or_equal:today',
            'net_monthly_pay' => 'nullable|numeric|min:0',
            'outstanding_loan' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        $salaryAdvance = new SalaryAdvance();
        $salaryAdvance->employee_id = auth()->user()->employee->employee_id;
        $salaryAdvance->amount_applied_for = $validated['amount_applied_for'];
        $salaryAdvance->reasons = $validated['reasons'];
        $salaryAdvance->repayment_start_date = $validated['repayment_start_date'];
        $salaryAdvance->repayment_end_date = $validated['repayment_end_date'];
        $salaryAdvance->date_of_contract_expiry = $validated['date_of_contract_expiry'];
        $salaryAdvance->net_monthly_pay = $validated['net_monthly_pay'];
        $salaryAdvance->outstanding_loan = $validated['outstanding_loan'] ?? null;
        $salaryAdvance->comments = $validated['comments'] ?? null;
        $salaryAdvance->loan_request_status = []; // You can replace this with a default status like 'pending'
        $salaryAdvance->save();

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance request submitted successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(SalaryAdvance $salary_advance)
    {
        return view('salary-advances.show', compact('salary_advance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalaryAdvance $salary_advance)
    {
         $role = auth()->user()->getRoleNames()->first();
        return view('salary-advances.edit', compact('salary_advance', 'role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalaryAdvance $salary_advance)
    {
        $validated = $request->validate([
            'amount_applied_for' => 'required|numeric|min:1',
            'reasons' => 'required|string',
            'repayment_start_date' => 'required|date|after_or_equal:today',
            'repayment_end_date' => 'required|date|after:repayment_start_date',
            'date_of_contract_expiry' => 'nullable|date|after_or_equal:today',
            'net_monthly_pay' => 'nullable|numeric|min:0',
            'outstanding_loan' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        $salary_advance->amount_applied_for = $validated['amount_applied_for'];
        $salary_advance->reasons = $validated['reasons'];
        $salary_advance->repayment_start_date = $validated['repayment_start_date'];
        $salary_advance->repayment_end_date = $validated['repayment_end_date'];
        $salary_advance->date_of_contract_expiry = $validated['date_of_contract_expiry'];
        $salary_advance->net_monthly_pay = $validated['net_monthly_pay'];
        $salary_advance->outstanding_loan = $validated['outstanding_loan'] ?? null;
        $salary_advance->comments = $validated['comments'] ?? null;
        $salary_advance->save();

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryAdvance $salary_advance)
    {
        $salary_advance->delete();

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance request deleted successfully.');
    }

    public function approveOrReject(Request $request, SalaryAdvance $salary_advance)
    {

        $request->validate([
            'status' => 'required|string|in:approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Retrieve current loan_request_status (it will be an array due to casting)
        $loanRequestStatus = $salary_advance->loan_request_status ?: []; // Default to an empty array if null

        // Update leave request based on the user's role and the input status
        if ($user->hasRole('HR')) {
            if ($request->input('status') === 'approved') {
                // Set HR status to approved
                $loanRequestStatus['HR'] = 'approved';
                $salary_advance->rejection_reason = null; // Clear reason if approved
            } else {
                // Set HR status to rejected
                $loanRequestStatus['HR'] = 'rejected';
                $salary_advance->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Head of Division')) {
            if ($request->input('status') === 'approved') {
                // Set Head of Division status to approved
                $loanRequestStatus['Head of Division'] = 'approved';
                $salary_advance->rejection_reason = null; // Clear reason if approved
            } else {
                // Set Head of Division status to rejected
                $loanRequestStatus['Head of Division'] = 'rejected';
                $salary_advance->rejection_reason = $request->input('reason'); // Store rejection reason
            }
        } elseif ($user->hasRole('Executive Secretary')) {
            if ($request->input('status') === 'approved') {
                // Set leave status as approved for Executive Secretary
                $loanRequestStatus['Executive Secretary'] = 'approved';
                $salary_advance->rejection_reason = null; // Clear reason if approved
            } else {
                // Set rejection status
                $loanRequestStatus['Executive Secretary'] = 'rejected';
                $salary_advance->rejection_reason = $request->input('reason'); // Store rejection reason
            }

            // Send notification
            $trainingRequester = User::find($salary_advance->user_id); // Get the user who requested the leave
            $approver = User::find(auth()->user()->id);
            Notification::send($trainingRequester, new SalaryAdvanceNotification($salary_advance, $approver, $loanRequestStatus['Executive Secretary']));
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Save the updated loan_request_status
        $salary_advance->loan_request_status = $loanRequestStatus;
        $salary_advance->save();

        return response()->json(['message' => 'Travel Clearance approved successfully.', 'status' => $salary_advance->loan_request_status]);
    }
}
