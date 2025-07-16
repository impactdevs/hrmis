<x-app-layout>
    <div class="mt-4">
        <h4 class="mb-4">Edit Work From Home Request</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>There were some issues with your submission:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('workfromhome.update', $entry->work_from_home_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="work_from_home_start_date" class="form-control"
                        value="{{ old('work_from_home_start_date', $entry->work_from_home_start_date) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" name="work_from_home_end_date" class="form-control"
                        value="{{ old('work_from_home_end_date', $entry->work_from_home_end_date) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason</label>
                <textarea name="work_from_home_reason" class="form-control" rows="3" required>{{ old('work_from_home_reason', $entry->work_from_home_reason) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Work Location</label>
                <input type="text" name="work_location" class="form-control"
                    value="{{ old('work_location', $entry->work_location) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Existing Attachment</label><br>
                @if($entry->work_from_home_attachments)
                    <a href="{{ asset('storage/' . $entry->work_from_home_attachments) }}" target="_blank">View current file</a>
                @else
                    <em>No attachment</em>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label">Replace Attachment (optional)</label>
                <input type="file" name="work_from_home_attachments" class="form-control">
            </div>

            {{-- Task entries (editable if needed) --}}
            @if ($entry->task && $entry->task->count())
                <div class="mb-3">
                    <label class="form-label">Tasks (View Only)</label>
                    <ul class="list-group">
                        @foreach ($entry->task as $task)
                            <li class="list-group-item">
                                <strong>{{ $task->description }}</strong><br>
                                <small>{{ $task->startdate }} to {{ $task->enddate }}</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('workfromhome.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Request
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
