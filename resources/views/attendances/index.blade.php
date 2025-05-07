<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between mb-3">
            <form action="{{ route('attendances.index') }}" method="GET" class="d-flex align-items-center">
                <input type="date" name="filter_date" class="form-control"
                    value="{{ request('filter_date', now()->format('Y-m-d')) }}">
                <button type="submit" class="btn btn-primary ms-2">Filter</button>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Date</th>
                        <th scope="col">Clock In Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $index => $attendance)
                        <tr class="align-middle">
                            <th scope="row">
                                <a href="{{ route('attendances.show', $attendance->attendance_id) }}"
                                    class="btn btn-outline-primary">
                                    {{ $attendance->employee->staff_id }}
                                </a>
                            </th>
                            <td>{{ $attendance->employee->first_name }}</td>
                            <td>{{ $attendance->employee->last_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d-m-Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-danger">
                                No attendance records found for the selected date.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrapper">
                {!! $attendances->appends(['filter_date' => request()->get('filter_date')])->render() !!}
            </div>
        </div>
    </div>
</x-app-layout>
