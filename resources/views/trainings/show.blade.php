<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4">
            <i class="fas fa-calendar-alt"></i> Training Details
        </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="training_name">
                        <i class="fas fa-tag"></i> Training Name
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_title }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_start_date">
                        <i class="fas fa-clock"></i> Training Start Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_start_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_end_date">
                        <i class="fas fa-clock"></i> Training End Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_end_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_description">
                        <i class="fas fa-info-circle"></i> Training Description
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_description }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="training_description">
                        <i class="fas fa-info-circle"></i> Training Categories
                    </label>
                    <p>
                        @php
                            // Assume training_category contains comma-separated IDs for each category
                            $userIds = explode(',', $training->training_category['users'] ?? '');
                            $departmentIds = explode(',', $training->training_category['departments'] ?? '');
                            $positionIds = explode(',', $training->training_category['positions'] ?? '');

                        @endphp
                        @foreach ($userIds as $id)
                            @if ($id === 'All')
                                <span class="badge bg-primary">All Users</span>
                            @elseif (!empty($id) && isset($options['users'][$id]))
                                <span class="badge bg-primary">{{ $options['users'][$id] ?? 'Unknown User' }}</span>
                            @endif
                        @endforeach

                        @foreach ($departmentIds as $id)
                            @if (filled($id))
                                <span
                                    class="badge bg-success">{{ $options['departments'][$id] == '' ? 'not found' : $options['departments'][$id] ?? 'Unknown Department' }}</span>
                            @endif
                        @endforeach

                        @foreach ($positionIds as $id)
                            @if (filled($id))
                                <span
                                    class="badge bg-info">{{ $options['positions'][$id] == '' ? '' : $options['positions'][$id] ?? 'Unknown Position' }}</span>
                            @endif
                        @endforeach
                    </p>

                </div>
            </div>

            @can('approve training')
                @if (!is_null($training->user_id))
                    <div class="status m-2">
                        @if (Auth::user()->roles->pluck('name')[0] === $training->approval_status)
                            <span class="badge bg-success">You Approved this Leave Request.</span>
                        @elseif ($training->approval_status === 'rejected')
                            <span class="badge bg-danger">You rejected this Request</span>
                            <p class="mt-1"><strong>Rejection Reason:</strong>
                                {{ $training->rejection_reason }}</p>
                        @elseif ($training->approval_status === 'approved')
                            <span class="badge bg-danger">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <input class="btn btn-outline-primary btn-large approve-btn" value="Approve" type="button"
                            data-training-id="{{ $training->training_id }}">
                        <input class="btn btn-outline-danger btn-large reject-btn" value="Reject" type="button"
                            data-training-id="{{ $training->training_id }}" data-bs-toggle="modal"
                            data-bs-target="#rejectModal">
                    </div>
                @endif
            @endcan
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
                let currentTrainingId;

                $('.approve-btn').click(function() {
                    const currentTrainingId = $(this).data('training-id');
                    updateLeaveStatus(currentTrainingId, 'approved');
                });

                $('.reject-btn').click(function() {
                    currentTrainingId = $(this).data('training-id');
                    console.log('Training Id:', currentTrainingId);
                });

                $('#confirmReject').click(function() {
                    const reason = $('#rejectionReason').val();
                    if (reason) {
                        updateLeaveStatus(currentTrainingId, 'rejected', reason);
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
                        url: `/trainings/${trainingId}/status`,
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
