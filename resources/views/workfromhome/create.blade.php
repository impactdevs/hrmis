<x-app-layout>
    <div class="container mt-4">

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

        <form action="{{ route('workfromhome.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="work_from_home_start_date" class="form-label">Start Date</label>
                    <input type="date" name="work_from_home_start_date" class="form-control"
                        value="{{ old('work_from_home_start_date') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="work_from_home_end_date" class="form-label">End Date</label>
                    <input type="date" name="work_from_home_end_date" class="form-control"
                        value="{{ old('work_from_home_end_date') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="work_from_home_reason" class="form-label">Reason</label>
                <textarea name="work_from_home_reason" class="form-control" rows="2" required>{{ old('work_from_home_reason') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="work_from_home_attachments" class="form-label">Attachment (Optional)</label>
                <input type="file" name="work_from_home_attachments" class="form-control">
            </div>

            {{-- Task Entries --}}
            <div class="mb-3">
                <label class="form-label">Tasks(A Break down Of what you will be doing from home)</label>
                <table class="table table-bordered" id="task-table">
                    <thead>
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Description</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="date" name="task_start_date[]" class="form-control"></td>
                            <td><input type="date" name="task_end_date[]" class="form-control"></td>
                            <td><input type="text" name="description[]" class="form-control"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-success btn-sm" id="add-task">Add Task</button>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('workfromhome.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Submit Request
                </button>
            </div>
        </form>
    </div>

    {{-- Dynamic row addition script --}}
    @push('scripts')
        <script>
            document.getElementById('add-task').addEventListener('click', function() {
                const row = `
                <tr>
                    <td><input type="date" name="task_start_date[]" class="form-control" required></td>
                    <td><input type="date" name="task_end_date[]" class="form-control" required></td>
                    <td><input type="text" name="description[]" class="form-control" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                </tr>`;
                document.querySelector('#task-table tbody').insertAdjacentHTML('beforeend', row);
            });

            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        </script>
    @endpush
</x-app-layout>
