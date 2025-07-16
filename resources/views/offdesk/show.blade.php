<x-app-layout>
    <div class="mt-4">
        <h4>Off Desk Details</h4>

        <div class="card p-4 shadow-sm">
            <p><strong>Employee:</strong> {{ $entry->employee->full_name ?? 'N/A' }}</p>
            <p><strong>Start:</strong> {{ $entry->start_datetime }}</p>
            <p><strong>End:</strong> {{ $entry->end_datetime }}</p>
            <p><strong>Reason:</strong> {{ $entry->reason }}</p>

            <div class="mt-3">
                <a href="{{ route('offdesk.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</x-app-layout>
