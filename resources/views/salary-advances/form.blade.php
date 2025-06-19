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
                                @if ($role != 'Staff') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
                                placeholder="Enter Amount Applied For"
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
                                id="repayment_start_date"
                                @if ($role != 'Staff') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
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
                                @if ($role != 'HR') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
                                id="date_of_contract_expiry"
                                value="{{ old('date_of_contract_expiry', isset($salary_advance) && $salary_advance->date_of_contract_expiry ? $salary_advance->date_of_contract_expiry->toDateString() : '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="net_monthly_pay">Net Monthly Pay</label>
                            <input type="number" class="form-control" name="net_monthly_pay" id="net_monthly_pay"
                                @if ($role != 'HR') readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
                                placeholder="Enter Net Monthly"
                                value="{{ old('net_monthly_pay', $salary_advance->net_monthly_pay ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $user = Auth::user();
        $userRole = $user->roles->pluck('name')[0] ?? null;
        $isFinanceHead = false;
        if (
            $userRole === 'Head of Division' &&
            isset($user->employee->department) &&
            strtoupper(trim($user->employee->department->department_name)) === 'FINANCE AND ADMINISTRATION (F&A)'
        ) {
            $isFinanceHead = true;
        }
    @endphp

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
                                @if (!($role == 'Finance Department' || $isFinanceHead)) readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif
                                placeholder="Outstanding Salary Advance/Loan if any"
                                value="{{ old('outstanding_loan', $salary_advance->outstanding_loan ?? 0) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="comments">Comments</label>
                            <textarea class="form-control" name="comments" id="comments" rows="3"
                                @if (!($role == 'Finance Department' || $isFinanceHead)) readonly title="Editing is disabled for your role" onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()" @endif>{{ old('comments', $salary_advance->comments ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-3 d-flex justify-content-between align-items-start">
        <!-- Left side: Submit button -->
        <div class="col-8">
            <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
        </div>

        <!-- Right side: Approval controls -->
        <div class="col-4 d-flex flex-row align-items-center justify-content-end mb-3">
            @can('approve salary advance')
                @if (isset($salary_advance))
                    @php
                        $user = Auth::user();
                        $userRole = $user->roles->pluck('name')[0];
                        // Check if user is Head of Division for Finance Department
                        if (
                            $userRole === 'Head of Division' &&
                            isset($user->employee->department) &&
                            strtoupper(trim($user->employee->department->department_name)) === 'FINANCE AND ADMINISTRATION (F&A)'
                        ) {
                            $userRole = 'Finance Department';
                        }
                        $statuses = $salary_advance->loan_request_status ?? [];
                        $approvalOrder = ['HR', 'Finance Department', 'Executive Secretary'];
                        $currentStep = null;
                        foreach ($approvalOrder as $role) {
                            if (empty($statuses[$role]) || $statuses[$role] === 'pending') {
                                $currentStep = $role;
                                break;
                            }
                        }
                        $status = $statuses[$userRole] ?? null;
                    @endphp

                    <div class="d-flex align-items-center">
                        @foreach ($approvalOrder as $idx => $role)
                            @php
                                $roleStatus = $statuses[$role] ?? 'pending';
                                $isCurrent = $currentStep === $role;
                            @endphp
                            <div class="d-flex flex-column align-items-center mx-2">
                                <div class="rounded-circle 
                @if ($roleStatus === 'approved') bg-success text-white
                @elseif($roleStatus === 'rejected') bg-danger text-white
                @elseif($isCurrent) bg-warning text-dark
                @else bg-light text-secondary @endif
                d-flex justify-content-center align-items-center"
                                    style="width: 40px; height: 40px; font-size: 1.2rem; border: 2px solid #ccc;">
                                    @if ($roleStatus === 'approved')
                                        <i class="bi bi-check-lg"></i>
                                    @elseif($roleStatus === 'rejected')
                                        <i class="bi bi-x-lg"></i>
                                    @elseif($isCurrent)
                                        <i class="bi bi-hourglass-split"></i>
                                    @else
                                        <span>{{ $idx + 1 }}</span>
                                    @endif
                                </div>
                                <small class="text-center" style="width: 70px;">{{ $role }}</small>
                            </div>
                            @if ($idx < count($approvalOrder) - 1)
                                <div style="width: 30px; height: 2px; background: #ccc;"></div>
                            @endif
                        @endforeach

                        {{-- Approval buttons, only for current step --}}
                        @if ($userRole === $currentStep && $status !== 'approved' && $status !== 'rejected')
                            <div class="ms-4 d-flex flex-row align-items-center gap-2">
                                <input class="btn btn-outline-primary approve-btn" value="Approve" type="button"
                                    data-loan-id="{{ $salary_advance->id }}">
                                <input class="btn btn-outline-danger reject-btn" value="Reject" type="button"
                                    data-loan-id="{{ $salary_advance->id }}" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                            </div>
                        @endif
                    </div>

                    <div class="status mt-2 w-100">
                        @if ($status === 'rejected')
                            <span class="badge bg-danger">
                                You ({{ $userRole }}) rejected this Request
                            </span>
                            <p class="mt-1"><strong>Rejection Reason:</strong>
                                {{ $salary_advance->rejection_reason }}
                            </p>
                        @elseif ($status === 'approved')
                            <span class="badge bg-success">
                                Approved by {{ $userRole }}
                            </span>
                        @elseif ($userRole === $currentStep)
                            <span class="badge bg-warning">Pending your action</span>
                        @else
                            <span class="badge bg-info">Awaiting {{ $currentStep }}...</span>
                        @endif
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

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentLoanId;

            $('.approve-btn').click(function() {
                const currentLoanId = $(this).data('loan-id');
                console.log('loan_id:', currentLoanId);
                updateLoanStatus(currentLoanId, 'approved');
            });

            $('.reject-btn').click(function() {
                currentLoanId = $(this).data('loan-id');
                console.log('loan Id:', currentLoanId);
            });

            $('#confirmReject').click(function() {
                const reason = $('#rejectionReason').val();
                if (reason) {
                    updateLoanStatus(currentLoanId, 'rejected', reason);
                    $('#rejectModal').modal('hide'); // Hide the modal
                } else {
                    Toastify({
                        text: 'Please enter a rejection reason.',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                    }).showToast();
                }
            });

            function updateLoanStatus(LoanId, status, reason = null) {
                $.ajax({
                    url: `/salary-advances/${LoanId}/status`,
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        status: status,
                        reason: reason
                    }),
                    success: function(data) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                        }).showToast();

                        // Update status badge
                        const statusContainer = $('.status');

                        // Clear existing status badges
                        statusContainer.empty();

                        if (status === 'approved') {
                            statusContainer.append(
                                '<span class="badge bg-success">You Approved this request</span>');
                        } else if (status === 'rejected') {
                            statusContainer.append(
                                '<span class="badge bg-danger">You Rejected this request</span>');
                            if (reason) {
                                statusContainer.append(
                                    '<p class="mt-1"><strong>Rejection Reason:</strong>' + reason +
                                    '</p>');
                            }
                        } else {
                            statusContainer.append('<span class="badge bg-warning">Pending</span>');
                        }

                        // Optionally, you could disable the approve/reject buttons once the status is updated
                        $('.approve-btn').prop('disabled', true);
                        $('.reject-btn').prop('disabled', true);
                    },
                    error: function(xhr) {
                        Toastify({
                            text: xhr.responseJSON?.error || 'An error occurred',
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                        }).showToast();
                    }
                });
            }
        });
    </script>
@endpush
