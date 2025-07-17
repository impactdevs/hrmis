<x-app-layout>
    <div class="container mt-4">

        <form action="{{ route('offdesk.store') }}" method="POST">
            @csrf

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="start_datetime" class="form-label">Start Date & Time</label>
                    <input type="datetime-local" name="start_datetime" class="form-control" value="{{ old('start_datetime') }}" required>
                </div>

                <div class="col-md-6">
                    <label for="end_datetime" class="form-label">End Date & Time</label>
                    <input type="datetime-local" name="end_datetime" class="form-control" value="{{ old('end_datetime') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Destination</label>
                <textarea name="destination" class="form-control" rows="1" required>{{ old('destination') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Duty Allocated</label>
                <textarea name="duty_allocated" class="form-control" rows="1" required>{{ old('duty_allocated') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('offdesk.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</x-app-layout>
