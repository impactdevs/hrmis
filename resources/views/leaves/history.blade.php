<x-app-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        Leave Request History: {{ $leave->getLeaveTitle() }}
                    </h4>
                    <div>
                        <a href="{{ route('leaves.show', ['leave' => $leave->leave_id]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Leave
                        </a>
                        <a href="{{ route('leaves.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list"></i> All Leaves
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Leave Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Leave Details</h6>
                            <p><strong>Employee:</strong> {{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</p>
                            <p><strong>Leave Type:</strong> {{ $leave->leaveCategory ? $leave->leaveCategory->leave_type_name : 'No leave type set' }}</p>
                            <p><strong>Duration:</strong> {{ $leave->start_date ? $leave->start_date->format('M d, Y') : 'Not set' }} - {{ $leave->end_date ? $leave->end_date->format('M d, Y') : 'Not set' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Current Status</h6>
                            <p><strong>Status:</strong> 
                                <span class="badge 
                                    @if($leave->getCurrentStatus() === 'Approved') badge-success
                                    @elseif(str_contains($leave->getCurrentStatus(), 'Rejected')) badge-danger
                                    @else badge-warning
                                    @endif">
                                    {{ $leave->getCurrentStatus() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="timeline">
                        <h6 class="text-muted mb-3">Leave Request Timeline</h6>
                        
                        @if(empty($history))
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No detailed history records found. This leave request was submitted before the history tracking system was implemented.
                                <br><small class="mt-2 d-block">Current status information is still available in the leave details above.</small>
                            </div>
                        @else
                            @foreach($history as $item)
                                <div class="timeline-item">
                                    <div class="timeline-marker 
                                        @if($item['action'] === 'approved') bg-success
                                        @elseif($item['action'] === 'rejected') bg-danger
                                        @elseif($item['action'] === 'submitted') bg-primary
                                        @elseif($item['action'] === 'cancelled') bg-secondary
                                        @else bg-info
                                        @endif">
                                        <i class="fas 
                                            @if($item['action'] === 'approved') fa-check
                                            @elseif($item['action'] === 'rejected') fa-times
                                            @elseif($item['action'] === 'submitted') fa-paper-plane
                                            @elseif($item['action'] === 'cancelled') fa-ban
                                            @elseif($item['action'] === 'created') fa-plus
                                            @elseif($item['action'] === 'edited') fa-edit
                                            @else fa-clock
                                            @endif"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $item['description'] }}</h6>
                                                @if($item['actor'])
                                                    <small class="text-muted">
                                                        by {{ $item['actor']['name'] }} 
                                                        @if($item['actor']['role'])
                                                            ({{ $item['actor']['role'] }})
                                                        @endif
                                                        @if($item['actor']['staff_id'])
                                                            - {{ $item['actor']['staff_id'] }}
                                                        @endif
                                                    </small>
                                                @endif
                                                
                                                @if($item['comments'] && $item['comments'] !== 'Leave request approved' && $item['comments'] !== 'Leave request created' && $item['comments'] !== 'Leave request submitted for approval')
                                                    <div class="mt-2">
                                                        <small class="d-block"><strong>Comment:</strong> {{ $item['comments'] }}</small>
                                                    </div>
                                                @endif

                                                @if($item['stage_transition']['from'] && $item['stage_transition']['to'])
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            Status changed from <span class="badge badge-light">{{ $item['stage_transition']['from'] }}</span>
                                                            to <span class="badge badge-light">{{ $item['stage_transition']['to'] }}</span>
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                            <small class="text-muted ml-3">{{ $item['timestamp'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline:before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -15px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    margin-left: 20px;
    position: relative;
}

.timeline-content:before {
    content: '';
    position: absolute;
    left: -8px;
    top: 15px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #f8f9fa;
}

.timeline-item:last-child .timeline:before {
    display: none;
}

.badge {
    font-size: 0.75em;
}
</style>
</x-app-layout>
