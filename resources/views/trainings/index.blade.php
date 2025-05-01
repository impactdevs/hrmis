<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3"></h5>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('out-of-station-trainings.index') }}" class="btn border-t-neutral-50 btn-primary me-1">
                    <i class="bi bi-airplane"></i>
                    Travel Clearance
                </a>
                @can('can add a training')
                    <a href="{{ route('trainings.create') }}" class="btn border-t-neutral-50 btn-primary">
                        <i class="bi bi-database-add"></i>Add Training
                    </a>
                @endcan
            </div>

        </div>

        <div class="table-wrapper">
            {{-- events table with training title, training description, training location, training start date, training end date, and training category --}}
            <table class="table table-striped">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Training Title</th>
                    <th scope="col">Training Description</th>
                    <th scope="col">Training Location</th>
                    <th scope="col">Training Start Date</th>
                    <th scope="col">Training End Date</th>
                    <th scope="col">Training Category</th>
                    <th scope="col">Actions</th>
                </tr>
                <tbody>
                    @forelse ($trainings as $index => $training)
                        <tr class="align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $training->training_title }}</td>
                            <td>{{ $training->training_description }}</td>
                            <td>{{ $training->training_location }}</td>
                            <td>{{ $training->training_start_date->format('d/m/Y') }}</td>
                            <td>{{ $training->training_end_date->format('d/m/Y') }}</td>
                            <td>
                                @if (!is_null($training->training_category))
                                    @php
                                        // Assume training_category contains comma-separated IDs for each category
                                        $userIds = explode(',', $training->training_category['users'] ?? '');
                                        $departmentIds = explode(
                                            ',',
                                            $training->training_category['departments'] ?? '',
                                        );
                                        $positionIds = explode(',', $training->training_category['positions'] ?? '');
                                    @endphp
                                    @foreach ($userIds as $id)
                                        @if ($id == '')
                                            <span class="badge bg-primary">All Users</span>
                                        @else
                                            <span
                                                class="badge bg-primary">{{ $options['users'][$id] ?? 'Unknown User' }}</span>
                                        @endif
                                    @endforeach

                                    @foreach ($departmentIds as $id)
                                        @if (!($id == ''))
                                            <span
                                                class="badge bg-success">{{ $options['departments'][$id] ?? 'Unknown Department' }}</span>
                                        @endif
                                    @endforeach

                                    @foreach ($positionIds as $id)
                                        @if (!($id == ''))
                                            <span
                                                class="badge bg-info">{{ $options['positions'][$id] ?? 'Unknown Position' }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">{{ $options['users'][$training->user_id] }}
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @can('can edit a training')
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('trainings.edit', $training->training_id) }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </li>
                                        @endcan
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('trainings.show', $training->training_id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>
                                        @can('can delete a training')
                                            <li>
                                                <form action="{{ route('trainings.destroy', $training->training_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-danger">
                                No Training records found for the selected date.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="pagination-wrapper">
                {!! $trainings->appends(['search' => request()->get('search'), 'position' => request()->get('position')])->render() !!}
            </div>
        </div>
    </div>
</x-app-layout>
