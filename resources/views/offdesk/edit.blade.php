<x-app-layout>
    <div class="mt-4">
        <h4>Edit Off Desk Record</h4>

        <form action="{{ route('offdesk.update', $entry->off_desk_id) }}" method="POST">
            @csrf
            @method('PUT')

        

            <div class="mb-3">
                <label class="form-label">Start Date & Time</label>
                <input type="datetime-local" name="start_datetime" class="form-control" value="{{ $entry->start_datetime }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">End Date & Time</label>
                <input type="datetime-local" name="end_datetime" class="form-control" value="{{ $entry->end_datetime }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="3" required>{{ $entry->reason }}</textarea>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('offdesk.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</x-app-layout>
