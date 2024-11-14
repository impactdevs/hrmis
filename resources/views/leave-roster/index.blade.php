<x-app-layout>
    @if (auth()->user()->isAdminOrSecretary())
        <div class="d-flex flex-row justify-content-between mb-5 mt-2">
            <h1 class="text-center fs-1">Leave Roster</h1>
            {{-- Modes i.e. edit or view --}}
            <div class="d-flex justify-content-center align-items-center">
                <a href="#"
                    class="btn btn-outline-primary ms-3 table-mode btn-sm text-center d-flex align-items-center"
                    id="editModeBtn">
                    <i class="bi bi-pencil-square me-2"></i> Edit Mode
                </a>
                <a href="#" class="btn btn-primary ms-3 table-mode btn-sm text-center d-flex align-items-center"
                    id="viewModeBtn">
                    <i class="bi bi-eye me-2"></i> View Mode
                </a>
            </div>
        </div>

        <!-- Bootstrap Tabs for Departments -->
        <ul class="nav nav-tabs nav-fill nav-underline" id="myTab" role="tablist">
            @foreach ($departments as $index => $department)
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $index == 0 ? 'active' : '' }}" id="tab-{{ $department->department_id }}"
                        data-bs-toggle="tab" href="#content-{{ $department->department_id }}" role="tab">
                        {{ $department->department_name }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content" id="myTabContent">
            @foreach ($departments as $index => $department)
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                    id="content-{{ $department->department_id }}" role="tabpanel">
                    <div class="card mt-1">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover leave-roster"
                                id="{{ $department->department_id }}">
                                <thead class="table-light">
                                    <tr>
                                        <th class="align-middle">
                                            <h1 class="fs-3">{{ $department->department_name }}</h3>
                                        </th>
                                        {{-- Loop for each month from January to December --}}
                                        @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                            <th class="table-primary text-light">{{ $month }}</th>
                                        @endforeach
                                        <th>Total</th>
                                        <th>Used</th>
                                        <th>Bal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop through the employees in the department --}}
                                    @foreach ($department->employees as $employee)
                                        <tr>
                                            <td>{{ $employee->first_name . ' ' . $employee->last_name }}</td>

                                            {{-- Loop through each month and create editable cells for leave data --}}
                                            @foreach (range(1, 12) as $monthIndex)
                                                <td class="table-info" contenteditable="false"
                                                    data-month="{{ $monthIndex }}"
                                                    data-employee-id="{{ $employee->employee_id }}">
                                                    {{ $employee->leaveRoster->months[$employee->leaveRoster->year][$monthIndex] ?? '' }}
                                                </td>
                                            @endforeach

                                            <td>{{ $employee->leaveRoster->totalLeaveDays() }}</td>
                                            <td>{{ $employee->totalLeaveDays() }}</td>
                                            <td>{{ $employee->leaveRoster->totalLeaveDays() - $employee->totalLeaveDays() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @push('scripts')
            <style>
                th {
                    font-weight: bold;
                    font-size: 20px;
                }

                td {
                    font-size: 14px;
                }

                .table-primary {
                    background-color: #007bff !important;
                }

                .table-info {
                    background-color: #d1ecf1 !important;
                }

                .table-secondary {
                    background-color: #f8d7da !important;
                }

                .leave-row:nth-child(even) {
                    background-color: #f9f9f9;
                }

                .leave-row:nth-child(odd) {
                    background-color: #ffffff;
                }

                .leave-row:hover {
                    background-color: #f1f1f1;
                }

                .editable-cell {
                    background-color: #fffbe6 !important;
                    border: 1px solid #f1c232;
                }

                .editable-cell:focus {
                    background-color: #fff5b1;
                    border-color: #f0ad4e;
                }
            </style>

            <script type="module">
                $(document).ready(function() {
                    // Initialize DataTable if needed
                    let table = $('.leave-roster').dataTable({
                        dom: 'Bfrltip',
                        buttons: [{
                                extend: "csv",
                                className: "btn btn-primary btn-small text-white",
                            },
                            {
                                extend: "excel",
                                className: "btn btn-primary btn-small text-white",
                            },
                            {
                                extend: "pdf",
                                className: "btn btn-warning btn-small text-white",
                                customize: function(doc) {
                                    doc.styles.tableHeader.fillColor = '#FFA500';
                                }
                            },
                            {
                                extend: "print",
                                className: "btn btn-primary btn-small text-white",
                            },
                        ],
                    });

                    // Toggle edit/view modes
                    let isEditMode = false;

                    $('#editModeBtn').on('click', function() {
                        isEditMode = true;
                        toggleEditableCells();
                        $('#editModeBtn').addClass('btn-primary').removeClass('btn-outline-primary');
                        $('#viewModeBtn').addClass('btn-outline-primary').removeClass('btn-primary');
                    });

                    $('#viewModeBtn').on('click', function() {
                        isEditMode = false;
                        toggleEditableCells();
                        $('#viewModeBtn').addClass('btn-primary').removeClass('btn-outline-primary');
                        $('#editModeBtn').addClass('btn-outline-primary').removeClass('btn-primary');
                    });

                    // Toggle contenteditable attribute based on mode
                    function toggleEditableCells() {
                        $('td').each(function() {
                            if (isEditMode) {
                                $(this).attr('contenteditable', 'true').addClass('editable-cell');
                            } else {
                                $(this).attr('contenteditable', 'false').removeClass('editable-cell');
                            }
                        });
                    }

                    // Save data when a cell is edited (you can modify this to send data to the server)
                    $('td').on('blur', function() {
                        let leaveData = $(this).text(); // Get the edited text
                        let month = $(this).data('month');
                        let employeeId = $(this).data('employee-id');
                        let year = new Date().getFullYear();
                        console.log('Employee ID:', employeeId, 'Month:', month, 'Year:', year);

                        if (leaveData.trim() === "") {
                            console.log("No data entered for Employee ID:", employeeId, "Month:", month);
                        } else {
                            console.log('Saving data:', leaveData, 'for Employee ID:', employeeId, 'Month:', month);

                            // Example of an AJAX request to save the data to the server
                            $.ajax({
                                url: '/save-leave-data', // Replace with your actual endpoint
                                method: 'POST',
                                data: {
                                    employee_id: employeeId,
                                    month: month,
                                    number_of_leave_days: leaveData,
                                    year: year
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Toastify({
                                        text: 'Successfully updated roster data.',
                                        duration: 3000,
                                        destination: "",
                                        newWindow: true,
                                        close: true,
                                        gravity: "top",
                                        position: "right",
                                        stopOnFocus: true,
                                        style: {
                                            background: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%);",
                                        },
                                    }).showToast();
                                },
                                error: function(error) {
                                    Toastify({
                                        text: 'Error saving updating roster data.',
                                        duration: 3000,
                                        destination: "",
                                        newWindow: true,
                                        close: true,
                                        gravity: "top",
                                        position: "right",
                                        stopOnFocus: true,
                                        style: {
                                            background: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%);",
                                        },
                                    }).showToast();
                                }
                            });
                        }
                    });
                });
            </script>
        @endpush
    @else
        <div class="container">
            <div class="card w-100 mt-2 p-3 h-100">
                @foreach ($departments as $index => $department)
                    @foreach ($department->employees as $employee)
                        <div class="card-body">
                            <div class="title text-center">
                                <h1 class="fs-1 font-weight-bold">Leave Roster</h1>
                            </div>

                            <div class="d-flex flex-column">
                                {{-- Initialize total leave days variable --}}
                                @php
                                    $totalLeaveDays = 0;
                                @endphp

                                {{-- Iterate through each month from Jan to Dec --}}
                                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                                    @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                        {{-- Create a card for each month --}}
                                        <div class="col">
                                            <div class="card text-bg-primary">
                                                <div class="card-body">
                                                    <p class="card-title">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <strong>{{ $month }}</strong>
                                                    </p>

                                                    {{-- Calculate the leave days for the month --}}
                                                    @php
                                                        $monthIndex =
                                                            array_search($month, [
                                                                'Jan',
                                                                'Feb',
                                                                'Mar',
                                                                'Apr',
                                                                'May',
                                                                'Jun',
                                                                'Jul',
                                                                'Aug',
                                                                'Sep',
                                                                'Oct',
                                                                'Nov',
                                                                'Dec',
                                                            ]) + 1;

                                                        $leaveDays =
                                                            $employee->leaveRoster->months[
                                                                $employee->leaveRoster->year
                                                            ][$monthIndex] ?? 0;

                                                        // Add to total leave days
                                                        $totalLeaveDays += $leaveDays;
                                                    @endphp

                                                    {{-- Show leave days for this month --}}
                                                    <p class="mb-1">Leave Days: {{ $leaveDays }}</p>
                                                    <p class="mb-1">Leave Days Used:
                                                        @if (array_key_exists($monthIndex, $employee->leaveDaysConsumedPerMonth()[$employee->leaveRoster->year]))
                                                            {{ $employee->leaveDaysConsumedPerMonth()[$employee->leaveRoster->year][$monthIndex] }}
                                                        @else
                                                            0
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Total leave days for the year --}}
                                <div class="mt-4 text-center">
                                    <hr>
                                    <h4>Total Leave Days for {{ $employee->leaveRoster->year }}: {{ $totalLeaveDays }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
