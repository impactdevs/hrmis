<x-app-layout>
    <section class="section dashboard m-2">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-12">
                {{-- Filters --}}
                <div class="d-flex align-items-center mb-3 justify-between">
                    @if (auth()->user()->isAdminOrSecretary)
                        <div class="d-flex">
                            {{-- Department Filter --}}
                            <div class="ms-3">
                                <select class="form-select form-select-sm rounded" id="departmentSelect"
                                    style="max-width: 180px;" name="department">
                                    <option value="all">All Departments</option>
                                    @foreach ($departments as $department_id => $department)
                                        <option value="{{ $department_id }}">{{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        {{-- Approval Status Filter --}}
                        <div class="d-flex align-items-center mb-3 justify-between">
                            @if (auth()->user()->isAdminOrSecretary)
                                <div class="d-flex">

                                    {{-- Approval Status Filter --}}
                                    {{-- Year Filter --}}
                                    <div class="ms-3">
                                        <select class="form-select form-select-sm rounded" id="yearSelect" name="year">
                                            <option value="all">All Years</option>
                                            @for ($y = now()->year; $y >= now()->year - 4; $y--)
                                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="ms-3">
                                        <select class="form-select form-select-sm rounded" id="monthSelect" name="month">
                                            <option value="all">All Months</option>
                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $monthName)
                                                <option value="{{ $index + 1 }}">{{ $monthName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="ms-3">
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="approval_status"
                                                id="allStatus" value="all" checked>
                                            <label class="btn btn-outline-primary btn-sm" for="allStatus">All
                                                Leaves</label>

                                            <input type="radio" class="btn-check" name="approval_status"
                                             id="activeStatus" value="active">
                                            <label class="btn btn-outline-info btn-sm" for="activeStatus">
                                                Active</label>

                                            <input type="radio" class="btn-check" name="approval_status"
                                                id="approvedStatus" value="approved">
                                            <label class="btn btn-outline-success btn-sm" for="approvedStatus">Fully
                                                Approved</label>

                                            <input type="radio" class="btn-check" name="approval_status"
                                                id="pendingStatus" value="pending">
                                            <label class="btn btn-outline-warning btn-sm"
                                                for="pendingStatus">Pending</label>

                                            <input type="radio" class="btn-check" name="approval_status"
                                                id="rejectedStatus" value="rejected">
                                            <label class="btn btn-outline-danger btn-sm"
                                                for="rejectedStatus">Rejected</label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-12">
                    <div class="card recent-sales overflow-auto vh-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title">
                                    {{ auth()->user()->isAdminOrSecretary ? 'UNCST Leave Requests' : 'My Leave Requests' }}
                                </h5>
                                {{-- check if role is HR and dont show the button --}}
                                @if (!auth()->user()->hasRole('HR'))
                                    <a class="btn btn-primary btn-sm ms-auto px-3 py-1"
                                        href="{{ route('leaves.create') }}" style="font-size: 14px;">
                                        <i class="bi bi-plus" style="font-size: 12px;"></i> Apply
                                    </a>
                                @endif
                            </div>



                            <table class="table table-striped table-bordered" id="leavePlan" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col">STAFF ID</th>
                                        <th scope="col">FULL NAME</th>
                                        <th scope="col">LEAVE TYPE</th>
                                        <th scope="col">DURATION</th>
                                        <th class="col">ACTIONS</th>
                                        <th scope="col">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>

                    </div>
                </div><!-- End Recent Sales -->
            </div><!-- End Left side columns -->
        </div>
    </section>

    <!-- Bootstrap Modal for Rejection Reason -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="rejectionReason">Please enter the reason for rejection:</label>
                    <textarea id="rejectionReason" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script type="module">
            $(document).ready(function() {
                var employeeId = @json(auth()->user()->employee->employee_id);
                var totalLeaveDaysEntitled = @json(auth()->user()->employee->entitled_leave_days);
                var totalLeaveDaysScheduled = @json(auth()->user()->employee->overallRosterDays());
                var balanceToSchedule = totalLeaveDaysEntitled - totalLeaveDaysScheduled;
                var percentageUsed = Math.min((totalLeaveDaysScheduled / totalLeaveDaysEntitled) * 100, 100);
                var canApproveLeave = @json(auth()->user()->hasAnyRole(['HR', 'Head of Division', 'Executive Secretary']));
                //get all the roles in the system
                var roles = @json($roles);

                // Update the progress bar
                $('#leaveProgressBar').css('width', percentageUsed + '%')
                    .attr('aria-valuenow', percentageUsed)
                    .text(Math.round(percentageUsed) + '%');
                // Update label with the number of scheduled days
                $('#scheduledDaysText').text(totalLeaveDaysScheduled + ' days scheduled');

                Echo.private(`roster.${employeeId}`)
                    .listen('RosterUpdate', (e) => {
                        var totalLeaveDaysEntitled = e.total_leave_days_entitled;
                        var totalLeaveDaysScheduled = e.total_leave_days_scheduled;
                        var balanceToSchedule = Number(totalLeaveDaysEntitled) - Number(totalLeaveDaysScheduled);

                        // Calculate the percentage of leave days scheduled
                        var percentageUsed = Math.min((totalLeaveDaysScheduled / totalLeaveDaysEntitled) * 100,
                            100);

                        // Update the text values in the HTML
                        $('#totalLeaveDaysEntitled').text('Total Leave Days Entitled: ' + totalLeaveDaysEntitled);
                        $('#totalLeaveDaysScheduled').text('Total Leave Days Scheduled: ' +
                            totalLeaveDaysScheduled);
                        $('#balanceToSchedule').text('Balance to Schedule: ' + balanceToSchedule);

                        // Update the progress bar
                        $('#leaveProgressBar').css('width', percentageUsed + '%')
                            .attr('aria-valuenow', percentageUsed)
                            .text(Math.round(percentageUsed) + '%');
                    });

                function loadLeaveTableData() {
                    var approvalStatus = $("input[name='approval_status']:checked")
                        .val(); // Get selected approval status
                    var department = $('#departmentSelect').val(); // Get selected department

                    $.ajax({
                        url: '{{ route('leave.data') }}',
                        type: 'GET',
                        data: {
                            approval_status: approvalStatus, // Pass the selected approval status filter
                            department: department, // Pass the selected department filter
                            year: $('#yearSelect').val(), // Pass the selected year filter
                            month: $('#monthSelect').val() // Pass the selected month filter
                        },
                        success: function(response) {
                                console.log('Filters sent - Approval:', approvalStatus,
                                    'Department:', department);
                                console.log('Data received:', response.data.length, 'records');
                                console.log(response)
                                // Check if the DataTable already exists on the #leavePlan element
                                if ($.fn.dataTable.isDataTable('#leavePlan')) {
                                    // If it exists, you can either clear it or destroy it
                                    var table = $('#leavePlan').DataTable();
                                    table.clear().draw(); // Clear existing data before updating
                                } else {
                                    // If DataTable does not exist, initialize it
                                    var table = $('#leavePlan').DataTable({
                                        scrollY: 'calc(100vh - 350px)',
                                        scrollX: true, // Enable horizontal scrolling for large tables
                                        lengthMenu: [15, 25, 50, 75, 100],
                                        // other DataTable initialization options
                                        dom: 'Bfrtip',
                                        language: {
                                            search: "_INPUT_", // Customize the search input box
                                            searchPlaceholder: "Search records"
                                        },
                                        initComplete: function() {
                                            // Optional: Customization after table initialization
                                            $('.dataTables_filter input').addClass(
                                                'form-control'
                                            ); // Add Bootstrap class to the search box
                                        },
                                        columnDefs: [{
                                                targets: 3, // Assuming the duration is in the second column (index 1)
                                                render: function(data, type, row) {
                                                    console.log(data);
                                                    // Use Bootstrap badge and apply styles for duration
                                                    return '<span class="badge bg-info text-dark">' +
                                                        data +
                                                        '</span>';
                                                }
                                            },
                                            {
                                                targets: 5,
                                                render: function(data, type, row) {
                                                    let status = '';

                                                    // Initialize the status div
                                                    let statusDiv =
                                                        '<div class="status mt-2">';

                                                    const now = new Date();
                                                    const endDate = new Date(
                                                        row[3].split(' - ')[
                                                            1]);

                                                    if ((endDate >= now) || ((
                                                                endDate < now
                                                            ) && (row[5]) &&
                                                            (row[5]
                                                                .leave_request_status
                                                            ))) {
                                                        // Check if there is a leave request status
                                                        if (row[5] && row[5]
                                                            .leave_request_status
                                                        ) {
                                                            var role =
                                                                @json(Auth::user()->roles->pluck('name')[0] ?? '');

                                                            // First, check if ANY role has rejected the leave request
                                                            var isRejected =
                                                                false;
                                                            var rejectedBy = '';
                                                            roles.forEach((
                                                                roleCheck
                                                            ) => {
                                                                if (row[
                                                                        5
                                                                        ]
                                                                    .leave_request_status[
                                                                        roleCheck
                                                                    ] ===
                                                                    'rejected'
                                                                ) {
                                                                    isRejected
                                                                        =
                                                                        true;
                                                                    rejectedBy
                                                                        =
                                                                        roleCheck;
                                                                }
                                                            });

                                                            if (isRejected) {
                                                                // If any role rejected, show rejection status
                                                                statusDiv +=
                                                                    '<span class="badge bg-danger">This leave request was rejected by ' +
                                                                    rejectedBy +
                                                                    '</span>';
                                                                if (row[5]
                                                                    .rejection_reason
                                                                ) {
                                                                    statusDiv +=
                                                                        '<p class="mt-1"><strong>Rejection Reason:</strong> ' +
                                                                        row[5]
                                                                        .rejection_reason +
                                                                        '</p>';
                                                                }
                                                            } else {
                                                                // Check current user's specific status if no rejection
                                                                if (row[5]
                                                                    .leave_request_status[
                                                                        role
                                                                    ] ===
                                                                    'approved'
                                                                ) {
                                                                    statusDiv +=
                                                                        '<span class="badge bg-success">You Approved this Leave Request.</span>';
                                                                } else if (row[
                                                                        5]
                                                                    .leave_request_status[
                                                                        role
                                                                    ] ===
                                                                    'rejected'
                                                                ) {
                                                                    statusDiv +=
                                                                        '<span class="badge bg-danger">You rejected this Request</span>';
                                                                    if (row[5]
                                                                        .rejection_reason
                                                                    ) {
                                                                        statusDiv
                                                                            +=
                                                                            '<p class="mt-1"><strong>Rejection Reason:</strong> ' +
                                                                            row[
                                                                                5
                                                                                ]
                                                                            .rejection_reason +
                                                                            '</p>';
                                                                    }
                                                                } else {
                                                                    // If no rejection and current user hasn't approved/rejected
                                                                    if (role ===
                                                                        'Staff' &&
                                                                        row[5]
                                                                        .leave_request_status[
                                                                            'Executive Secretary'
                                                                        ]) {
                                                                        const
                                                                            executiveStatus =
                                                                            row[
                                                                                5
                                                                                ]
                                                                            .leave_request_status[
                                                                                'Executive Secretary'
                                                                            ];
                                                                        if (executiveStatus ===
                                                                            'approved'
                                                                        ) {
                                                                            statusDiv
                                                                                +=
                                                                                '<span class="badge bg-success">This leave request was fully approved</span>';
                                                                        } else {
                                                                            statusDiv
                                                                                +=
                                                                                '<span class="badge bg-warning">Pending</span>';
                                                                        }
                                                                    } else {
                                                                        statusDiv
                                                                            +=
                                                                            '<span class="badge bg-warning">Pending</span>';
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            if (row[5]
                                                                .leave_id) {
                                                                statusDiv +=
                                                                    '<span class="badge bg-success">Application review in progress</span>';
                                                            } else {
                                                                statusDiv +=
                                                                    '<span class="badge bg-warning">No Application</span>';
                                                            }
                                                        }

                                                        statusDiv +=
                                                            '<p>Leave Approved By:</p>';
                                                        if (row[5] && row[5]
                                                            .leave_request_status
                                                        ) {
                                                            roles.forEach((
                                                                role
                                                            ) => {
                                                                const
                                                                    status =
                                                                    row[
                                                                        5
                                                                    ]
                                                                    .leave_request_status[
                                                                        role
                                                                    ];

                                                                // Determine the badge with improved Bootstrap styling
                                                                if (status ===
                                                                    'approved'
                                                                ) {
                                                                    statusDiv
                                                                        += `
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span class="fw-semibold text-success">${role}: Approved</span>
                </div>`;
                                                                } else if (
                                                                    status ===
                                                                    'rejected'
                                                                ) {
                                                                    statusDiv
                                                                        += `
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-x-circle-fill text-danger me-2"></i>
                    <span class="fw-semibold text-danger">${role}: Rejected</span>
                </div>`;
                                                                } else if (
                                                                    status ===
                                                                    null ||
                                                                    status ===
                                                                    undefined
                                                                ) {
                                                                    // Handle both `null` and missing roles as "Pending"
                                                                    statusDiv
                                                                        += `
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-hourglass-split text-warning me-2"></i>
                    <span class="fw-semibold text-warning">${role}: Pending</span>
                </div>`;
                                                                }
                                                            });
                                                        } else {
                                                            statusDiv +=
                                                                '<span class="badge bg-warning">No Approval Yet</span>';
                                                        }
                                                    } else {
                                                        statusDiv +=
                                                            '<span class="badge bg-secondary">Expired</span>';
                                                        // give a hint to reclaim the days by rescheduling
                                                        statusDiv +=
                                                            '<a href="{{ route('leave-roster.index') }}" class="d-block text-muted small mt-1" style="font-size: 11px; line-height: 1.2; text-decoration: underline;">Reclaim days: reschedule roster.</a>';
                                                    }

                                                    statusDiv +=
                                                        '</div>'; // Close the status div

                                                    return statusDiv;
                                                }
                                            },

                                            {
                                                targets: 1, // Assuming the first column is the employee name
                                                render: function(data, type, row) {
                                                    console.log(data)
                                                    return data; // Bold employee name
                                                }
                                            },

                                        ],
                                        fixedHeader: true, // Sticky header for large tables
                                        responsive: true, // Ensures the table is mobile-friendly
                                        ordering: false, // Disable ordering globally


                                    });
                                }
                                var rows1 = [];

                                // First, sort all data by creation date (most recent first)
                                var sortedData = response.data.sort(function(a, b) {
                                    // Use created_at if available, otherwise use start date as fallback
                                    var dateA = a.leave && a.leave.created_at ?
                                        new Date(a.leave.created_at) : new Date(a
                                            .start);
                                    var dateB = b.leave && b.leave.created_at ?
                                        new Date(b.leave.created_at) : new Date(b
                                            .start);
                                    return dateB -
                                        dateA; // Descending order (newest first)
                                });

                                var latestLeavesByStaff = {};
                                var groupedByStaff = {};

                                // Group events by staff_id
                                response.data.forEach(function(item) {
                                    if (!groupedByStaff[item.staffId]) {
                                        groupedByStaff[item.staffId] = [];
                                    }
                                    groupedByStaff[item.staffId].push(item);
                                });


                                // Iterate over each staff group and create rows
                                Object.keys(groupedByStaff).forEach(function(staffId) {
                                    var eventsForStaff = groupedByStaff[staffId];
                                    //events for staff ..
                                    var rowspan = eventsForStaff
                                        .length; // Calculate rowspan for the name cell

                                    eventsForStaff.forEach(function(event, index) {
                                        var row = [];

                                        // For the first row of the staff group, show the name and set rowspan
                                        if (index === 0) {
                                            console.log(event.is_cancelled);

                                            row[1] =
                                                `<span class="name-span fw-bold" rowspan="${rowspan}">${event.first_name} ${event.last_name}</span>`;

                                        } else {
                                            row[1] =
                                                ''; // Empty name cell for the other rows with the same staff_id
                                        }

                                        // Fill other columns
                                        row[0] = event.staffId || event.numeric_id;
                                        console.log("Leave:", event);
                                        if (event.leave.length != 0) {
                                            console.log()
                                            if (event.is_cancelled) {
                                                row[2] =
                                                    `<span class="text-danger">${event.leave.leave_category.leave_type_name} (${event.duration})</span>`;
                                                //create a span

                                            } else {
                                                //create a span here with danger
                                                row[2] =
                                                    `<span>${event.leave.leave_category.leave_type_name} (${event.duration})</span>`;

                                            }
                                        } else {
                                            row[2] = "N/A";
                                        }
                                        row[3] = formatDate(event.start) +
                                            ' - ' + formatDate(event.end);
                                        row[5] = event.leave;

                                        if (event.leave.length == 0) {
                                            // Check if the end date is in the future or today
                                            const now = new Date();
                                            const endDate = new Date(event.end);
                                            if (endDate >= now) {
                                                row[4] = `
    <div class="dropdown">
        <button class="btn btn-secondary btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i> Actions
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li>
                <a class="dropdown-item apply-btn" href="/leave-roster/${event.leave_roster_id}/apply" title="Apply">
                    <i class="bi bi-pencil"></i> Apply
                </a>
            </li>
        </ul>
    </div>
`;

                                            } else {
                                                row[4] = `
                                                    <span class="badge bg-secondary">Expired</span>
                                                `;
                                            }
                                        } else {
                                            if (canApproveLeave) {
                                                if (!event.is_cancelled) {
                                                    row[4] = `
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i> Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <li>
                                                                <a class="dropdown-item approve-btn" href="#${event.leave.leave_id}" data-leave-id="${event.leave.leave_id}" title="Approve">
                                                                    <i class="bi bi-check-circle"></i> Approve
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item reject-btn" href="#${event.leave.leave_id}" data-leave-id="${event.leave.leave_id}" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                                    <i class="bi bi-x-circle"></i> Reject
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item view-btn" href="{{ url('leaves') }}/${event.leave.leave_id}" title="View Details">
                                                                    <i class="bi bi-eye"></i> View Details
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                `;
                                                } else {
                                                    row[4] = `
                                                    <span class="badge text-danger">Cancelled</span>
                                                    `
                                                }
                                            } else {
                                                if (!event.is_cancelled) {
                                                    row[4] = `
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i> Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <li>
                                                                <a class="dropdown-item view-btn" href="{{ url('leaves') }}/${event.leave.leave_id}" title="View">
                                                                    <i class="bi bi-eye"></i> View
                                                                </a>
                                                            </li>
                                                            ${event.leave.leave_id ? `
                                                                                    ` : ''}
                                                        </ul>
                                                    </div>
                                                `;
                                                } else {
                                                    row[4] = `
                                                    <span class="badge text-danger">Cancelled</span>
                                                    `
                                                }
                                            }


                                        }
                                        // Add the row to the table
                                        rows1.push(row);
                                    });
                                });


                                // Add the rows to the DataTable
                                console.log(rows1);
                                table.clear().rows.add(rows1).draw();

                                //leave approval, rejection and application
                                let currentLeaveId;

                                $('.approve-btn').click(function() {
                                    //prevent default
                                    const leaveId = $(this).data('leave-id');
                                    updateLeaveStatus(leaveId, 'approved');
                                    loadLeaveTableData();
                                });

                                $('.reject-btn').click(function() {
                                    currentLeaveId = $(this).data('leave-id');
                                });

                                $('#confirmReject').click(function() {
                                    const reason = $('#rejectionReason').val();
                                    if (reason) {
                                        updateLeaveStatus(currentLeaveId, 'rejected',
                                            reason);

                                        $('#rejectModal').modal(
                                            'hide'); // Hide the modal
                                        loadLeaveTableData();
                                    } else {
                                        alert('Please enter a rejection reason.');
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('There was an error fetching leave data:', error);
                            }
                        });
                }

                loadLeaveTableData();

                // Listen for filter changes and reload the table
                $("input[name='approval_status']").on('change', function() {
                    console.log('Approval status changed to:', $(this).val());
                    loadLeaveTableData();
                });

                $('#yearSelect').on('change', function() {
                    console.log('Year changed to:', $(this).val());
                    loadLeaveTableData();
                });

                $('#monthSelect').on('change', function() {
                    console.log('Month changed to:', $(this).val());
                    loadLeaveTableData();
                });

                $('#departmentSelect').on('change', function() {
                    console.log('Department changed to:', $(this).val());
                    loadLeaveTableData();
                });

                // on clicking cancel-btn (now delete), send a DELETE request
                $(document).on('click', '.cancel-btn', function(e) {
                    e.preventDefault();
                    console.log('Cancel button clicked'); // Debug: Confirm click event

                    const leaveId = $(this).data('leave-id');
                    console.log('Leave ID:', leaveId); // Debug: Log leave ID

                    if (!leaveId) {
                        Toastify({
                            text: 'Invalid leave ID. Unable to delete.',
                            duration: 3000,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                        }).showToast();
                        return;
                    }

                    $.ajax({
                        url: `{{ url('leaves') }}/${leaveId}`, // Use the correct route
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Toastify({
                                text: response.message || 'Leave cancelled successfully.',
                                duration: 3000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                            }).showToast();

                            // Refresh the table
                            loadLeaveTableData();
                        },
                        error: function(xhr) {
                            console.error('Delete error:', xhr.status, xhr
                                .responseJSON); // Debug: Log detailed error
                            const errorMsg = xhr.responseJSON?.error ||
                                'An error occurred while deleting the leave.';
                            Toastify({
                                text: errorMsg,
                                duration: 3000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                            }).showToast();
                        }
                    });
                });


            });

            function formatDate(dateString) {
                const dateObj = new Date(dateString);
                const options = {
                    day: "numeric",
                    month: "short",
                    year: "numeric",
                };
                return new Intl.DateTimeFormat("en", options).format(dateObj);
            }

            function updateLeaveStatus(leaveId, status, reason = null) {
                $.ajax({
                    url: `/leaves/${leaveId}/status`,
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        status: status,
                        reason: reason
                    }),
                    success: function(data) {
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                        }).showToast();

                        // Redraw the table to reflect changes
                        $('#leavePlan').DataTable().draw(false);

                    },
                    error: function(xhr) {
                        Toastify({
                            text: xhr.responseJSON?.error || 'An error occurred',
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                        }).showToast();
                    }
                });
            }
        </script>
        <style>
            /* Custom styling for balance color */
            .balance-text {
                font-weight: bold;
            }

            /* Add hover effect on hover of the leave days section */
            .d-flex:hover {
                background-color: #e8eff7;
            }

            /* Shadow for better contrast */
            .shadow-sm {
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            }

            /* Ensure spacing around elements */
            .p-3 {
                padding: 1.5rem;
            }
        </style>
    @endpush
</x-app-layout>