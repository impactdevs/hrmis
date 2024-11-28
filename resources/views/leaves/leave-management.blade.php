<x-app-layout>
    <div class="mt-3">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3">Leave Management</h5>
        </div>
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
                // Make the "Entitled Days" cell editable on click
                //click event for .entitled-days-text
                $(document).on('click', '.entitled-days-text', function() {
                    var $inputField = $(this).next('.entitled-days-input');
                    $inputField.show();
                    $inputField.focus();

                    //hide the text element
                    $(this).hide();
                });
                // Update the "Entitled Days" value automatically when the input loses focus
                $(document).on('blur', '.entitled-days-input', function() {
                    var $inputField = $(this);
                    var newValue = $inputField.val();
                    var $row = $inputField.closest('tr');
                    var employeeId = $row.data('employee-id'); // Get employee ID from data attribute

                    // Send the new value to the server via AJAX when input loses focus
                    $.ajax({
                        url: '/update-entitled-leave-days/' +
                            employeeId, // Update the URL to match your employee update route
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            entitled_leave_days: newValue
                        },
                        success: function(response) {
                            // On success, update the span with the new value and hide the input
                            $row.find('.entitled-days-text').text(newValue).show();
                            $inputField.hide();
                        },
                        error: function() {
                            // Handle any error if necessary (e.g., show a message)
                            alert('Failed to update');
                        }
                    });
                });


                var $table = $('#leave-management-table');
                var $remove = $('#remove');


                //initialize the table
                function initTable() {
                    $table.bootstrapTable('destroy').bootstrapTable({
                        columns: [{
                                field: 'numeric_id',
                                title: 'ID'
                            },
                            {
                                field: 'first_name',
                                title: 'First Name',
                                sortable: true
                            },
                            {
                                field: 'last_name',
                                title: 'Last Name',
                                sortable: true
                            },
                            {
                                field: 'total_leave_roster_days',
                                title: 'Entitled Days',
                                sortable: true

                            },
                            {
                                field: 'total_leave_days',
                                title: 'Used',
                                sortable: true
                            },
                            {
                                field: 'leave_balance',
                                title: 'Balance',
                                sortable: true
                            }
                        ],
                        rowAttributes: function(row, index) {
                            // Attach employee_id as a data attribute for each row
                            return {
                                'data-employee-id': row.employee_id // This attaches employee_id to each row
                            };
                        },
                    });
                }

                //initialize the table
                initTable();
            });
        </script>
    @endpush
</x-app-layout>
