<x-app-layout>
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Employee:</div>
                    <div class="col-md-9">{{ $entry->employee->full_name ?? 'N/A' }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Start:</div>
                    <div class="col-md-9">{{ $entry->start_datetime }}</div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">End:</div>
                    <div class="col-md-9">{{ $entry->end_datetime }}</div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 fw-bold">Reason:</div>
                    <div class="col-md-9">{{ $entry->reason }}</div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('offdesk.index') }}" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>