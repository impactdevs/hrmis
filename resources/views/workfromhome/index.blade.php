<x-app-layout>
    <div class="mt-3">
        <div class="flex-row flex-1 d-flex justify-content-between">
            @can('can add work from home request')
                <div>
                    <a href="{{ route('workfromhome.create') }}" class="btn border-t-neutral-50 btn-primary">
                        <i class="bi bi-house-add me-2"></i>Apply To work from home
                    </a>
                </div>
            @endcan
        </div>

        <div class="table-wrapper mt-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Employee</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $index => $entry)
                        <tr class="align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $entry->employee->full_name ?? 'N/A' }}</td>
                            <td>{{ $entry->work_from_home_start_date }}</td>
                            <td>{{ $entry->work_from_home_end_date }}</td>
                            <td class="d-flex gap-2">
                                @if (auth()->user()->employee->employee_id == $entry->employee_id)
                                    <a href="{{ route('workfromhome.edit', $entry->work_from_home_id) }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>



                                    <form action="{{ route('workfromhome.destroy', $entry->work_from_home_id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this request?')">Delete</button>
                                    </form>
                                @endif


                                <a href="{{ route('workfromhome.show', $entry->work_from_home_id) }}"
                                    class="btn btn-sm btn-info">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No work from home requests found.</td>
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
