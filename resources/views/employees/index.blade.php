<x-app-layout>
    <div class="mt-3">
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
                                    {{ request()->input('department') == $department->department_id ? 'selected' : '' }}>
                                    {{ $department->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <input list="expiry-options" id="contract_expiry" name="contract_expiry" class="form-control"
                            placeholder="Select or enter contract expiry (in months)"
                            value="{{ request()->input('contract_expiry') }}">
                        <datalist id="expiry-options">
                            <option value="">Select or enter contract expiry (in months)</option>
                            @foreach ($expiryOptions as $months)
                                <option value="{{ $months }}">Expiring in {{ $months }} months</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="col">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </div>
                    <div class="col">
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Reset Filters</a>
                    </div>

                    @can('add an employee')
                        <div class="col">
                            <a href="{{ route('employees.create') }}" class="btn border-t-neutral-50 btn-primary">
                                <i class="bi bi-database-add me-2"></i>Add Employee
                            </a>
                        </div>
                    @endcan
                </div>
            </form>
        </div>

        <!-- Display the number of filtered results -->
        <div class="mt-3">
            @if ($employeeCount > 0)
                <p><strong>{{ $employeeCount }}</strong> employees found based on the applied filters.</p>
            @else
                <p>No employees found with the applied filters.</p>
            @endif

            @if (count($appliedFiltersMessage) > 0)
                <p>Filters applied: {{ implode(', ', $appliedFiltersMessage) }}.</p>
            @endif
        </div>

        <div class="table-wrapper">
            <table class="table table-striped" data-toggle="table" data-show-columns="true" data-sortable="true"
                id="employees" data-show-export="true" data-show-pagination-switch="true"
                data-page-list="[20, 25, 50, 100, 500, 1000, 2000, 10000, all]" data-pagination="true">
                <thead>
                    <tr>
                        <th scope="col" data-field="id">#</th>
                        <th scope="col" data-field="first_name">First Name</th>
                        <th scope="col" data-field="last_name">Last Name</th>
                        <th scope="col" data-field="position">Position</th>
                        <th scope="col" data-field="department">Department</th>
                        <th scope="col" data-field="phone_number">Phone Number</th>
                        <th scope="col" data-field="date_of_entry">Date of Entry</th>
                        <th scope="col" data-field="contract_expiry">Contract Expiry</th>
                        <th class="col" data-field="retirement_years">Retirement Yrs</th>
                        <th scope="col" data-field="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $index => $employee)
                        <tr class="align-middle">
                            <th scope="row">
                                <a href="{{ route('employees.show', $employee->employee_id) }}"
                                    class="btn {{ $employee->contract_expiry_date && $employee->contract_expiry_date->isPast() ? 'btn-outline-danger' : 'btn-outline-primary' }}">
                                    {{ $employee->staff_id??'STAFF0000' }}
                                </a>
                            </th>
                            <td>{{ $employee->first_name }}</td>
                            <td>{{ $employee->last_name }}</td>
                            <td>{{ optional($employee->position)->position_name ?? 'N/A' }}</td>
                            <td>{{ optional($employee->department)->department_name }}</td>
                            <td>{{ $employee->phone_number }}</td>
                            <td>{{ $employee->date_of_entry ? $employee->date_of_entry->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $employee->contract_expiry_date ? $employee->contract_expiry_date->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td>{{ $employee->retirementYearsRemaining() }}</td>
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
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const input = $('#contract_expiry');
                const datalist = $('#expiry-options');
                const $table = $('#employees');

                // Listen for the 'input' event when the user types
                input.on('input', function() {
                    const filter = input.val().toLowerCase();

                    // Loop through each option and show/hide based on input
                    datalist.find('option').each(function() {
                        const option = $(this);
                        const optionText = option.val().toLowerCase();

                        if (optionText.indexOf(filter) !== -1 || filter === "") {
                            option.show(); // Show matching option
                        } else {
                            option.hide(); // Hide non-matching option
                        }
                    });
                });

                $table.bootstrapTable('hideColumn', 'contract_expiry')
                $table.bootstrapTable('hideColumn', 'position')
                $table.bootstrapTable('hideColumn', 'phone_number')
            });
        </script>
    @endpush
</x-app-layout>
