<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3">Events Management</h5>
            <div>
                <a href="{{ route('events.create') }}" class="btn border-t-neutral-50 btn-primary">
                    <i class="bi bi-database-add me-2"></i>Add Event
                </a>
            </div>
        </div>

        <div class="table-wrapper">
            {{-- events table with training title, training description, training location, training start date, training end date, and training category --}}
            <table class="table table-striped">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Event Title</th>
                    <th scope="col">Event Description</th>
                    <th scope="col">Event Location</th>
                    <th scope="col">Event Start Date</th>
                    <th scope="col">Event End Date</th>
                    <th scope="col">Event Category</th>
                    <th scope="col">Actions</th>
                </tr>
                <tbody>
                    @foreach ($events as $index => $event)
                        <tr class="align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $event->event_title }}</td>
                            <td>{{ $event->event_description }}</td>
                            <td>{{ $event->event_location }}</td>
                            <td>{{ $event->event_start_date->format('d/m/Y') }}</td>
                            <td>{{ $event->event_end_date->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    // Assume training_category contains comma-separated IDs for each category
                                    $userIds = explode(',', $event->category['users'] ?? '');
                                    $departmentIds = explode(',', $event->category['departments'] ?? '');
                                    $positionIds = explode(',', $event->category['positions'] ?? '');
                                @endphp

                                @foreach ($userIds as $id)
                                    <span class="badge bg-primary">{{ $options['users'][$id] ?? 'Unknown User' }}</span>
                                @endforeach

                                @foreach ($departmentIds as $id)
                                    <span
                                        class="badge bg-success">{{ $options['departments'][$id] ?? 'Unknown Department' }}</span>
                                @endforeach

                                @foreach ($positionIds as $id)
                                    <span
                                        class="badge bg-info">{{ $options['positions'][$id] ?? 'Unknown Position' }}</span>
                                @endforeach
                            </td>
                            <td class="align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('events.edit', $event->event_id) }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('events.show', $event->event_id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('events.destroy', $event->event_id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

            <div class="pagination-wrapper">
                {!! $events->appends(['search' => request()->get('search'), 'position' => request()->get('position')])->render() !!}
            </div>
        </div>
    </div>
</x-app-layout>
