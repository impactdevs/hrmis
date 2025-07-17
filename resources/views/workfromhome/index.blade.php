<x-app-layout>
    <div class="mt-3">
        <div class="flex-row flex-1 d-flex justify-content-between">
            @can('can add work from home request')
                <div>
                    <a href="{{ route('workfromhome.create') }}" class="btn border-t-neutral-50 btn-primary">
                        <i class="bi bi-house-add me-2"></i>Add Work From Home
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
                        <!-- <th scope="col">Reason</th>
                        <th scope="col">Location</th>
                        <th scope="col">Attachment</th>
                        <th scope="col">Task</th> -->
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
                            <!-- <td>{{ $entry->work_from_home_reason }}</td>
                            <td>{{ $entry->work_location }}</td>

                            <td>
                                @if ($entry->work_from_home_attachments)
                                    <a href="{{ asset('storage/' . $entry->work_from_home_attachments) }}" target="_blank">
                                        View
                                    </a>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td style="max-width: 200px; overflow-y: auto;">
                                @if ($entry->task && $entry->task instanceof \Illuminate\Support\Collection)
                                    @foreach ($entry->task as $item)
                                        <div>
                                            <strong>{{ $item->description }}</strong><br>
                                        </div>
                                    @endforeach
                                    @else
                                    <span class="text-muted">No task submitted</span>
                                @endif
                            </td> -->
                            <td class="d-flex gap-2">
                                @can('edit work from home request')
                                    <a href="{{ route('workfromhome.edit', $entry->work_from_home_id) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                @endcan

                                @can('delete work from home request')
                                    <form action="{{ route('workfromhome.destroy', $entry->work_from_home_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')">Delete</button>
                                    </form>
                                @endcan

                                <a href="{{ route('workfromhome.show', $entry->work_from_home_id) }}" class="btn btn-sm btn-info">
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
