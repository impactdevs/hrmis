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
                            formatter: function(value) {
                                return `<span class="entitled-days-text">${value ?? 0}</span>
                            <input type="number" class="entitled-days-input form-control form-control-sm" style="display: none;" value="${value}">`;
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
                                'data-employee-id': row.employee_id // Attach employee_id to each row
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
