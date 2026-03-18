<x-app-layout>
    <div class="mt-4">

        {{-- Filters --}}
        <form method="GET" action="{{ route('attendances.index') }}" class="row g-3 mb-4">

            {{-- Date From --}}
            <div class="col-md-2">
                <label for="date_from" class="form-label">From</label>
                <input type="date" name="date_from" id="date_from" class="form-control"
                    value="{{ $dateFrom }}">
            </div>

            {{-- Date To --}}
            <div class="col-md-2">
                <label for="date_to" class="form-label">To</label>
                <input type="date" name="date_to" id="date_to" class="form-control"
                    value="{{ $dateTo }}">
            </div>

            {{-- Quick range shortcuts --}}
            <div class="col-md-3">
                <label class="form-label">Quick Range</label>
                <div class="d-flex gap-2">
                    <a href="{{ route('attendances.index', ['date_from' => now()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}"
                        class="btn btn-sm btn-outline-secondary">Today</a>
                    <a href="{{ route('attendances.index', ['date_from' => now()->startOfWeek()->format('Y-m-d'), 'date_to' => now()->endOfWeek()->format('Y-m-d')]) }}"
                        class="btn btn-sm btn-outline-secondary">This Week</a>
                    <a href="{{ route('attendances.index', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d')]) }}"
                        class="btn btn-sm btn-outline-secondary">This Month</a>
                    <a href="{{ route('attendances.index', ['date_from' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}"
                        class="btn btn-sm btn-outline-secondary">Last Month</a>
                </div>
            </div>

            {{-- Department filter — HR only --}}
            @if (auth()->user()->hasRole('HR'))
                <div class="col-md-2">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Employee filter --}}
            <div class="col-md-2">
                <label for="staff_id" class="form-label">Employee</label>
                <select name="staff_id" id="staff_id" class="form-select">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->staff_id }}"
                            {{ request('staff_id') == $employee->staff_id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        {{-- Summary bar --}}
        <div class="mb-3 text-muted small">
            Showing attendance from <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong>
            to <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
            — {{ $summarizedAttendances->total() }} record(s) found
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="attendance-table">
                <thead class="table-light">
                    <tr>
                        <th>Staff ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Hours Worked</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($summarizedAttendances as $attendance)
                        @php
                            $employee = \App\Models\Employee::where('staff_id', $attendance->staff_id)
                                ->with('department')
                                ->first();
                        @endphp
                        <tr>
                            <td>{{ $attendance->staff_id }}</td>
                            <td>{{ $employee?->first_name ?? 'N/A' }}</td>
                            <td>{{ $employee?->last_name ?? 'N/A' }}</td>
                            <td>{{ $employee?->department?->department_name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->access_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i:s') }}</td>
                            <td>{{ $attendance->hours_worked ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-danger">
                                No attendance data found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $summarizedAttendances->links() }}
        </div>

    </div>
</x-app-layout>