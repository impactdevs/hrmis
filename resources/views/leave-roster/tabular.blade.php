<x-app-layout>
    <div class="mt-3">
        <div class="d-flex align-items-center mb-3">
            {{-- Add Leave Roster --}}
            <button class="btn btn-primary btn-sm mt-3 ms-1 font-weight-bold" id="addLeaveRoster" type="button"
                style="max-height: 40px; font-size: 12px">
                <i class="bi bi-plus-circle"></i> Add Leave Days</button>
        </div>
        <div class="table-wrapper">
            <table class="table table-striped table-hover table-responsive" id="leave-management-table"
                data-toggle="table" data-search="true" data-show-columns="true" data-sortable="true"
                data-pagination="true" data-show-export="true" data-show-pagination-switch="true"
                data-page-list="[10, 25, 50, 100, 500, 1000, 2000, 10000, all]" data-side-pagination="server"
                data-url="{{ url('/leave-roster-tabular/data') }}">
                <!-- Table Data -->
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">
                        <i class="bi bi-calendar-plus"></i> Add Leave Roster(<span id="display-entitled"></span> and
                        <span id="display-scheduled"></span>)
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

                        <div class="mb-3 col">
                            <label for="user_id"></label>
                            <select class="employees form-select" name="user_id" id="user_id"
                                data-placeholder="Choose the employee">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        data-entitled="{{ $user->employee->entitled_leave_days }}"
                                        data-scheduled="{{ $user->employee->overallRosterDays() }}">{{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hidden input for leave title -->
                        <input type="hidden" name="leave_title" value="New Leave">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyButton"
                        data-bs-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                //variables
                var totalLeaveDaysEntitled = 0;
                var totalLeaveDaysScheduled = 0;
                console.log('leave days scheduled:', totalLeaveDaysScheduled);
                var public_holidays = @json($public_holidays);



                $('.employees').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $("#applyModal"),
                    placeholder: $(this).data('placeholder')
                });

                // Handle leave roster deletion
                $(document).on('click', '.delete-roster', function() {
                    const rosterId = $(this).data('roster-id');
                    const $listItem = $(this).closest('li');

                    if (confirm('Are you sure you want to delete this leave roster?')) {
                        $.ajax({
                            url: `/leave-roster/${rosterId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                $listItem.remove();
                                $table.bootstrapTable('refresh');
                                Toastify({
                                    text: response.message,
                                    duration: 3000,
                                    style: {
                                        background: "green"
                                    }
                                }).showToast();
                            },
                            error: function(xhr) {
                                Toastify({
                                    text: xhr.responseJSON?.error || 'Delete failed',
                                    duration: 3000,
                                    style: {
                                        background: "red"
                                    }
                                }).showToast();
                            }
                        });
                    }
                });

                // 2) when modal is shown (fires each time you open it)
                $('#applyModal').on('shown.bs.modal', function() {
                    // find the currently selected option
                    const $sel = $('#user_id');
                    const $opt = $sel.find('option:selected');

                    // read the data-* attributes
                    totalLeaveDaysEntitled = parseInt($opt.data('entitled'), 10) || 0;
                    totalLeaveDaysScheduled = parseInt($opt.data('scheduled'), 10) || 0;

                    console.log('Selected user totals → Entitled:', totalLeaveDaysEntitled,
                        'Scheduled:', totalLeaveDaysScheduled);

                    // (optional) If you have badges/spans to show these:
                    $('#display-entitled').text("Entitled to " + totalLeaveDaysEntitled);
                    $('#display-scheduled').text("Have Scheduled " + totalLeaveDaysScheduled);

                });

                $('#user_id').on('change', function() {
                    const $opt = $(this).find('option:selected');
                    totalLeaveDaysEntitled = parseInt($opt.data('entitled'), 10) || 0;
                    totalLeaveDaysScheduled = parseInt($opt.data('scheduled'), 10) || 0;

                    console.log('Selected user totals → Entitled:', totalLeaveDaysEntitled,
                        'Scheduled:', totalLeaveDaysScheduled);

                    // (optional) If you have badges/spans to show these:
                    $('#display-entitled').text("Entitled to " + totalLeaveDaysEntitled);
                    $('#display-scheduled').text("Have Scheduled " + totalLeaveDaysScheduled);
                });



                //calculate scheduled Leave days before saving a schedule to make sure the user does not exceed the entitled leave days
                function calculateScheduledLeaveDays(start_date, end_date, publicHolidays = [],
                    totalLeaveDaysScheduled) {
                    const startDate = new Date(start_date);
                    const endDate = new Date(end_date);
                    console.log(startDate, endDate);
                    let numberOfDaysBeingAdded = 0;
                    console.log(totalLeaveDaysEntitled)

                    for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                        const dayOfWeek = d.getDay(); // 0 = Sunday, 6 = Saturday
                        const formattedDate = d.toISOString().split('T')[0]; // 'YYYY-MM-DD'

                        const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                        const isPublicHoliday = publicHolidays.includes(formattedDate);

                        if (!isWeekend && !isPublicHoliday) {
                            numberOfDaysBeingAdded++;
                        }
                    }

                    const willBeTotaldays = totalLeaveDaysScheduled + numberOfDaysBeingAdded;

                    console.log(`Days being added: ${numberOfDaysBeingAdded}`);
                    console.log(`Total leave days after scheduling: ${willBeTotaldays}`);

                    if (willBeTotaldays > totalLeaveDaysEntitled) {
                        return true;
                    }

                    return false;
                }

                $('#applyButton').click(function() {
                    //do date validation #
                    const startDate = new Date($('#start_date').val());
                    const endDate = new Date($('#end_date').val());
                    const formattedStartDate = moment(startDate).format('YYYY-MM-DD');
                    const formattedEndDate = moment(endDate).format('YYYY-MM-DD');

                    if (!startDate || !endDate) {
                        Toastify({
                            text: "Please fill in both the Start Date and End Date.",
                            duration: 3000,
                            destination: "",
                            newWindow: true,
                            close: true,
                            gravity: "top", // `top` or `bottom`
                            position: "right", // `left`, `center` or `right`
                            stopOnFocus: true, // Prevents dismissing of toast on hover
                            style: {
                                background: "red",
                            },
                            onClick: function() {} // Callback after click
                        }).showToast();
                        return; // Stop execution here
                    }

                    if (startDate >= endDate) {
                        Toastify({
                            text: "The Start Date must be earlier than the End Date.Please try again with valid date range",
                            duration: 3000,
                            destination: "",
                            newWindow: true,
                            close: true,
                            gravity: "top", // `top` or `bottom`
                            position: "right", // `left`, `center` or `right`
                            stopOnFocus: true, // Prevents dismissing of toast on hover
                            style: {
                                background: "red",
                            },
                            onClick: function() {} // Callback after click
                        }).showToast();
                        return; // Stop execution here
                    }

                    var exceededDays = calculateScheduledLeaveDays(formattedStartDate, formattedEndDate,
                        public_holidays, totalLeaveDaysScheduled);

                    if (exceededDays) {
                        Toastify({
                            text: "Entitled Leave Days exceeded!!",
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

                        return;
                    }

                    // Extract form values
                    var start_date = $('#start_date').val();
                    var end_date = $('#end_date').val();
                    var leave_title = $("input[name='leave_title']").val();
                    var user_id = Number(($("#user_id").val()));
                    // Send the data via AJAX POST request

                    $.ajax({
                        url: "{{ route('leave-roster.store') }}",
                        method: 'POST',
                        data: {
                            start_date: start_date,
                            end_date: end_date,
                            leave_title: leave_title,
                            user_id: user_id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            //close the modal
                            $('#applyModal').modal('hide');

                            //update Leave days entitled and what has been schedlued
                            totalLeaveDaysScheduled = response.data.scheduled_days

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

                // Make the "Entitled Days" cell editable on click
                $(document).on('click', '.entitled-days-text', function() {
                    var $inputField = $(this).next('.entitled-days-input');
                    $inputField.show();
                    $inputField.focus();
                    $(this).hide(); // Hide the text when editing starts
                });

                $(document).on('click', '.remove-roster', function() {
                    var leaveRosterId = $(this).data('roster-id');
                    var $entry = $(this).closest('.roster-entry');

                    $.ajax({
                        url: "{{ route('leave-roster.destroy', '') }}/" + leaveRosterId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log("Roster Deleted");
                            $entry.remove(); // remove the whole block
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting event:', error);
                        }
                    });
                });


                //addLeaveRoster
                $('#addLeaveRoster').click(function() {
                    // show applyModal
                    $('#applyModal').modal('show');

                });

                // Handle "Entitled Days" updates on blur
                $(document).on('blur', '.entitled-days-input', function() {
                    var $inputField = $(this);
                    var newValue = $inputField.val();
                    var $row = $inputField.closest('tr');
                    var employeeId = $row.data('employee-id');

                    // Perform validation
                    if (isNaN(newValue) || newValue < 0) {
                        alert('Invalid number of leave days');
                        return;
                    }

                    // AJAX request to update data
                    $.ajax({
                        url: '/update-entitled-leave-days/' + employeeId,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            entitled_leave_days: newValue
                        },
                        success: function(response) {
                            $row.find('.entitled-days-text').text(newValue).show();
                            $inputField.hide();
                        },
                        error: function() {
                            alert('Failed to update');
                        }
                    });
                });

                var $table = $('#leave-management-table');

                // Initialize the table with Bootstrap Table options
                function initTable() {
                    $table.bootstrapTable('destroy').bootstrapTable({
                        columns: [{
                            field: 'staff_id',
                            title: 'STAFF ID',
                            sortable: true,
                            formatter: function(value) {
                                return `<button class="btn btn-outline-primary btn-sm">${value}</button>`;
                            },
                            headerFormatter: function() {
                                return '<span class="text-uppercase font-weight-bold">Staff ID</span>';
                            }
                        }, {
                            field: 'first_name',
                            title: 'FIRST NAME',
                            sortable: true,
                            class: 'text-primary',
                            formatter: function(value) {
                                if (!value) return '<span class="text-muted">N/A</span>';
                                return value.toUpperCase(); // Make names uppercase
                            },
                            headerFormatter: function() {
                                return '<span class="text-uppercase font-weight-bold">First Name</span>';
                            }
                        }, {
                            field: 'last_name',
                            title: 'LAST NAME',
                            sortable: true,
                            class: 'text-primary',
                            formatter: function(value) {
                                if (!value) return '<span class="text-muted">N/A</span>';
                                return value.toUpperCase(); // Make names uppercase
                            },
                            headerFormatter: function() {
                                return '<span class="text-uppercase font-weight-bold">Last Name</span>';
                            }
                        }, {
                            field: 'total_leave_roster_days',
                            title: 'No. OF LEAVE DAYS',
                            sortable: true,
                            formatter: function(value) {
                                return `<span class="badge bg-success">${value}</span>`;
                            },
                            headerFormatter: function() {
                                return '<span class="text-uppercase font-weight-bold">Entitled Days</span>';
                            }
                        }, {
                            field: 'leave_roster',
                            title: 'LEAVE SCHEDULE',
                            sortable: true,
                            formatter: leaveRosterFormatter,
                            headerFormatter: function() {
                                return '<span class="text-uppercase font-weight-bold">Leave Schedule</span>';
                            }
                        }],
                        rowAttributes: function(row) {
                            return {
                                'data-employee-id': row.employee_id
                            };
                        },
                        pagination: true,
                        pageSize: 10,
                        search: true,
                        showPaginationSwitch: true,
                    });
                }

                function leaveRosterFormatter(value) {
                    if (value) {
                        var formattedValue = '<ul class="list-unstyled">';
                        value.forEach(function(item) {
                            var startDate = new Date(item.start_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            var endDate = new Date(item.end_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            var status = item.booking_approval_status;

                            formattedValue += `
                <div class="roster-entry mb-2 nowrap d-flex justify-content-between flex-1">
                    <span class="text-info">${startDate} - ${endDate}</span>
                    <span class="remove-roster" title="Remove Roster" data-roster-id="${item.leave_roster_id}"><i class="bi bi-trash"></i></span>
                </div>`;
                        });
                        formattedValue += '</ul>';
                        return formattedValue;
                    }
                    return '';
                }


                // Initialize table
                initTable();

                // Add row hover effect for better UI
                $table.on('mouseenter', 'tr', function() {
                    $(this).addClass('table-active');
                }).on('mouseleave', 'tr', function() {
                    $(this).removeClass('table-active');
                });
            });
        </script>
    @endpush

    <style>
        /* Styling for Leave Roster Title */
        .leave-roster-title {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 2.5rem;
            color: #fff;
            /* White text for contrast */
            background: linear-gradient(90deg, #4CAF50, #81C784);
            /* Green gradient */
            padding: 10px 20px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.2);
            /* Subtle shadow */
            margin-bottom: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            /* Soft shadow for depth */
            transition: all 0.3s ease;
            /* Smooth transition for hover effect */
        }

        /* Hover Effect */
        .leave-roster-title:hover {
            transform: scale(1.05);
            /* Slight zoom effect */
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.3);
            /* Enhanced shadow on hover */
            cursor: pointer;
        }

        /* Styling for table headers */
        th {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 1.1rem;
            text-transform: uppercase;
            color: #343a40;
            background-color: #f8f9fa;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
            padding: 10px;
        }

        /* Styling for the staff ID button */
        .btn-outline-primary {
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 10px;
        }

        /* Add some padding to table cells */
        td {
            padding: 8px;
        }
    </style>
</x-app-layout>
