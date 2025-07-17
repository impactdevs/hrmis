<x-app-layout>
    <div class="container mt-5">

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title mb-4">Work From Home Details</h4>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Employee:</strong> {{ $entry->employee->full_name ?? 'N/A' }}</p>
                        <p><strong>Start Date:</strong> {{ $entry->work_from_home_start_date }}</p>
                        <p><strong>End Date:</strong> {{ $entry->work_from_home_end_date }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Reason:</strong> {{ $entry->work_from_home_reason }}</p>
                        <p><strong>Location:</strong> {{ $entry->work_location }}</p>
                        @if ($entry->work_from_home_attachments)
                            <p><strong>Attachment:</strong>
                                <a href="{{ asset('storage/' . $entry->work_from_home_attachments) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-paperclip"></i> View Attachment
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($entry->tasks && $entry->tasks->count())
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Tasks</h5>
                    <ul class="list-group list-group-flush">
                        @foreach ($entry->tasks as $task)
                            <li class="list-group-item">
                                <strong>{{ $task->description }}</strong><br>
                                <small class="text-muted">{{ $task->task_start_date }} to
                                    {{ $task->task_end_date }}</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <a href="{{ route('workfromhome.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>

    </div>
</x-app-layout>
