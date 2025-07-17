<x-app-layout>
    <div class="mt-3">
        <div class="d-flex justify-content-between">
            @can('create offdesk')
                <a href="{{ route('offdesk.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add Off Desk Record
                </a>
            @endcan
        </div>

        <div class="table-wrapper mt-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Destination</th>
                        <th>Duty Allocated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $index => $entry)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $entry->employee->full_name ?? 'N/A' }}</td>
                            <td>{{ $entry->start_datetime }}</td>
                            <td>{{ $entry->end_datetime }}</td>
                            <td>{{ $entry->destination }}</td>
                            <td>{{ $entry->duty_allocated }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('offdesk.show', $entry->off_desk_id) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('offdesk.edit', $entry->off_desk_id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('offdesk.destroy', $entry->off_desk_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No off desk records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
