<x-app-layout>
    <section class="section dashboard m-2">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-8">
                {{-- Filters --}}
                <div class="d-flex align-items-center mb-3 justify-between">
                    @if (auth()->user()->isAdminOrSecretary())
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
                    @endif
                </div>
                <div class="col-12">
                    <div class="card recent-sales overflow-auto vh-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title">Staff Leave Requests</h5>
                                <a class="btn btn-primary btn-sm ms-auto px-3 py-1" href="{{ route('leaves.create') }}"
                                    style="font-size: 14px;">
                                    <i class="bi bi-plus" style="font-size: 12px;"></i> Apply
                                </a>
                            </div>



                            <table class="table table-striped table-bordered" id="leavePlan" cellspacing="0"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
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

            <!-- Right side columns -->
            <div class="col-lg-4">
                <div class="row">
                    <!-- Recent Sales -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">
                            <div class="card-body">
                                <div class="p-3">
                                    <p class="fs-1 text-primary title">
                                        UPCOMING SCHEDULES
                                    </p>
                                </div>
                                {{-- Calendar --}}
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Right side columns -->
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">
                        <i class="bi bi-calendar-plus"></i> Add Leave Roster
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('leave-roster.store') }}" accept-charset="UTF-8"
                        class="form-horizontal" enctype="multipart/form-data" id="leaveRosterForm">
                        @csrf
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>

                        <!-- Hidden input for leave title -->
                        <input type="hidden" name="leave_title" value="New Leave">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyButton"
                        data-bs-dismiss="modal">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="eventOffCanvas" aria-labelledby="eventOffCanvasLabel">
        <div class="offcanvas-header d-flex justify-content-between align-items-center">
            <h5 id="eventOffCanvasLabel" class="fs-5 fw-bold">Roster Options</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="applyLeave" title="Apply for leave">
                    <i class="bi bi-pencil"></i> Apply Leave
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" id="deleteEvent" title="Delete event">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>

        <div class="offcanvas-body">
            <!-- Event Details Section -->
            <div class="mb-4">
                <h6 class="text-muted">Roster Details</h6>

                <!-- Start and End Dates -->
                <div class="d-flex justify-content-between mb-2">
                    <strong class="text-secondary">Start Date:</strong>
                    <span id="eventStartDate" class="text-dark">2024-12-01 10:00 AM</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <strong class="text-secondary">End Date:</strong>
                    <span id="eventEndDate" class="text-dark">2024-12-05 05:00 PM</span>
                </div>

                <!-- Staff Info -->
                <div class="d-flex justify-content-between mb-2">
                    <strong class="text-secondary">Staff:</strong>
                    <span id="eventStaffName" class="text-dark">John Doe</span>
                </div>

                <!-- Rejection Reason Section -->
                <div id="rejectionReasonSection" class="mt-3" style="display: none;">
                    <strong class="text-danger">Rejection Reason:</strong>
                </div>
            </div>

            <!-- Hint Section -->
            <div class="text-muted mt-5 border-top pt-3">
                <p class="mb-1">To apply for leave, click the <i class="bi bi-pencil"></i> icon on the top right
                    corner of this card.</p>
            </div>
        </div>
    </div>

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
        <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css' rel='stylesheet' />
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.15/index.global.min.js"></script>
        <script type="module">
            $(document).ready(function() {
                var employeeId = @json(auth()->user()->employee->employee_id);
                var totalLeaveDaysEntitled = @json(auth()->user()->employee->entitled_leave_days);
                var totalLeaveDaysScheduled = @json(auth()->user()->employee->overallRosterDays());
                var balanceToSchedule = totalLeaveDaysEntitled - totalLeaveDaysScheduled;
                var percentageUsed = Math.min((totalLeaveDaysScheduled / totalLeaveDaysEntitled) * 100, 100);
                var canApproveLeave = @json(auth()->user()->can('approve-leave'));
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

                var calendarEl = $('#calendar');
                var currentEvent = null;
                var listTitle = @json(auth()->user()->isAdminOrSecretary() ? 'Leave Roster' : 'My Leaves');
                var calendar = new FullCalendar.Calendar(calendarEl[0], {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        // right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay, listYear'
                        right: 'dayGridMonth'
                    },
                    height: 'auto',
                    contentHeight: 'auto',
                    buttonText: {
                        dayGridMonth: 'Month',
                    },
                    views: {},
                    eventContent: function(arg) {
                        var firstName = arg.event.extendedProps.first_name;
                        var lastName = arg.event.extendedProps.last_name;
                        var title = firstName + ' ' + lastName;

                        // Check the isApproved status and set the appropriate icon and color
                        var approvalStatusText = '';
                        var statusColor = '';
                        var statusIcon = '';

                        if (arg.event.extendedProps.isApproved === null) {
                            approvalStatusText = 'Pending';
                            statusColor = 'violet'; // You can use a color name or hex code
                            statusIcon = '<i class="bi bi-clock"></i>'; // Bootstrap clock icon for pending
                        } else if (arg.event.extendedProps.isApproved === true) {
                            approvalStatusText = 'Approved';
                            statusColor = 'green';
                            statusIcon =
                                '<i class="bi bi-check-circle"></i>'; // Bootstrap check-circle icon for approved
                        } else if (arg.event.extendedProps.isApproved === false) {
                            approvalStatusText = 'Rejected';
                            statusColor = 'red';
                            statusIcon =
                                '<i class="bi bi-x-circle"></i>'; // Bootstrap x-circle icon for rejected
                        }

                        return {
                            html: '<div class="d-flex align-items-center">' +
                                '<div class="me-2">' +
                                '<span class="text-' +
                                statusColor +
                                '">' +
                                statusIcon +
                                '</span>' +
                                '</div>' +
                                '<div class="flex-grow-1">' +
                                title +
                                '</div>' +
                                '</div>'
                        };
                    },

                    events: function(fetchInfo, successCallback, failureCallback) {
                        var approvalStatus = $("input[name='approval_status']:checked")
                            .val(); // Get selected approval status
                        var department = $('#departmentSelect').val(); // Get selected department

                        $.ajax({
                            url: '{{ route('leave.data') }}',
                            type: 'GET',
                            data: {
                                approval_status: approvalStatus, // Pass the selected approval status filter
                                department: department // Pass the selected department filter
                            },
                            success: function(response) {
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

                                                    // Check if there is a leave request status
                                                    if (row[5] && row[5]
                                                        .leave_request_status) {
                                                        var role =
                                                            @json(Auth::user()->roles->pluck('name')[0] ?? '');

                                                        // Check if role exists in the leave request status
                                                        if (row[5]
                                                            .leave_request_status[
                                                                role] ===
                                                            'approved') {
                                                            statusDiv +=
                                                                '<span class="badge bg-success">You Approved this Leave Request.</span>';
                                                        } else if (row[5]
                                                            .leave_request_status[
                                                                role] ===
                                                            'rejected') {
                                                            statusDiv +=
                                                                '<span class="badge bg-danger">You rejected this Request</span>';
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
                                                            console.log(row[5])
                                                            // If the status is neither approved nor rejected
                                                            if (role ===
                                                                'Staff' && row[
                                                                    5]
                                                                .leave_request_status[
                                                                    'Executive Secretary'
                                                                ]) {
                                                                const
                                                                    executiveStatus =
                                                                    row[5]
                                                                    .leave_request_status[
                                                                        'Executive Secretary'
                                                                    ];
                                                                if (executiveStatus ===
                                                                    'approved'
                                                                ) {
                                                                    statusDiv +=
                                                                        '<span class="badge bg-success">This leave request was fully approved</span>';
                                                                } else if (
                                                                    executiveStatus ===
                                                                    'rejected'
                                                                ) {
                                                                    statusDiv +=
                                                                        '<span class="badge bg-danger">This leave request was rejected</span>';
                                                                } else {
                                                                    statusDiv +=
                                                                        '<span class="badge bg-warning">Pending</span>';
                                                                }
                                                            } else {
                                                                statusDiv +=
                                                                    '<span class="badge bg-warning">Pending</span>';
                                                            }
                                                        }
                                                    } else {
                                                        if (row[5].leave_id) {
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
                                                        .leave_request_status) {
                                                        roles.forEach((
                                                            role) => {
                                                            const
                                                                status =
                                                                row[5]
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

                                                    statusDiv +=
                                                        '</div>'; // Close the status div

                                                    return statusDiv;
                                                }
                                            },

                                            {
                                                targets: 1, // Assuming the first column is the employee name
                                                render: function(data, type, row) {
                                                    return '<span class="fw-bold">' +
                                                        data +
                                                        '</span>'; // Bold employee name
                                                }
                                            },

                                        ],
                                        fixedHeader: true, // Sticky header for large tables
                                        responsive: true, // Ensures the table is mobile-friendly
                                        ordering: false, // Disable ordering globally


                                    });
                                }
                                var rows1 = [];
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
                                            row[1] =
                                                `<span class="name-span" rowspan="${rowspan}">${event.first_name} ${event.last_name}</span>`;
                                        } else {
                                            row[1] =
                                                ''; // Empty name cell for the other rows with the same staff_id
                                        }

                                        // Fill other columns
                                        row[0] = event.numeric_id;
                                        console.log("Leave:", event);
                                        if (event.leave.length > 0) {
                                            row[2] = event.leave.leave_category
                                                .leave_type_name + "(" + event
                                                .duration + ")";
                                        } else {
                                            row[2] = "N/A";
                                        }
                                        row[3] = formatDate(event.start) +
                                            ' - ' + formatDate(event.end);
                                        row[5] = event.leave;

                                        if (event.leave.length == 0) {
                                            row[4] = `
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i> Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <li>
                                                                <a class="dropdown-item apply-btn" href="/apply-for-leave/${event.leave_roster_id}" title="Apply">
                                                                    <i class="bi bi-pencil"></i> Apply
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                `;

                                        } else {
                                            if (canApproveLeave) {
                                                row[4] = `
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i> Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <li>
                                                                <a class="dropdown-item approve-btn" href="#" data-leave-id="${event.leave.leave_id}" title="Approve">
                                                                    <i class="bi bi-check-circle"></i> Approve
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item reject-btn" href="#" data-leave-id="${event.leave.leave_id}" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                                    <i class="bi bi-x-circle"></i> Reject
                                                                </a>
                                                            </li>
                                                              <li>
                                                                <a class="dropdown-item apply-btn" href="/leaves/${event.leave.leave_id}" title="Apply">
                                                                    <i class="bi bi-pencil"></i> View Details
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                `;
                                            } else {
                                                row[4] = `
                                                <div class="dropdown">
                                                        <button class="btn btn-secondary btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i> Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                              <li>
                                                                <a class="dropdown-item apply-btn" href="/leaves/${event.leave.leave_id}" title="Apply">
                                                                    <i class="bi bi-pencil"></i> View
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>`
                                            }


                                        }
                                        // Add the row to the table
                                        rows1.push(row);
                                    });
                                });


                                // Return the events to FullCalendar
                                var events = [];
                                response.data.forEach(function(event) {
                                    events.push({
                                        id: event.leave_roster_id,
                                        title: event.title,
                                        start: event.start,
                                        staff_id: event.staffId,
                                        first_name: event.first_name,
                                        last_name: event.last_name,
                                        isApproved: event.isApproved,
                                        end: event.end,
                                        color: 'blue',
                                        fullDay: true
                                    });
                                });

                                successCallback(events);
                                // Add the rows to the DataTable
                                console.log(rows1);
                                table.clear().rows.add(rows1).draw();

                                //leave approval, rejection and application
                                let currentLeaveId;

                                $('.approve-btn').click(function() {
                                    //prevent default
                                    const leaveId = $(this).data('leave-id');
                                    updateLeaveStatus(leaveId, 'approved');
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
                                    } else {
                                        alert('Please enter a rejection reason.');
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('There was an error fetching events:', error);
                                failureCallback(error);
                            }
                        });
                    },
                    editable: true,
                    droppable: true,
                    eventClick: function(info) {
                        // Get the event data
                        currentEvent = info.event;

                        $('#eventStartDate').text(moment(currentEvent.start).format(
                            'YYYY-MM-DD HH:mm')); // Display formatted start date
                        $('#eventEndDate').text(moment(currentEvent.end).format(
                            'YYYY-MM-DD HH:mm')); // Display formatted end date
                        $('#eventStaffName').text(currentEvent.extendedProps.first_name + ' ' + currentEvent
                            .extendedProps.last_name); // Display staff name

                        $('#applyLeave').show(); // Show the apply leave button if approved

                        // Display the event's approval status in the offcanvas
                        var approvalStatus = currentEvent.extendedProps.isApproved === true ? 'Approved' :
                            (currentEvent.extendedProps.isApproved === false ? 'Rejected' : 'Pending');
                        $('#eventApprovalStatusText').text(
                            approvalStatus); // Display approval status

                        // Show the rejection reason section if the event is rejected
                        if (currentEvent.extendedProps.isApproved === false) {
                            $('#rejectionReasonSection').show();
                            $('#rejectionReasonText').text(currentEvent.extendedProps.rejection_reason ||
                                'No rejection reason provided.');
                        } else {
                            $('#rejectionReasonSection')
                                .hide(); // Hide rejection reason section if not rejected
                        }

                        // Trigger the offcanvas to show
                        var offcanvas = new bootstrap.Offcanvas(document.getElementById('eventOffCanvas'));
                        offcanvas.show();
                    }

                });

                calendar.render();

                // Listen for filter changes and update the calendar
                $("input[name='approval_status']").on('change', function() {
                    calendar.refetchEvents(); // Re-fetch events based on the new filter
                });

                $('#departmentSelect').on('change', function() {
                    calendar.refetchEvents(); // Re-fetch events based on the new filter
                });
                //delete leave roster
                $('#deleteEvent').click(function() {
                    $.ajax({
                        url: "{{ route('leave-roster.destroy', '') }}/" + currentEvent.id,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(
                                'eventOffCanvas'));
                            offcanvas.hide();
                            calendar.getEventById(currentEvent.id).remove()
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting event:', error);
                        }
                    });
                });

                // apply leave
                $('#applyLeave').click(function() {
                    //navigate to apply for leave with leave roster id current event.id
                    window.location.href = "/apply-for-leave/" + currentEvent.id;
                });

                //addLeaveRoster
                $('#addLeaveRoster').click(function() {
                    // show applyModal
                    $('#applyModal').modal('show');

                });

                $('#applyButton').click(function() {
                    // Extract form values
                    var start_date = $('#start_date').val();
                    var end_date = $('#end_date').val();
                    var leave_title = $("input[name='leave_title']").val();

                    // Send the data via AJAX POST request

                    $.ajax({
                        url: "{{ route('leave-roster.store') }}",
                        method: 'POST',
                        data: {
                            start_date: start_date,
                            end_date: end_date,
                            leave_title: leave_title,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log(response);

                            // Add the event to the DataTable at the top (front)
                            var table = $('#leavePlan').DataTable();

                            // Calculate the numerical id
                            var numericId = table.rows().count() + 1;

                            var row = [
                                numericId,
                                response.data.employee.first_name + ' ' + response.data
                                .employee.last_name,
                                formatDate(response.data.start_date) + ' - ' +
                                formatDate(response.data.end_date)
                            ];

                            // Insert the new row at the top (position 0)
                            table.rows.add([row]).draw(
                                false
                            ); // `false` ensures the table isn't re-sorted after adding the row

                            // Optionally, if you want to sort by numeric_id, you can add this:
                            table.order([0, 'desc']).draw();
                            calendar.addEvent({
                                title: 'New Leave',
                                start: response.data.start_date,
                                end: response.data.end_date,
                                backgroundColor: 'yellow',
                                borderColor: 'orange',
                                textColor: 'black',
                                id: response.data.leave_roster_id,
                                first_name: response.data.employee.first_name,
                                last_name: response.data.employee.last_name,
                            });

                            //close the modal
                            $('#applyModal').modal('hide');

                            Toastify({
                                text: response.message,
                                duration: 3000,
                                destination: "",
                                newWindow: true,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "right", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%);",
                                },
                                onClick: function() {} // Callback after click
                            }).showToast();
                        },
                        error: function(xhr, status, error) {
                            Toastify({
                                text: xhr.responseJSON?.error || 'An error occurred',
                                duration: 3000,
                                destination: "",
                                newWindow: true,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "right", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%);",
                                },
                                onClick: function() {} // Callback after click
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
