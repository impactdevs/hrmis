<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="leave_type_id">Leave Type *</label>

            <select name="leave_type_id" id="leave_type_id" required
                class="form-control @error('leave_type_id') is-invalid @enderror">
                <option value="">-- Select Leave Type --</option>
                @php
                    $annualLeaveType = \App\Models\LeaveType::where('leave_type_name', 'Annual')->first();
                @endphp
                @if (isset($leaveRoster) && $annualLeaveType)
                    <option value="{{ $annualLeaveType->leave_type_id }}" selected>Annual Leave</option>
                @else
                    @foreach ($leaveTypes as $value => $text)
                        <option value="{{ $value }}" {{ (old('leave_type_id') ?? ($leave->leave_type_id ?? '')) == $value ? 'selected' : '' }}>
                            {{ $text }}
                        </option>
                    @endforeach
                @endif
            </select>

            @error('leave_type_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <x-forms.text-area name="handover_note" label="Hand Over Note *" id="handover_note" required :value="old('handover_note', $leave->handover_note ?? '')" />

        <div style="margin-bottom: 1rem;">
            <label for="handover_note_file" style="font-weight: bold; display: block; margin-bottom: 0.5rem;">Upload
                Handover Notes</label>
            <input type="file" name="handover_note_file" id="handover_note_file" accept="application/pdf"
                style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 5px; display: block; width: 100%; max-width: 400px; font-size: 0.9rem;">
            <span style="font-size: 0.85rem; color: #555; margin-top: 0.5rem; display: inline-block;">
                If your handover notes are lengthy, please upload a PDF file.
            </span>
        </div>

    </div>
    <div class="col-md-6">
        @if (!isset($leaveRoster))
            <x-forms.input name="start_date" label="Leave Start Date *" type="date" id="start_date" required
                value="{{ old('start_date', isset($leave) && $leave->start_date ? $leave->start_date->toDateString() : '') }}" />
        @else
            <x-forms.input name="start_date" label="Leave Start Date *" type="date" id="start_date" required
                value="{{ old('start_date', $leaveRoster->start_date ? $leaveRoster->start_date->toDateString() : '') }}" />
        @endif
    </div>
    <div class="col-md-6">
        @if (!isset($leaveRoster))
            <x-forms.input name="end_date" label="Leave End Date *" type="date" id="end_date" required
                value="{{ old('end_date', isset($leave) && $leave->end_date ? $leave->end_date->toDateString() : '') }}" />
        @else
            <x-forms.input name="end_date" label="Leave End Date *" type="date" id="end_date" required
                value="{{ old('end_date', $leaveRoster->end_date ? $leaveRoster->end_date->toDateString() : '') }}" />
        @endif
    </div>
    <div class="col-md-12 mt-3">
        <p><strong>Difference in Days(Excluding Weekends and Holidays):</strong> <span id="days-difference">0</span></p>
    </div>
</div>

<x-forms.hidden name="user_id" id="user_id" value="{{ $user_id }}" />


<div class="mb-3 col">
    <label for="usertokenfield" class="form-label">The following do my work *</label>
    <input type="text" class="form-control" id="usertokenfield" required />
    <input type="hidden" name="my_work_will_be_done_by[users]" id="user_ids" />
</div>

{{-- leave adress --}}
<div class="row mb-3">
    <div class="col-md-6">
        <x-forms.input name="leave_address" label="Leave Address *" type="text" id="leave_address" required
            value="{{ old('leave_address', $leave->leave_address ?? '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="phone_number" label="Contact Number *" type="text" id="phone_number" required
            value="{{ old('phone_number', $leave->phone_number ?? '') }}" />
    </div>
</div>

{{-- textarea for other contact details --}}
<div class="row mb-3">
    <div class="col-md-12">
        <x-forms.text-area name="other_contact_details" label="Other Contact Details" id="other_contact_details"
            :value="old('other_contact_details', $leave->other_contact_details ?? '')" />
    </div>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Submit' }}">
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            const users = @json($users);
            const holidays = @json($holidays);
            console.log(holidays);
            const userSource = Object.entries(users).map(([id, name]) => ({
                id,
                name
            }));
            // Users Tokenfield
            $('#usertokenfield').tokenfield({
                autocomplete: {
                    source: userSource.map(user => user.name),
                    delay: 100
                },
                showAutocompleteOnFocus: true
            }).on('tokenfield:createtoken', function(event) {
                const token = event.attrs;
                const userId = userSource.find(user => user.name === token.value)?.id;
                if (userId) {
                    const currentIds = $('#user_ids').val().split(',').filter(Boolean);
                    currentIds.push(userId);
                    $('#user_ids').val(currentIds.join(','));
                }
            });

            function calculateWeekdayDifference() {
                const startDate = new Date($('#start_date').val());
                const endDate = new Date($('#end_date').val());

                if (!isNaN(startDate.getTime()) && !isNaN(endDate.getTime())) {
                    let totalDays = 0;

                    // Convert holiday strings to Date objects
                    const holidayDates = holidays.map(holiday => new Date(holiday).toISOString().split('T')[0]);

                    for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
                        const day = date.getDay(); // 0 = Sunday, 6 = Saturday
                        const dateString = date.toISOString().split('T')[0]; // Get date as YYYY-MM-DD string

                        // Exclude weekends and holidays
                        if (day !== 0 && day !== 6 && !holidayDates.includes(dateString)) {
                            totalDays++;
                        }
                    }

                    $('#days-difference').text(totalDays >= 0 ? totalDays : 0);
                } else {
                    $('#days-difference').text(0);
                }
            }

            // Bind event listeners to the date inputs
            $('#start_date, #end_date').on('change', calculateWeekdayDifference);
        });

        document.querySelector("form").addEventListener("submit", function(e) {
            // Comprehensive form validation
            let isValid = true;
            let errorMessage = "";

            // Check Leave Type
            const leaveType = document.getElementById("leave_type_id").value;
            if (!leaveType) {
                isValid = false;
                errorMessage += "- Leave Type is required\n";
            }

            // Check Start Date
            const startDate = document.getElementById("start_date").value;
            if (!startDate) {
                isValid = false;
                errorMessage += "- Leave Start Date is required\n";
            }

            // Check End Date
            const endDate = document.getElementById("end_date").value;
            if (!endDate) {
                isValid = false;
                errorMessage += "- Leave End Date is required\n";
            }

            // Check if end date is after start date
            if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                isValid = false;
                errorMessage += "- End Date must be after Start Date\n";
            }

            // Check Handover Note
            const note = document.getElementById("handover_note").value.trim();
            if (note.length === 0) {
                isValid = false;
                errorMessage += "- Handover Note is required\n";
            }

            // Check if handover note is too long without file
            const fileInput = document.getElementById("handover_note_file");
            if (note.length > 1000 && fileInput.files.length === 0) {
                isValid = false;
                errorMessage += "- Since your handover note is long, please upload a PDF file\n";
            }

            // Check Work Coverage (tokenfield)
            const userIds = document.getElementById("user_ids").value;
            if (!userIds || userIds.trim() === "") {
                isValid = false;
                errorMessage += "- Please select at least one person to cover your work\n";
            }

            // Check Leave Address
            const leaveAddress = document.getElementById("leave_address").value.trim();
            if (!leaveAddress) {
                isValid = false;
                errorMessage += "- Leave Address is required\n";
            }

            // Check Phone Number
            const phoneNumber = document.getElementById("phone_number").value.trim();
            if (!phoneNumber) {
                isValid = false;
                errorMessage += "- Contact Number is required\n";
            }

            // Validate phone number format (basic validation)
            if (phoneNumber && !/^[\d\s\+\-\(\)]+$/.test(phoneNumber)) {
                isValid = false;
                errorMessage += "- Contact Number must contain only digits, spaces, +, -, (, )\n";
            }

            // If validation fails, show error message and prevent submission
            if (!isValid) {
                alert("Please correct the following errors:\n\n" + errorMessage);
                e.preventDefault();
                return false;
            }

            return true;
        });
    </script>
@endpush
