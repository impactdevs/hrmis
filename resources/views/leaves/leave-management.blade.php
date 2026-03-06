<x-app-layout>
    <div class="mt-3">
        <div class="table-wrapper">
            <table class="table table-striped" id="leave-management-table" data-toggle="table" data-search="true"
                data-show-columns="true" data-sortable="true" data-pagination="true" data-show-export="true"
                data-show-pagination-switch="true" data-page-list="[10, 25, 50, 100, 500, 1000, 2000, 10000, all]"
                data-side-pagination="server" data-url="{{ url('/leave-management/data') }}"> <!-- New AJAX route -->
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var $table = $('#leave-management-table');

                // Initialize the table with Bootstrap Table options
                function initTable() {
                    $table.bootstrapTable('destroy').bootstrapTable({
                        columns: [{
                            field: 'numeric_id',
                            title: 'ID',
                            formatter: function(value) {
                                return `<button class="btn btn-outline-primary btn-sm">${value}</button>`;
                            }
                        }, {
                            field: 'first_name',
                            title: 'FIRST NAME',
                            sortable: true,
                            class: 'text-primary',
                            formatter: function(value) {
                                return value ? value.toUpperCase() : "None";
                            }

                        }, {
                            field: 'last_name',
                            title: 'LAST NAME',
                            sortable: true,
                            class: 'text-primary',
                            formatter: function(value) {
                                return value ? value.toUpperCase() : "None";
                            }

                        }, {
                            field: 'entitled_leave_days',
                            title: 'ENTITLED LEAVE DAYS',
                            sortable: true,
                            formatter: function(value, row) {
                                return `
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="entitled-days-text fw-bold">${value ?? 0}</span>
                                        <button class="btn btn-outline-secondary btn-sm edit-entitled-btn py-0 px-1"
                                            data-employee-id="${row.employee_id}"
                                            data-current="${value ?? 0}"
                                            title="Edit leave days">
                                            <i class="bi bi-pencil" style="font-size:11px;"></i>
                                        </button>
                                    </div>`;
                            }
                        }, {
                            field: 'total_leave_days',
                            title: 'USED LEAVE DAYS',
                            sortable: true
                        }, {
                            field: 'leave_balance',
                            title: 'BALANCE DAYS',
                            sortable: true
                        }],
                        rowAttributes: function(row, index) {
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

                // Initialize the table
                initTable();

                // ── Inline edit handler ─────────────────────────────────────────
                $table.on('click', '.edit-entitled-btn', function() {
                    var $btn = $(this);
                    var employeeId = $btn.data('employee-id');
                    var currentVal = $btn.data('current');
                    var $cell = $btn.closest('td');

                    // Replace cell content with an input group
                    $cell.html(`
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" class="form-control form-control-sm entitled-days-edit-input"
                                style="width:90px;" value="${currentVal}" min="0" max="365"
                                data-employee-id="${employeeId}" autofocus>
                            <button class="btn btn-sm btn-success save-entitled-btn py-0 px-1" title="Save">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary cancel-entitled-btn py-0 px-1" title="Cancel">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    `);
                    $cell.find('.entitled-days-edit-input').focus().select();
                });

                // Save on Enter key inside the input
                $table.on('keydown', '.entitled-days-edit-input', function(e) {
                    if (e.key === 'Enter') {
                        $(this).closest('td').find('.save-entitled-btn').click();
                    }
                    if (e.key === 'Escape') {
                        $(this).closest('td').find('.cancel-entitled-btn').click();
                    }
                });

                // Save button click
                $table.on('click', '.save-entitled-btn', function() {
                    var $cell = $(this).closest('td');
                    var $input = $cell.find('.entitled-days-edit-input');
                    var employeeId = $input.data('employee-id');
                    var newValue = $input.val();

                    if (newValue === '' || newValue < 0) {
                        alert('Please enter a valid number of leave days.');
                        return;
                    }

                    $.ajax({
                        url: `/update-entitled-leave-days/${employeeId}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            entitled_leave_days: newValue,
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update the cell back to display mode with new value
                                $cell.html(`
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="entitled-days-text fw-bold">${newValue}</span>
                                        <button class="btn btn-outline-secondary btn-sm edit-entitled-btn py-0 px-1"
                                            data-employee-id="${employeeId}"
                                            data-current="${newValue}"
                                            title="Edit leave days">
                                            <i class="bi bi-pencil" style="font-size:11px;"></i>
                                        </button>
                                    </div>
                                `);

                                Toastify({
                                    text: `Leave days updated to ${newValue}`,
                                    duration: 3000,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                                }).showToast();
                            }
                        },
                        error: function(xhr) {
                            Toastify({
                                text: xhr.responseJSON?.error || 'Failed to update leave days.',
                                duration: 3000,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                            }).showToast();
                        }
                    });
                });

                // Cancel puts the original value back
                $table.on('click', '.cancel-entitled-btn', function() {
                    $table.bootstrapTable('refresh');
                });

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
        /* Styling for Leave Management Title */
        .leave-management-title {
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
        .leave-management-title:hover {
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

        /* Styling for table row hover effect */
        tr.table-active {
            background-color: #f1f1f1;
        }
    </style>
</x-app-layout>
