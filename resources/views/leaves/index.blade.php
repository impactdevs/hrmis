<x-app-layout>
    <div class="mt-3">
        <h5 class="text-center mt-5">Leave Management</h5>

        <!-- Statistics Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-info text-white">
                        Total Leaves Taken
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalLeaves }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-warning text-white">
                        Total Days Consumed
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalDays }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header bg-success text-white">
                        Leaves per Category
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($leavesPerCategory as $category => $count)
                                <li class="list-group-item d-flex justify-content-between">
                                    {{ $category }} <span class="badge bg-primary">{{ $count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('leaves.create') }}" class="btn btn-success">Add a Leave</a>
        </div>

        <div class="mt-3">
            <div class="row" id="leaveCards">
                @foreach ($leaves as $leave)
                    @php
                        $isExpired = \Carbon\Carbon::now()->isAfter(\Carbon\Carbon::parse($leave->end_date));
                    @endphp
                    <div class="col-md-4 mb-3 leave-card" data-leave-id="{{ $leave->leave_id }}">
                        <div class="card border border-info">
                            <div class="card-header position-relative">
                                <h6 class="m-0">{{ $leave->employee->first_name }}</h6>
                                <span
                                    class="badge {{ $isExpired ? 'bg-danger' : 'bg-success' }} position-absolute top-0 end-0 m-1">
                                    {{ $isExpired ? 'Expired' : 'Ongoing' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ optional($leave->leaveCategory)->leave_type_name }}</h5>
                                <p class="card-text"><strong>Reason:</strong> {{ $leave->reason }}</p>
                                <p class="card-text"><strong>Leave Duration:</strong>
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} -
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                </p>
                                <p class="card-text"><strong>People to Stand In For Me:</strong></p>
                                <div>
                                    @foreach ($leave->my_work_will_be_done_by as $person)
                                        <span class="badge badge-info bg-info">{{ $person }}</span>
                                    @endforeach
                                </div>
                                <div class="status mt-2">
                                    @if (Auth::user()->roles->pluck('name')[0] === $leave->leave_request_status)
                                        <span class="badge bg-success">You Approved this Leave Request.</span>
                                    @elseif ($leave->leave_request_status === 'rejected')
                                        <span class="badge bg-danger">You rejected this Request</span>
                                        <p class="mt-1"><strong>Rejection Reason:</strong>
                                            {{ $leave->rejection_reason }}</p>
                                    @elseif ($leave->leave_request_status === 'approved')
                                        <span class="badge bg-danger">Approved</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-primary btn-sm rounded-circle approve-btn" title="Approve"
                                    data-leave-id="{{ $leave->leave_id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-danger btn-sm rounded-circle reject-btn" title="Reject"
                                    data-leave-id="{{ $leave->leave_id }}" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Rejection Reason -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Leave</h5>
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
                let currentLeaveId;

                $('.approve-btn').click(function() {
                    const leaveId = $(this).data('leave-id');
                    updateLeaveStatus(leaveId, 'approved');
                });

                $('.reject-btn').click(function() {
                    currentLeaveId = $(this).data('leave-id');
                    console.log('Leave Id:', currentLeaveId);
                });

                $('#confirmReject').click(function() {
                    const reason = $('#rejectionReason').val();
                    if (reason) {
                        updateLeaveStatus(currentLeaveId, 'rejected', reason);
                        $('#rejectModal').modal('hide'); // Hide the modal
                    } else {
                        alert('Please enter a rejection reason.');
                    }
                });

                function updateLeaveStatus(leaveId, status, reason = null) {
                    $.ajax({
                        url: `/leaves/${leaveId}/status`,
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

                            // Update the UI based on the action
                            const card = $(`.leave-card[data-leave-id="${leaveId}"]`);
                            if (status === 'approved') {
                                card.find('.status').html('<span class="badge bg-success">Approved</span>');
                                card.find('.approve-btn, .reject-btn').prop('disabled', true);
                            } else if (status === 'rejected') {
                                card.find('.status').html(`
                                    <span class="badge bg-danger">Rejected</span>
                                    <p class="mt-1"><strong>Rejection Reason:</strong> ${reason}</p>
                                `);
                                card.find('.approve-btn, .reject-btn').prop('disabled', false);
                            }
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
