<x-app-layout>

    <div id="calendar-container">
        <h1 class="text-center m-3 fs-1 text-primary font-weight-bold">
            {{ strtoupper(auth()->user()->isAdminOrSecretary() ? 'Leave Roster' : 'My Leave Roster') }}</h1>

        {{-- Filters --}}
        <div class="d-flex align-items-center mb-3 justify-between">
            <div class="d-flex">
                {{-- Approval Status Filter --}}
                <div class="form-check form-check-inline">
                    <input type="radio" class="btn-check" id="btn-check-4" checked autocomplete="off"
                        name="approval_status" value="all">
                    <label class="btn btn-outline-primary" for="btn-check-4">All</label>
                </div>

                <div class="form-check form-check-inline">
                    <input type="radio" class="btn-check" id="btn-check-5" autocomplete="off" name="approval_status"
                        value="Pending">
                    <label class="btn btn-outline-primary" for="btn-check-5">Pending</label>
                </div>

                <div class="form-check form-check-inline">
                    <input type="radio" class="btn-check" id="btn-check-6" autocomplete="off" name="approval_status"
                        value="Approved">
                    <label class="btn btn-outline-primary" for="btn-check-6">Approved</label>
                </div>

                <div class="form-check form-check-inline">
                    <input type="radio" class="btn-check" id="btn-check-7" autocomplete="off" name="approval_status"
                        value="Rejected">
                    <label class="btn btn-outline-primary" for="btn-check-7">Rejected</label>
                </div>
                @if (auth()->user()->isAdminOrSecretary())
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

                @endif
            </div>

            <div class="d-flex align-items-center mb-3">
                {{-- Add Leave Roster --}}
                <button class="btn btn-primary btn-sm mt-3 ms-1 font-weight-bold" id="addLeaveRoster" type="button"
                    style="max-height: 40px; font-size: 12px">
                    <i class="bi bi-plus-circle"></i> Add Leave Roster</button>
            </div>
            @if (auth()->user()->isAdminOrSecretary())
                <div class="d-flex align-items-center mb-3">
                    {{-- Tabular view --}}
                    <a class="btn btn-primary btn-sm mt-3 ms-1 font-weight-bold"
                        style="max-height: 40px; font-size: 12px" href="{{ route('leave-roster-tabular.index') }}">
                        <i class="bi bi-eye"></i>Tabular View</a>
                </div>
            @endif

            {{-- leave days entitlement info --}}
            <div class="d-flex flex-column mb-4 p-3 rounded-3 shadow-sm"
                style="background-color: #f4f7fc; border: 1px solid #e0e4e8;">
                <div class="d-flex flex-column">
                    <!-- Total Leave Days Entitled -->
                    <p class="text-primary fw-bold fs-5 mb-2" id="totalLeaveDaysEntitled">Total Leave Days Entitled:
                        <span class="text-dark" style="font-weight: 400;">
                            {{ auth()->user()->employee->leave_days_entitled ?? 0 }}
                        </span>
                    </p>

                    <!-- Total Leave Days Scheduled -->
                    <p class="text-secondary fw-bold fs-5 mb-2" id="totalLeaveDaysScheduled">Total Leave Days Scheduled:
                        <span class="text-dark" style="font-weight: 400;">
                            {{ auth()->user()->employee->overallRosterDays() ?? 0 }}
                        </span>
                    </p>

                    <!-- Balance to Schedule -->
                    <p class="fw-bold fs-5 mb-2" id="balanceToSchedule">Balance to Schedule:
                        <span class="balance-text text-dark" style="font-weight: 400;">
                            {{ auth()->user()->employee->leave_days_entitled - auth()->user()->employee->overallRosterDays() ?? 0 }}
                        </span>
                    </p>
                </div>
            </div>



        </div>


        {{-- Calendar --}}
        <div id="calendar"></div>
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

                <!-- Approval Status -->
                <div class="d-flex justify-content-between mb-3">
                    <strong class="text-secondary">Approval Status:</strong>
                    <span id="eventApprovalStatusText" class="badge text-bg-primary">Pending</span>
                </div>

                <!-- Rejection Reason Section -->
                <div id="rejectionReasonSection" class="mt-3" style="display: none;">
                    <strong class="text-danger">Rejection Reason:</strong>
                </div>
            </div>

            <!-- Action Buttons (Admin) -->
            @can('approve leave roster')
                <div class="border-top pt-3 mt-4">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-success" id="approveRoster"
                            title="Approve the roster">
                            <i class="bi bi-check-circle"></i> Approve Roster
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="rejectRoster"
                            title="Reject the roster">
                            <i class="bi bi-x-circle"></i> Reject Roster
                        </button>
                    </div>

                    <!-- Rejection Reason Input (Initially Hidden) -->
                    <div id="rejectionReasonSectionInput" class="mt-3" style="display: none;">
                        <label for="rejectionReason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Enter rejection reason"></textarea>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-danger btn-sm" id="submitRejection"
                                title="Submit rejection reason">
                                Submit Rejection
                            </button>
                        </div>
                    </div>
                </div>
            @endcan

            <!-- Hint Section -->
            <div class="text-muted mt-5 border-top pt-3">
                <p class="mb-1">To apply for leave, click the <i class="bi bi-pencil"></i> icon on the top right
                    corner of this card.</p>
                <p class="mb-0"><strong>Note:</strong> The option to apply for leave only appears if your roster has
                    been approved.</p>
            </div>
        </div>
    </div>




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




    @push('scripts')
        <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css' rel='stylesheet' />
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.15/index.global.min.js"></script>
        <script type="module">
            $(document).ready(function() {
                var employeeId = @json(auth()->user()->employee->employee_id);
                Echo.private(`roster.${employeeId}`)
                    .listen('RosterUpdate', (e) => {
                        var totalLeaveDaysEntitled = e.total_leave_days_entitled;
                        var totalLeaveDaysScheduled = e.total_leave_days_scheduled;
                        var balanceToSchedule = Number(totalLeaveDaysEntitled) - Number(totalLeaveDaysScheduled);

                        // Update the values in the HTML
                        $('#totalLeaveDaysEntitled').text('Total Leave Days Entitled: ' + totalLeaveDaysEntitled);
                        $('#totalLeaveDaysScheduled').text('Total Leave Days Scheduled: ' +
                            totalLeaveDaysScheduled);
                        $('#balanceToSchedule').text('Balance to Schedule: ' + balanceToSchedule);
                    });

                var calendarEl = $('#calendar');
                var currentEvent = null;
                var listTitle = @json(auth()->user()->isAdminOrSecretary() ? 'Leave Roster' : 'My Leaves');
                var calendar = new FullCalendar.Calendar(calendarEl[0], {
                    initialView: 'multiMonthYear',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay, listYear'
                    },
                    height: 'auto',
                    contentHeight: 'auto',
                    buttonText: {
                        multiMonthYear: 'All Year',
                        dayGridMonth: 'Month',
                        timeGridWeek: 'Week',
                        timeGridDay: 'Day',
                        today: 'Today',
                        listYear: listTitle
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
                            url: '{{ route('leave-roster.calendarData') }}',
                            type: 'GET',
                            data: {
                                approval_status: approvalStatus, // Pass the selected approval status filter
                                department: department // Pass the selected department filter
                            },
                            success: function(response) {
                                var events = response.data.map(function(event) {

                                    //event
                                    return {
                                        id: event.leave_roster_id,
                                        title: event.title,
                                        start: event.start,
                                        staff_id: event.staff_id,
                                        first_name: event.first_name,
                                        last_name: event.last_name,
                                        isApproved: event.isApproved,
                                        end: event.end,
                                        color: 'blue',
                                        fullDay: true

                                    };
                                });


                                successCallback(events);
                            },
                            error: function(xhr, status, error) {
                                console.error('There was an error fetching events:', error);
                                failureCallback(error);
                            }
                        });
                    },
                    selectable: true,
                    select: function(info) {
                        const startDate = info.start;
                        const endDate = info.end;
                        const leaveTitle = 'New Leave';

                        const formattedStartDate = moment(startDate).format('YYYY-MM-DD');
                        const formattedEndDate = moment(endDate).format('YYYY-MM-DD');

                        $.ajax({
                            url: "{{ route('leave-roster.store') }}",
                            method: 'POST',
                            data: {
                                start_date: formattedStartDate,
                                end_date: formattedEndDate,
                                leave_title: leaveTitle
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                calendar.addEvent({
                                    title: 'New Leave',
                                    start: info.start,
                                    end: info.end,
                                    backgroundColor: 'yellow',
                                    borderColor: 'orange',
                                    textColor: 'black',
                                    id: response.data.leave_roster_id,
                                    first_name: response.data.employee.first_name,
                                    last_name: response.data.employee.last_name,
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
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

                        // Check if the event is approved, and show/hide the "Apply Leave" button accordingly
                        if (currentEvent.extendedProps.isApproved === true) {
                            $('#applyLeave').show(); // Show the apply leave button if approved
                        } else {
                            $('#applyLeave').hide(); // Hide the apply leave button if not approved
                        }

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


                // Handle the Reject button
                $('#rejectRoster').click(function() {
                    // Show rejection reason textarea
                    $('#rejectionReasonSection').show();
                    //show input for rejection reason
                    $('#rejectionReasonSectionInput').show();
                });

                //approve roster
                $('#approveRoster').click(function() {
                    //if rejection reason is open, remove it
                    $('#rejectionReasonSection').hide();
                    $.ajax({
                        url: "{{ route('leave-roster.update', '') }}/" + currentEvent.id,
                        method: 'PUT',
                        data: {
                            booking_approval_status: 'Approved'
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(
                                'eventOffCanvas'));
                            offcanvas.hide();
                            console.log('Event approved successfully');
                            calendar.refetchEvents();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error approving event:', error);
                        }
                    });
                });

                // Handle the Submit Rejection button
                $('#submitRejection').click(function() {
                    var rejectionReason = $('#rejectionReason').val();
                    if (rejectionReason.trim()) {
                        $.ajax({
                            url: "{{ route('leave-roster.update', '') }}/" + currentEvent.id,
                            method: 'PUT',
                            data: {
                                booking_approval_status: 'Rejected',
                                rejection_reason: rejectionReason
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Close the offcanvas and reset the rejection section
                                var offcanvas = bootstrap.Offcanvas.getInstance(document
                                    .getElementById('eventOffCanvas'));
                                offcanvas.hide();
                                $('#rejectionReasonSection').hide();
                                $('#rejectionReason').val('');
                                calendar.refetchEvents();
                            },
                            error: function(xhr, status, error) {
                                console.error('Error rejecting event:', error);
                            }
                        });
                    } else {
                        alert("Please enter a rejection reason.");
                    }
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
