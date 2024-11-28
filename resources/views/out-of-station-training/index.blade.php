<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3 font-weight-bold">Travel Clearance</h5>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('trainings.index') }}" class="btn border-t-neutral-50 btn-primary me-1">
                    <i class="bi bi-skip-backward-fill"></i>Trainings
                </a>
                <a href="{{ route('out-of-station-trainings.create') }}" class="btn border-t-neutral-50 btn-primary">
                    <i class="bi bi-database-add"></i>Apply
                </a>
            </div>

        </div>

        <div class="table-wrapper">
            {{-- events table with training title, training description, training location, training start date, training end date, and training category --}}
            <table class="table table-striped">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Employee Name</th>
                    <th scope="col">Destination</th>
                    <th scope="col">Departure Date</th>
                    <th scope="col">Return Date</th>
                    <th scope="col">Substitutes </th>
                    <th scope="col">Status</th>
                    <th scope="col">Actions</th>
                </tr>
                <tbody>
                    @forelse ($trainings as $index => $training)
                        <tr class="align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $training->user->employee->first_name }} {{ $training->user->employee->last_name }}
                            </td>
                            <td>{{ $training->destination }}</td>
                            <td>{{ $training->departure_date->format('d/m/Y') }}</td>
                            <td>{{ $training->return_date->format('d/m/Y') }}</td>

                            <td>
                                @if (!is_null($training->my_work_will_be_done_by))
                                    <ul type="disc">
                                        @foreach ($training->my_work_will_be_done_by as $key => $substitute)
                                            @if (!is_null($substitute))
                                                <li>{{ $options[$key][$substitute] }}</li>
                                            @else
                                                -
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if (!is_null($training->training_request_status))
                                    <div class="status m-2">
                                        @foreach ($training->training_request_status as $key => $status)
                                            <span
                                                class="status-{{ $key }}">{{ $key }}-{{ $status }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
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
                                                href="{{ route('out-of-station-trainings.edit', $training->training_id) }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('out-of-station-trainings.show', $training->training_id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>

                                        <li>
                                            <form
                                                action="{{ route('out-of-station-trainings.destroy', $training->training_id) }}"
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
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-danger">
                                No Requests Found
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
