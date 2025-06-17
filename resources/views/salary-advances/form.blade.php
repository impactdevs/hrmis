<div class="container">
    <div class="mb-4">

        <div class="card">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>There were some problems with your input:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-header bg-primary text-white">
                <strong>Section 1: Applicant to Fill</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount_applied_for">Amount Applied For</label>
                            <input type="number" class="form-control" name="amount_applied_for" id="amount_applied_for"
                                @if ($role != 'Staff') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif placeholder="Enter Amount Applied For"
                                value="{{ old('amount_applied_for', $salary_advance->amount_applied_for ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reasons">Reason(s)</label>
                            <textarea class="form-control" name="reasons" id="reasons" rows="3"
                                @if ($role != 'Staff') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif>{{ old('reasons', $salary_advance->reasons ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repayment_start_date">Repayment Start Date</label>
                            <input type="date" class="form-control" name="repayment_start_date"
                                id="repayment_start_date" @if ($role != 'Staff') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
                                value="{{ old('repayment_start_date', isset($salary_advance) && $salary_advance->repayment_start_date ? $salary_advance->repayment_start_date->toDateString() : '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="repayment_end_date">Repayment End Date</label>
                            <input type="date" class="form-control" name="repayment_end_date" id="repayment_end_date"
                                @if ($role != 'Staff') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
                                value="{{ old('repayment_end_date', isset($salary_advance) && $salary_advance->repayment_end_date ? $salary_advance->repayment_end_date->toDateString() : '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <strong>Section 2: To be filled by Human Resource Department</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date_of_contract_expiry">Date of contract expiry for the Applicant</label>
                            <input type="date" class="form-control" name="date_of_contract_expiry"
                                @if ($role != 'HR') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif id="date_of_contract_expiry"
                                value="{{ old('date_of_contract_expiry', isset($salary_advance) && $salary_advance->date_of_contract_expiry ? $salary_advance->date_of_contract_expiry->toDateString() : '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="net_monthly_pay">Net Monthly Pay</label>
                            <input type="number" class="form-control" name="net_monthly_pay" id="net_monthly_pay"
                                @if ($role != 'HR') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif placeholder="Enter Net Monthly"
                                value="{{ old('net_monthly_pay', $salary_advance->net_monthly_pay ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <strong>Section 3: To be filled by Finance Department</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="outstanding_loan">Outstanding Salary Advance/Loan if any</label>
                            <input type="number" class="form-control" name="outstanding_loan" id="outstanding_loan"
                                @if ($role != 'Finance Department') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
                                placeholder="Outstanding Salary Advance/Loan if any"
                                value="{{ old('outstanding_loan', $salary_advance->outstanding_loan ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="comments">Comments</label>
                            <textarea class="form-control" name="comments" id="comments" rows="3"
                                @if ($role != 'Finance Department') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif>{{ old('comments', $salary_advance->comments ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="form-group mt-3 d-flex justify-content-between align-items-start">
    <!-- Left side: Submit button -->
    <div class="col-auto">
        <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
    </div>

    <!-- Right side: Approval controls -->
    <div class="col d-flex flex-column align-items-end">
        @can('approve salary advance')
            @if (!is_null($salary_advance))
                <div class="status mb-2 text-end">
                    @php
                        $userRole = Auth::user()->roles->pluck('name')[0];
                        $status = $salary_advance->loan_request_status[$userRole] ?? null;
                    @endphp

                    @if ($status === 'rejected')
                        <span class="badge bg-danger">You rejected this Request</span>
                        <p class="mt-1"><strong>Rejection Reason:</strong> {{ $salary_advance->rejection_reason }}</p>
                    @elseif ($status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </div>

                <div class="form-group text-end">
                    <input class="btn btn-outline-primary approve-btn" value="Approve" type="button"
                        data-training-id="{{ $salary_advance->loan_id }}">
                    <input class="btn btn-outline-danger reject-btn" value="Reject" type="button"
                        data-training-id="{{ $salary_advance->loan_id }}" data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                </div>
            @endif
        @endcan
    </div>
</div>

</div>

<!-- Bootstrap Modal for Rejection Reason -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Training Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="rejectionReason">Please enter the reason for rejection:</label>
                <textarea id="rejectionReason" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
            </div>
        </div>
    </div>
</div>
