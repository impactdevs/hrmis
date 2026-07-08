<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4 fs-3 text-primary">
            <i class="fas fa-calendar-alt"></i> Leave Details
        </h5>
        <div class="row">
            <div class="col-md-6">

                <div class="form-group mb-3">
                    <label for="leave_start_date">
                        <i class="fas fa-clock"></i> Leave Start Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->start_date ? $leave->start_date->toDateString() : 'Not set' }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_end_date">
                        <i class="fas fa-clock"></i> Leave End Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->end_date ? $leave->end_date->toDateString() : 'Not set' }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_description">
                        <i class="fas fa-info-newspaper"></i> Handover Notes
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->handover_note }}</p>
                   @if ($leave->handover_note_file)
    <a href="{{ route('leaves.handover.view', ['leave' => $leave->leave_id]) }}" target="_blank"
        class="btn btn-primary mt-2">
        <i class="fas fa-eye me-1"></i>View Handover File
    </a>
    <a href="{{ route('leaves.handover.download', ['leave' => $leave->leave_id]) }}"
        class="btn btn-primary mt-2">
        <i class="fas fa-download me-1"></i>Download Handover File
    </a>
@endif
                </div>

                <div class="form-group mb-3">
                    <label for="leave_type">
                        <i class="far fa-address-book"></i> Leave Type
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->leaveCategory ? $leave->leaveCategory->leave_type_name : 'No leave type set' }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="leave_address">
                        <i class="fas fa-map-marker-alt"></i> Leave Address
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->leave_address }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="contact_number">
                        <i class="fas fa-phone"></i> Contact Number
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->phone_number }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="other_contact_details">
                        <i class="fas fa-list"></i> Other Contact Details
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->other_contact_details }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="my_work_will_be_done_by">
                        <i class="fas fa-user-alt"></i> My Work Will Be done By:
                    </label>
                    <p>
                        @if (!is_null($leave->my_work_will_be_done_by) && is_array($leave->my_work_will_be_done_by) && isset($leave->my_work_will_be_done_by['users']))
                            <ul type="disc">
                                @foreach (explode(",", $leave->my_work_will_be_done_by['users']) as $key => $substitute)
                                    @if (!empty($substitute) && isset($options["users"][$substitute]))
                                        <li>{{ $options["users"][$substitute] }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @elseif (!is_null($leave->my_work_will_be_done_by) && is_array($leave->my_work_will_be_done_by))
                            <ul type="disc">
                                @foreach ($leave->my_work_will_be_done_by as $userId)
                                    @if (!empty($userId) && isset($options["users"][$userId]))
                                        <li>{{ $options["users"][$userId] }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">No substitute assigned</span>
                        @endif
                    </p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_status">
                        <i class="fas fa-info-circle"></i> Leave Status
                    </label>
                    <p class="border p-2 rounded bg-light">
                        <strong>{{ $leave->getCurrentStatus() }}</strong>
                        @if($leave->is_cancelled)
                            <span class="text-danger"> (Cancelled)</span>
                        @endif
                    </p>
                </div>

                @if($leave->rejection_reason)
                <div class="form-group mb-3">
                    <label for="reason">
                        <i class="fas fa-comment"></i> Reason
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->rejection_reason }}</p>
                </div>
                @endif

                <div class="form-group mb-3">
                    <label for="duration">
                        <i class="fas fa-calendar-days"></i> Leave Duration
                    </label>
                    <p class="border p-2 rounded bg-light">
                        {{ $leave->durationForLeave(\App\Models\PublicHoliday::pluck('holiday_date')->toArray()) }} working days
                        <small class="text-muted">(excluding weekends and public holidays)</small>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="{{ route('leaves.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Leaves
                    </a>

                    @if($leave->canBeEdited())
                        <a href="{{ route('leaves.edit', $leave) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Leave
                        </a>
                    @else
                        {{-- Debug information for why edit is not available --}}
                        @if(!auth()->check())
                            <small class="text-muted">Edit not available: Not authenticated</small>
                        @elseif($leave->user_id !== auth()->id())
                            <small class="text-muted">Edit not available: Not your leave request</small>
                        @elseif($leave->is_cancelled)
                            <small class="text-muted">Edit not available: Leave is cancelled</small>
                        @else
                            @php
                                $status = $leave->leave_request_status ?? [];
                                $hasApproval = false;
                                foreach ($status as $role => $roleStatus) {
                                    if ($roleStatus === 'approved') {
                                        $hasApproval = true;
                                        break;
                                    }
                                }
                            @endphp
                            @if($hasApproval)
                                <small class="text-muted">Edit not available: Leave has been approved</small>
                            @else
                                {{-- This should not happen, but let's show edit anyway for troubleshooting --}}
                                <a href="{{ route('leaves.edit', $leave) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Leave (Debug)
                                </a>
                                <br><small class="text-warning">Debug: Edit should be available but canBeEdited() returned false</small>
                            @endif
                        @endif
                    @endif

                    @if($leave->leave_id)
                        <a href="{{ route('leaves.show-history', ['leave' => $leave->leave_id]) }}" class="btn btn-info">
                            <i class="fas fa-history"></i> View History
                        </a>
                    @else
                        <span class="btn btn-info disabled" title="Leave ID not available">
                            <i class="fas fa-history"></i> View History (ID Missing)
                        </span>
                        {{-- Debug information --}}
                        <small class="text-danger d-block">Debug: Leave ID is missing ({{ $leave->leave_id ?? 'null' }})</small>
                    @endif

                    @if(!$leave->is_cancelled && (string)auth()->id() === (string)$leave->user_id)
                        <button type="button" class="btn btn-warning" onclick="cancelLeave()">
                            <i class="fas fa-ban"></i> Cancel Leave
                        </button>
                    @endif

                    @if($leave->canUserApprove())
                        <button type="button" class="btn btn-success approve-btn" data-leave-id="{{ $leave->leave_id }}">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger reject-btn" data-leave-id="{{ $leave->leave_id }}"
                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif
                </div>
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

    <script>
        function updateLeaveStatus(leaveId, status, reason = null) {
            fetch(`/leaves/${leaveId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status, reason: reason })
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.error || 'An error occurred while updating the leave request.');
                }
                return data;
            })
            .then(data => {
                alert(data.message || 'Leave request updated successfully.');
                location.reload();
            })
            .catch(error => {
                alert(error.message);
            });
        }

        let currentLeaveIdForRejection;

        document.addEventListener('DOMContentLoaded', function() {
            const approveBtn = document.querySelector('.approve-btn');
            if (approveBtn) {
                approveBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to approve this leave request?')) {
                        updateLeaveStatus(this.dataset.leaveId, 'approved');
                    }
                });
            }

            const rejectBtn = document.querySelector('.reject-btn');
            if (rejectBtn) {
                rejectBtn.addEventListener('click', function() {
                    currentLeaveIdForRejection = this.dataset.leaveId;
                });
            }

            const confirmRejectBtn = document.getElementById('confirmReject');
            if (confirmRejectBtn) {
                confirmRejectBtn.addEventListener('click', function() {
                    const reason = document.getElementById('rejectionReason').value;
                    if (!reason) {
                        alert('Please enter a rejection reason.');
                        return;
                    }
                    updateLeaveStatus(currentLeaveIdForRejection, 'rejected', reason);
                });
            }
        });
    </script>

    <script>

        function cancelLeave() {
            if (confirm('Are you sure you want to cancel this leave request?')) {
                const reason = prompt('Please provide a reason for cancellation (optional):');

                fetch(`/leaves/{{ $leave->leave_id }}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message || 'Leave cancelled successfully');
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while cancelling the leave');
                });
            }
        }
    </script>
</x-app-layout>
