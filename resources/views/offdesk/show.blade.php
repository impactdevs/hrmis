<x-app-layout>
    <div class="container mt-4">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-house-door-fill"></i> Off the Desk Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Start Date:</strong> {{ $entry->start_datetime }}</div>
                    <div class="col-md-6"><strong>End Date:</strong> {{ $entry->end_datetime }}</div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6"><strong>Destination:</strong> {{ $entry->destination ?? 'N/A' }}</div>
                    <div class="col-md-6"><strong>Duty Allocated:</strong> {{ $entry->duty_allocated }}</div>
                </div>

                <div class="mb-2">
                    <strong>Reason:</strong> {{ $entry->reason }}
                </div>
            </div>
        </div>


        @if(auth()->user()->hasRole('HR') && $entry->status === 'pending')
            <div class="mb-4">
                <form action="{{ route('offdesk.approve', $entry->off_desk_id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success me-2">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                </form>

                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#declineModal">
                    <i class="bi bi-x-circle"></i> Decline
                </button>
            </div>

            <!-- Decline Modal -->
            <div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('offdesk.decline', $entry->off_desk_id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="declineModalLabel">Decline Work From Home Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="decline_reason" class="form-label">Reason for Decline</label>
                                    <textarea name="decline_reason" class="form-control" rows="3" required placeholder="Provide a reason..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Submit Decline</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($entry->status === 'declined')
            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Declined Reason:</strong> {{ $entry->decline_reason }}
            </div>
        @endif

        <a href="{{ route('offdesk.index') }}" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-arrow-left-circle"></i> Back to List
        </a>
    </div>
</x-app-layout>
