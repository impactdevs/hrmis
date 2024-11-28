<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4">
            <i class="fas fa-calendar-alt"></i> Travel Details
        </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="training_name">
                        <i class="fas fa-tag"></i> Destination
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->destination }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_name">
                        <i class="fas fa-tag"></i> Purpose of Travel:
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->travel_purpose }}</p>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4"><strong>Relevant Documents:</strong></div>
                    <div class="col-md-8">
                        <div class="mt-2">
                            @foreach ($training->relevant_documents as $item)
                                @if (isset($item['proof']))
                                    <div class="mb-2">
                                        @php
                                            $filePath = asset('storage/' . $item['proof']);
                                            $fileExtension = pathinfo($item['proof'], PATHINFO_EXTENSION);
                                        @endphp
                                        @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                            <!-- Display Image -->
                                            <div>
                                                <img src="{{ $filePath }}" alt="{{ $item['title'] }}"
                                                    class="img-fluid rounded mt-2" style="max-width: 120px;">
                                            </div>
                                        @elseif ($fileExtension === 'pdf')
                                            <!-- Display PDF Link -->
                                            <div>
                                                <a href="{{ $filePath }}" target="_blank"
                                                    class="d-flex align-items-center text-decoration-none">
                                                    <img src="{{ asset('assets/img/pdf-icon.png') }}" alt="PDF icon"
                                                        class="pdf-icon me-2" width="24">
                                                    <span
                                                        class="text-dark">{{ $item['title'] ?? 'The document has no title' }}</span>
                                                </a>
                                            </div>
                                        @else
                                            <!-- Handle other file types -->
                                            <p class="text-muted">Unsupported file type: {{ $item['title'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="training_start_date">
                        <i class="fas fa-clock"></i> Departure Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->departure_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_end_date">
                        <i class="fas fa-clock"></i> Return Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->return_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_description">
                        <i class="fas fa-info-circle"></i> Sponsors
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->sponsor }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="training_description">
                        <i class="fas fa-info-circle"></i> Substitutes
                    </label>
                    <p>
                        @if (!is_null($training->my_work_will_be_done_by))
                            <ul type="disc">
                                @foreach ($training->my_work_will_be_done_by as $key => $substitute)
                                    @if (!is_null($substitute))
                                        <li>{{ $options[$key][$substitute] }}</li>
                                    @else
                                        -
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            -
                        @endif
                    </p>

                </div>
            </div>

            @can('approve training')
                @if (!is_null($training->user_id))
                    <div class="status m-2">
                        @if (isset($training->training_request_status[Auth::user()->roles->pluck('name')[0]]) &&
                                $training->training_request_status[Auth::user()->roles->pluck('name')[0]] === 'rejected')
                            <span class="badge bg-danger">You rejected this Request</span>
                            <p class="mt-1"><strong>Rejection Reason:</strong>
                                {{ $training->rejection_reason }}</p>
                        @elseif (isset($training->training_request_status[Auth::user()->roles->pluck('name')[0]]) &&
                                $training->training_request_status[Auth::user()->roles->pluck('name')[0]] === 'approved')
                            <span class="badge bg-success">Approved</span>
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
                        url: `/out-of-station-trainings/${trainingId}/status`,
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
