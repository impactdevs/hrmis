<x-app-layout>
    <div class="mt-4">
        <form method="GET" action="{{ route('attendances.index') }}" class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="filter_date" class="form-label">Date</label>
                <input type="date" name="filter_date" id="filter_date" class="form-control"
                    value="{{ request('filter_date', now()->format('Y-m-d')) }}">
            </div>
            @if (auth()->user()->hasRole('HR'))
                <div class="col-md-4">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach (\App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}"
                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </form>

        <div class="table-responsive">
            <table id="attendance-table" class="table table-striped" data-toggle="table" data-pagination="true"
                data-search="true" data-show-columns="true" data-show-export="true" data-click-to-select="true"
                data-export-types="['csv', 'excel', 'pdf', 'print']" data-toolbar="#toolbar">
                <thead class="table-light">
                    <tr>
                        <th data-sortable="true">#</th>
                        <th data-sortable="true">First Name</th>
                        <th data-sortable="true">Last Name</th>
                        <th data-sortable="true">Department</th>
                        <th data-sortable="true">Date</th>
                        <th data-sortable="true">Clock In</th>
                        <th data-sortable="true">Clock Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($summarizedAttendances as $index => $attendance)
                        @php
                            $employee = \App\Models\Employee::where('staff_id', $attendance->staff_id)->first();
                        @endphp
                        <tr>
                            <td>{{ $employee?->staff_id ?? 'N/A' }}</td>
                            <td>{{ $employee?->first_name ?? 'N/A' }}</td>
                            <td>{{ $employee?->last_name ?? 'N/A' }}</td>
                            <td>{{ $employee?->department?->department_name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->access_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">No attendance data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
