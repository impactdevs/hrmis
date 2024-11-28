<x-app-layout>

    <div id="calendar-container">
        {{-- Filters --}}
        <div class="d-flex align-items-center mb-3">
            {{-- Approval Status Filter --}}
            <div class="form-check form-check-inline">
                <input type="radio" class="btn-check" id="btn-check-4" checked autocomplete="off" name="approval_status"
                    value="all">
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

            {{-- Department Filter --}}
            <div class="ms-3">
                <select class="form-select form-select-sm rounded" id="departmentSelect" style="max-width: 180px;"
                    name="department">
                    <option value="all">All Departments</option>
                    @foreach ($departments as $department_id => $department)
                        <option value="{{ $department_id }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Calendar --}}
        <div id="calendar"></div>
    </div>



    <!-- Off-Canvas for Event Options -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="eventOffCanvas" aria-labelledby="eventOffCanvasLabel">
        <div class="offcanvas-header">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <h5 id="eventOffCanvasLabel">Roster Options</h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <button type="button" class="btn" id="applyLeave">
                <i class="bi bi-pencil"></i>
            </button>


            <button type="button" class="btn" id="deleteEvent">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            {{-- <form id="eventForm">
                <div class="form-group m-2 border p-3 rounded">
                    <label for="eventTitle">Event Title</label>
                    <input type="text" class="form-control title-primary font-weight-bold mb-3" id="eventTitle"
                        placeholder="Enter new title">

                    <button type="button" class="btn-outline btn-outline-success btn-sm ms-3" id="renameEvent">
                        <i class="bi bi-pencil"></i> Rename Event
                    </button>
                </div>
            </form> --}}

            <div class="border border-primary m-2 rounded">
                <div class="fs-6 text-center">
                    ROSTER
                </div>

                <div class="d-flex flex-row justify-content-between p-1 m-3">
                    <button type="button" class="btn btn-sm btn-outline btn-outline-success" id="approveRoster">
                        Approve Roster
                    </button>

                    <button type="button" class="btn btn-sm btn-outline btn-outline-danger" id="rejectRoster">
                        Reject Roster
                    </button>
                </div>

                <!-- Rejection Reason (Initially Hidden) -->
                <div id="rejectionReasonSection" class="mt-3" style="display: none;">
                    <label for="rejectionReason">Rejection Reason</label>
                    <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Enter rejection reason"></textarea>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-danger btn-sm" id="submitRejection">
                            Submit Rejection
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>

    {{-- apply modal --}}
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">Apply for Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('leaves.store') }}" accept-charset="UTF-8"
                        class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        @include ('leaves.form', ['formMode' => 'create'])
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css' rel='stylesheet' />
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.15/index.global.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#tokenfield').tokenfield({
                    autocomplete: {
                        source: ['red', 'blue', 'green', 'yellow', 'violet', 'brown', 'purple', 'black',
                            'white'
                        ],
                        delay: 100
                    },
                    showAutocompleteOnFocus: true
                })
                var calendarEl = $('#calendar');
                var currentEvent = null;

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
                        listYear: 'List'
                    },
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
                            statusColor = 'yellow'; // You can use a color name or hex code
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

                        // Return the title with the status icon and color
                        return {
                            html: `<div class="fc-event-title">${title}</div>
               <div class="fc-event-status" style="color: ${statusColor};">
                   ${statusIcon} ${approvalStatusText}
               </div>`
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
                                    return {
                                        id: event.leave_roster_id,
                                        title: event.title,
                                        start: event.start,
                                        staff_id: event.staff_id,
                                        first_name: event.first_name,
                                        last_name: event.last_name,
                                        isApproved: event.isApproved,
                                        end: event.end,
                                        color: 'blue'
                                    };
                                });

                                console.log(events);

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
                        currentEvent = info.event;
                        $('#eventTitle').val(currentEvent.title);

                        // Check if the event is approved, and show/hide the "Apply Leave" button accordingly
                        if (currentEvent.extendedProps.isApproved === true) {
                            $('#applyLeave').show(); // Show button if approved
                        } else {
                            $('#applyLeave').hide(); // Hide button if not approved
                        }

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

                // Handle the Rename button
                $('#renameEvent').click(function() {
                    var newTitle = $('#eventTitle').val();
                    if (newTitle) {
                        currentEvent.setProp('title', newTitle);
                        $.ajax({
                            url: "{{ route('leave-roster.update', '') }}/" + currentEvent.id,
                            method: 'PUT',
                            data: {
                                leave_title: newTitle
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                var offcanvas = bootstrap.Offcanvas.getInstance(document
                                    .getElementById('eventOffCanvas'));
                                offcanvas.hide();
                                console.log('Event updated successfully');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error updating event:', error);
                            }
                        });
                    } else {
                        alert("Please enter a title.");
                    }
                });

                // Handle the Reject button
                $('#rejectRoster').click(function() {
                    // Show rejection reason textarea
                    $('#rejectionReasonSection').show();
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
                                console.log('Event rejected successfully');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error rejecting event:', error);
                            }
                        });
                    } else {
                        alert("Please enter a rejection reason.");
                    }
                });

                // apply leave
                $('#applyLeave').click(function() {
                    //navigate to apply for leave with leave roster id current event.id
                    window.location.href = "/apply-for-leave/" + currentEvent.id;
                });
            });
        </script>
    @endpush



</x-app-layout>
