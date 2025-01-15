<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between align-items-center">
            <h5 class="ms-3 leave-roster-title">Leave Roster</h5>

        </div>

        <div class="table-wrapper mt-4">
            <table class="table table-striped table-hover table-responsive" id="leave-management-table" data-toggle="table"
                data-search="true" data-show-columns="true" data-sortable="true" data-pagination="true"
                data-show-export="true" data-show-pagination-switch="true"
                data-page-list="[10, 25, 50, 100, 500, 1000, 2000, 10000, all]" data-side-pagination="server"
                data-url="{{ url('/leave-roster-tabular/data') }}">
                <!-- Table Data -->
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Make the "Entitled Days" cell editable on click
                $(document).on('click', '.entitled-days-text', function() {
                    var $inputField = $(this).next('.entitled-days-input');
                    $inputField.show();
                    $inputField.focus();
                    $(this).hide(); // Hide the text when editing starts
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
                            formattedValue +=
                                `<li><span class="text-info">${startDate} - ${endDate}</span></li>`;
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
