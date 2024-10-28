<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3">Training Management</h5>
            <div>
                <a href="{{ route('trainings.create') }}" class="btn border-t-neutral-50 btn-primary">
                    <i class="bi bi-database-add me-2"></i>Add Training
                </a>
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
                    @foreach ($trainings as $index => $training)
                        <tr class="align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $training->training_title }}</td>
                            <td>{{ $training->training_description }}</td>
                            <td>{{ $training->training_location }}</td>
                            <td>{{ $training->training_start_date->format('d/m/Y') }}</td>
                            <td>{{ $training->training_end_date->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    // Assume training_category contains comma-separated IDs for each category
                                    $userIds = explode(',', $training->training_category['users'] ?? '');
                                    $departmentIds = explode(',', $training->training_category['departments'] ?? '');
                                    $positionIds = explode(',', $training->training_category['positions'] ?? '');
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
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('trainings.edit', $training->training_id) }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('trainings.show', $training->training_id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>
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
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <div class="pagination-wrapper">
                {!! $trainings->appends(['search' => request()->get('search'), 'position' => request()->get('position')])->render() !!}
            </div>
        </div>
    </div>
</x-app-layout>
