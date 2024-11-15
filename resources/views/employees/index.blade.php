<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3">Employee Management</h5>
            @can('add an employee')
                <div>
                    <a href="{{ route('employees.create') }}" class="btn border-t-neutral-50 btn-primary">
                        <i class="bi bi-database-add me-2"></i>Add Employee
                    </a>
                </div>
            @endcan
        </div>

        <div class="mt-3">
            <form method="get" action="{{ route('employees.index') }}" class="mb-3">
                <div class="row">
                    <div class="col">
                        <input type="text" name="search" class="form-control" placeholder="Search by name"
                            value="{{ request()->input('search') }}">
                    </div>
                    <div class="col">
                        <select name="position" class="form-select">
                            <option value="">Select Position</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->position_id }}"
                                    {{ request()->input('position') == $position->position_id ? 'selected' : '' }}>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <select name="department" class="form-select">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->department_id }}"
                                    {{ request()->input('department_id') == $department->department_id ? 'selected' : '' }}>
                                    {{ $department->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Position</th>
                        <th scope="col">Department</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">Date of Entry</th>
                        <th scope="col">Contract Expiry</th>
                        <th class="col">No. of Years to Retirement</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $index => $employee)
                        <tr class="align-middle">
                            <th scope="row"><a href="#"
                                    class="btn btn-outline-danger">{{ $employee->staff_id }}</a></th>
                            <td>{{ $employee->first_name }}</td>
                            <td>{{ $employee->last_name }}</td>
                            <td>{{ optional($employee->position)->position_name ?? 'N/A' }}</td>
                            <td>{{ optional($employee->department)->department_name }}</td>
                            <td>{{ $employee->phone_number }}</td>
                            <td>{{ $employee->date_of_entry ? $employee->date_of_entry->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $employee->contract_expiry_date ? $employee->contract_expiry_date->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td>
                                {{ $employee->retirementYearsRemaining() }}
                            </td>
                            <td class="align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('employees.show', $employee->employee_id) }}">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </li>
                                        @can('can edit an employee')
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('employees.edit', $employee->employee_id) }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </li>
                                        @endcan
                                        @can('can delete an employee')
                                            <li>
                                                <form action="{{ route('employees.destroy', $employee->employee_id) }}"
                                                    method="POST" style="display:inline;">
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
                    @endforeach
                </tbody>
            </table>

            <div class="pagination-wrapper">
                {!! $employees->appends(['search' => request()->get('search'), 'position' => request()->get('position')])->render() !!}
            </div>
        </div>
    </div>
</x-app-layout>
