@props(['workfromhome' => null, 'tasks' => []])

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="work_from_home_start_date" label="Start Date" type="date"
            :value="old('work_from_home_start_date', $workfromhome->work_from_home_start_date ?? '')" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="work_from_home_end_date" label="End Date" type="date"
            :value="old('work_from_home_end_date', $workfromhome->work_from_home_end_date ?? '')" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-12">
        <x-forms.text-area name="work_from_home_reason" label="Reason"
            :value="old('work_from_home_reason', $workfromhome->work_from_home_reason ?? '')" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="work_location" label="Work Location"
            :options="[
                'Home'     => 'Home',
                'Office'   => 'Office',
                'Field'    => 'Field',
                'Workshop' => 'Workshop',
                'Training' => 'Training',
                'Other'    => 'Other (Please specify)',
            ]"
            :selected="old('work_location', $workfromhome->work_location ?? '')" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="work_from_home_attachments" label="Attachment (optional)" type="file" />
        @if(isset($workfromhome) && $workfromhome->work_from_home_attachments)
            <small>Existing: <a href="{{ asset('storage/' . $workfromhome->work_from_home_attachments) }}" target="_blank">View</a></small>
        @endif
    </div>
</div>

{{-- Task Inputs with Dynamic Add/Remove --}}
<div class="mb-3">
    <label class="form-label">Tasks</label>
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
            @php
                $oldStartDates = old('task_start_date', []);
                $oldEndDates = old('task_end_date', []);
                $oldDescriptions = old('description', []);
            @endphp

            @if(count($oldStartDates) > 0)
                @foreach($oldStartDates as $i => $start)
                    <tr>
                        <td><input type="date" name="task_start_date[]" class="form-control" value="{{ $start }}"></td>
                        <td><input type="date" name="task_end_date[]" class="form-control" value="{{ $oldEndDates[$i] ?? '' }}"></td>
                        <td><input type="text" name="description[]" class="form-control" value="{{ $oldDescriptions[$i] ?? '' }}"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                    </tr>
                @endforeach
            @elseif(isset($tasks) && count($tasks) > 0)
                @foreach($tasks as $task)
                    <tr>
                        <td><input type="date" name="task_start_date[]" class="form-control" value="{{ $task->start_date }}"></td>
                        <td><input type="date" name="task_end_date[]" class="form-control" value="{{ $task->end_date }}"></td>
                        <td><input type="text" name="description[]" class="form-control" value="{{ $task->description }}"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><input type="date" name="task_start_date[]" class="form-control"></td>
                    <td><input type="date" name="task_end_date[]" class="form-control"></td>
                    <td><input type="text" name="description[]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                </tr>
            @endif
        </tbody>
    </table>
    <button type="button" class="btn btn-primary" id="add-task">Add Task</button>
</div>

@push('scripts')
<script>
    document.getElementById('add-task').addEventListener('click', function () {
        const tableBody = document.querySelector('#task-table tbody');
        const row = `
            <tr>
                <td><input type="date" name="task_start_date[]" class="form-control"></td>
                <td><input type="date" name="task_end_date[]" class="form-control"></td>
                <td><input type="text" name="description[]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row);
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.matches('.remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endpush
