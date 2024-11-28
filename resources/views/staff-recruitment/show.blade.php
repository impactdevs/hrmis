<x-app-layout>
    <div class="container py-5 card ">
        <!-- Title Section -->
        <div class="text-center mb-5 card-title">
            <h1 class="text-primary font-weight-bold">Staff Recruitment Request Form</h1>
            <h3 class="text-muted">Recruitment Details</h3>
        </div>
        <!-- Form Details Section -->
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-4">
                    <label class="font-weight-bold">Position:</label>
                    <p class="lead text-muted">{{ $recruitment->position }}</p>
                </div>
                <div class="mb-4">
                    <label class="font-weight-bold">Department:</label>
                    <p class="lead text-muted">{{ $recruitment->department->department_name }}</p>
                </div>
                <div class="mb-4">
                    <label class="font-weight-bold">Number of Staff:</label>
                    <p class="lead text-muted">{{ $recruitment->number_of_staff }}</p>
                </div>
                <div class="mb-4">
                    <label class="font-weight-bold">When Recruitment is Needed:</label>
                    <p class="lead text-muted">{{ $recruitment->date_of_recruitment->format('d M Y') }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-4">
                    <label class="font-weight-bold">Sourcing Method:</label>
                    <p class="lead text-muted">{{ $recruitment->sourcing_method }}</p>
                </div>
                <div class="mb-4">
                    <label class="font-weight-bold">Employment Basis:</label>
                    <p class="lead text-muted">{{ $recruitment->employment_basis }}</p>
                </div>
                <div class="mb-4">
                    <label class="font-weight-bold">Justification:</label>
                    <p class="lead text-muted">{{ $recruitment->justification }}</p>
                </div>

                <div class="mb-4">
                    <label for="" class="font-weight-bold">Funding Budget:</label>
                    <p class="lead text-muted">{{ $recruitment->funding_budget }}</p>
                </div>
            </div>

            {{-- approval status --}}
            <div class="col-md-12">
                <div class="mb-4">
                    <label class="font-weight-bold">Approval Status:</label>
                    <p class="lead text-muted">
                        @if (!is_null($recruitment->approval_status))
                            @foreach ($recruitment->approval_status as $key => $status)
                                <span>{{ $key . '-' . ucfirst($status) }}</span>
                            @endforeach
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="status m-2">
            @if (isset($recruitment->approval_status[Auth::user()->roles->pluck('name')[0]]) && ($recruitment->approval_status[Auth::user()->roles->pluck('name')[0]] === 'rejected'))
                <span class="badge bg-danger">You rejected this Request</span>
                <p class="mt-1"><strong>Rejection Reason:</strong>
                    {{ $recruitment->rejection_reason }}</p>
            @elseif (isset($recruitment->approval_status[Auth::user()->roles->pluck('name')[0]]) && ($recruitment->approval_status[Auth::user()->roles->pluck('name')[0]] === 'approved'))
                <span class="badge bg-danger">Approved</span>
            @else
                <span class="badge bg-warning">Pending</span>
            @endif
        </div>
        @can('can approve recruitment')
            <div class="form-group">
                <input class="btn btn-outline-primary btn-large approve-btn" value="Approve" type="button"
                    data-recruitment-id="{{ $recruitment->staff_recruitment_id }}">
                <input class="btn btn-outline-danger btn-large reject-btn" value="Reject" type="button"
                    data-recruitment-id="{{ $recruitment->staff_recruitment_id }}" data-bs-toggle="modal"
                    data-bs-target="#rejectModal">
            </div>
        @endcan

    </div>

    <!-- Bootstrap Modal for Rejection Reason -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Recruitment Request</h5>
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
                let currentRecruitmentId;

                $('.approve-btn').click(function() {
                    const currentRecruitmentId = $(this).data('recruitment-id');
                    updateLeaveStatus(currentRecruitmentId, 'approved');
                });

                $('.reject-btn').click(function() {
                    currentRecruitmentId = $(this).data('recruitment-id');
                    console.log('Recruitment Id:', currentRecruitmentId);
                });

                $('#confirmReject').click(function() {
                    const reason = $('#rejectionReason').val();
                    if (reason) {
                        updateLeaveStatus(currentRecruitmentId, 'rejected', reason);
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

                function updateLeaveStatus(trainingId, status, reason = null) {
                    $.ajax({
                        url: `/recruitments/${trainingId}/status`,
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
</x-app-layout>
