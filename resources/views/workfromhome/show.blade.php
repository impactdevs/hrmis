<x-app-layout>
    <div class="container mt-4">

        <h4>Work From Home Details</h4>

        <div class="mb-3">
            <strong>Employee:</strong> {{ $entry->employee->full_name ?? 'N/A' }} <br>
            <strong>Start Date:</strong> {{ $entry->work_from_home_start_date }} <br>
            <strong>End Date:</strong> {{ $entry->work_from_home_end_date }} <br>
            <strong>Reason:</strong> {{ $entry->work_from_home_reason }} <br>
            <strong>Location:</strong> {{ $entry->work_location }} <br>

            @if($entry->work_from_home_attachments)
                <strong>Attachment:</strong>
                <a href="{{ asset('storage/' . $entry->work_from_home_attachments) }}" target="_blank">View</a>
            @endif
        </div>

        @if ($entry->task && $entry->task->count())
            <div class="mb-3">
                <h5>Tasks</h5>
                <ul class="list-group">
                    @foreach ($entry->task as $task)
                        <li class="list-group-item">
                            <strong>{{ $task->description }}</strong><br>
                            <small>{{ $task->start_date }} to {{ $task->end_date }}</small>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <a href="{{ route('workfromhome.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</x-app-layout>
