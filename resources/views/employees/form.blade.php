<!-- Personal Information Group -->
<fieldset class="border p-2 mb-4">
    <legend class="w-auto">Personal Information</legend>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="first_name" label="First Name" type="text" id="first_name"
                placeholder="Enter Employee First Name" value="{{ old('first_name', $employee->first_name ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="middle_name" label="Middle Name" type="text" id="middle_name"
                placeholder="Enter Employee Middle Name"
                value="{{ old('middle_name', $employee->middle_name ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="last_name" label="Last Name" type="text" id="last_name"
                placeholder="Enter Employee Last Name" value="{{ old('last_name', $employee->last_name ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.radio name="gender" label="Gender" id="gender" :options="['M' => 'Male', 'F' => 'Female']" :selected="$employee->gender ?? ''" />
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="title" label="Title" type="text" id="title"
                placeholder="Enter Employee Title eg. MR., DR., Prof."
                value="{{ old('title', $employee->title ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="staff_id" label="Staff ID" type="text" id="staff_id"
                placeholder="Enter Employee Staff ID" value="{{ old('staff_id', $employee->staff_id ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.upload name="passport_photo" label="Employee Passport Photo" id="passport_photo"
                value="{{ old('passport_photo', $employee->passport_photo ?? '') }}" />
            <div id="passport-photo-preview" class="mt-3"></div> <!-- Preview container -->
        </div>
        <div class="col-md-6">
            <x-forms.upload name="national_id_photo" label="National ID Photo" id="national_id_photo"
                value="{{ old('national_id_photo', $employee->national_id_photo ?? '') }}" />
            <div id="national-id-photo-preview" class="mt-3"></div> <!-- Preview container -->
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <x-forms.input name="date_of_birth" label="Date of Birth" type="date" id="date_of_birth"
                    value="{{ old('date_of_birth', isset($employee) && $employee->date_of_birth ? $employee->date_of_birth->toDateString() : '') }}" />
            </div>
        </div>
    </div>
</fieldset>

<!-- Employment Details Group -->
<fieldset class="border p-2 mb-4">
    <legend class="w-auto">Employment Details</legend>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.dropdown name="position_id" label="Position" id="position_id" :options="$positions"
                :selected="$employee->position_id ?? ''" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="nin" label="NIN" type="text" id="nin" placeholder="Enter Employee NIN"
                value="{{ old('nin', $employee->nin ?? '') }}" />
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="date_of_entry" label="Date of Entry" type="date" id="date_of_entry"
                value="{{ old('date_of_entry', isset($employee) && $employee->date_of_entry ? $employee->date_of_entry->toDateString() : '') }}" />
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.text-area name="job_description" label="Job Description" id="job_description" :value="old('job_description', $employee->job_description ?? '')" />
        </div>

        <div class="col-md-6">
            <x-forms.dropdown name="department_id" label="Department" id="department_id" :options="$departments"
                :selected="$employee->department_id ?? ''" />
        </div>
    </div>
</fieldset>


<!-- Qualification Details Group -->
<fieldset class="border p-2 mb-4">
    <legend class="w-auto">Professional Qualifications</legend>

    <div id="qualifications-container">
        @php
            // Determine which qualifications to show
            $qualifications = old('qualifications_details');
            if (!$qualifications && isset($employee) && $employee->qualifications_details) {
                $qualifications = $employee->qualifications_details;
            }
            if (!$qualifications) {
                $qualifications = [['qualification' => '', 'institution' => '', 'year_obtained' => '', 'proof' => '']];
            }
        @endphp

        @foreach($qualifications as $index => $qual)
            <div class="row mb-3 qualification-row" data-index="{{ $index }}">
                <div class="col-md-3">
                    <x-forms.input
                        name="qualifications_details[{{ $index }}][qualification]"
                        label="Qualification"
                        type="text"
                        value="{{ $qual['qualification'] ?? $qual['title'] ?? '' }}"
                        placeholder="e.g., Bachelor's Degree, CPA, etc." />
                </div>
                <div class="col-md-3">
                    <x-forms.input
                        name="qualifications_details[{{ $index }}][institution]"
                        label="Institution"
                        type="text"
                        value="{{ $qual['institution'] ?? '' }}"
                        placeholder="e.g., Makerere University" />
                </div>
                <div class="col-md-2">
                    <x-forms.input
                        name="qualifications_details[{{ $index }}][year_obtained]"
                        label="Year Obtained"
                        type="number"
                        value="{{ $qual['year_obtained'] ?? '' }}"
                        placeholder="YYYY"
                        min="1950"
                        max="{{ date('Y') }}" />
                </div>
                <div class="col-md-3">
                    <x-forms.upload
                        name="qualifications_details[{{ $index }}][proof]"
                        label="Proof Document"
                        id="qualifications_details_{{ $index }}_proof"
                        accept=".pdf,.jpg,.jpeg,.png"
                        value="{{ $qual['proof'] ?? '' }}"
                        description="Upload PDF, JPG, JPEG, or PNG files"
                        filetype="document" />
                    @if(isset($qual['proof']) && $qual['proof'])
                        <small class="text-muted">
                            Current: <a href="{{ asset('storage/' . $qual['proof']) }}" target="_blank">View Document</a>
                        </small>
                    @endif
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-qualification" {{ $index === 0 && count($qualifications) === 1 ? 'disabled' : '' }}>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-end mt-3">
        <button type="button" class="btn btn-primary" id="add-qualification">
            <i class="fas fa-plus"></i> Add Another Qualification
        </button>
    </div>
</fieldset>


<!-- Additional Information Group -->
<fieldset class="border p-2 mb-4">
    <legend class="w-auto">Additional Information</legend>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="nssf_no" label="NSSF Number" type="text" id="nssf_no"
                placeholder="Employee NSSF Number" value="{{ old('nssf_no', $employee->nssf_no ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="tin_no" label="TIN Number" type="text" id="tin_no"
                placeholder="Employee TIN Number" value="{{ old('tin_no', $employee->tin_no ?? '') }}" />
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="home_district" label="Home District" type="text" id="home_district"
                placeholder="Employee Home District"
                value="{{ old('home_district', $employee->home_district ?? '') }}" />
        </div>

        <div class="col-md-6">
            <x-forms.input name="next_of_kin" label="Next Of Kin" type="text" id="next_of_kin"
                placeholder="Employee Next Of Kin" value="{{ old('next_of_kin', $employee->next_of_kin ?? '') }}" />
        </div>
    </div>
</fieldset>

<!-- Contact Information Group -->
<fieldset class="border p-2 mb-4">
    <legend class="w-auto">Contact Information</legend>

    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="email" label="Email" type="email" id="email"
                placeholder="Enter your email" value="{{ old('email', $employee->email ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="phone_number" label="Phone Number" type="tel" id="phone_number"
                placeholder="Enter your phone number eg 0786065399"
                value="{{ old('phone_number', $employee->phone_number ?? '') }}" />
        </div>
    </div>
</fieldset>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle Passport Photo file selection and preview
            $('#passport_photo').on('change', function(event) {
                var input = $(this)[0];
                var file = input.files[0];
                var previewContainer = $('#passport-photo-preview');
                previewContainer.empty(); // Clear previous preview

                if (file) {
                    var reader = new FileReader();
                    var fileName = file.name;

                    // If the file is an image, show the image preview
                    if (file.type.startsWith('image/')) {
                        reader.onload = function(e) {
                            var img = $('<img>', {
                                src: e.target.result,
                                alt: fileName,
                                class: 'img-fluid'
                            });
                            previewContainer.append(img);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // For other file types, show a default placeholder
                        var img = $('<img>', {
                            src: "{{ asset('assets/img/upload.png') }}",
                            alt: "upload placeholder",
                            height: 70,
                            width: 70,
                            class: 'img-fluid upload-icon'
                        });
                        previewContainer.append(img);
                    }
                }
            });

            // Handle National ID Photo file selection and preview
            $('#national_id_photo').on('change', function(event) {
                var input = $(this)[0];
                var file = input.files[0];
                var previewContainer = $('#national-id-photo-preview');
                previewContainer.empty(); // Clear previous preview

                if (file) {
                    var reader = new FileReader();
                    var fileName = file.name;

                    // If the file is an image, show the image preview
                    if (file.type.startsWith('image/')) {
                        reader.onload = function(e) {
                            var img = $('<img>', {
                                src: e.target.result,
                                alt: fileName,
                                class: 'img-fluid'
                            });
                            previewContainer.append(img);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        // For other file types, show a default placeholder
                        var img = $('<img>', {
                            src: "{{ asset('assets/img/upload.png') }}",
                            alt: "upload placeholder",
                            height: 70,
                            width: 70,
                            class: 'img-fluid upload-icon'
                        });
                        previewContainer.append(img);
                    }
                }
            });

            // Qualification management
            let qualificationIndex = {{ count($qualifications ?? []) }};

            // Add new qualification row
            $('#add-qualification').on('click', function() {
                const newRow = `
                    <div class="row mb-3 qualification-row" data-index="${qualificationIndex}">
                        <div class="col-md-3">
                            <label for="qualifications_details_${qualificationIndex}_qualification" class="form-label">Qualification</label>
                            <input type="text" 
                                   name="qualifications_details[${qualificationIndex}][qualification]" 
                                   id="qualifications_details_${qualificationIndex}_qualification"
                                   class="form-control" 
                                   placeholder="e.g., Bachelor's Degree, CPA, etc.">
                        </div>
                        <div class="col-md-3">
                            <label for="qualifications_details_${qualificationIndex}_institution" class="form-label">Institution</label>
                            <input type="text" 
                                   name="qualifications_details[${qualificationIndex}][institution]" 
                                   id="qualifications_details_${qualificationIndex}_institution"
                                   class="form-control" 
                                   placeholder="e.g., Makerere University">
                        </div>
                        <div class="col-md-2">
                            <label for="qualifications_details_${qualificationIndex}_year_obtained" class="form-label">Year Obtained</label>
                            <input type="number" 
                                   name="qualifications_details[${qualificationIndex}][year_obtained]" 
                                   id="qualifications_details_${qualificationIndex}_year_obtained"
                                   class="form-control" 
                                   placeholder="YYYY" 
                                   min="1950" 
                                   max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="qualifications_details_${qualificationIndex}_proof" class="form-label">Proof Document</label>
                            <input type="file" 
                                   name="qualifications_details[${qualificationIndex}][proof]" 
                                   id="qualifications_details_${qualificationIndex}_proof"
                                   class="form-control" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-qualification">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                $('#qualifications-container').append(newRow);
                qualificationIndex++;
                updateRemoveButtons();
            });

            // Remove qualification row
            $(document).on('click', '.remove-qualification', function() {
                if (!$(this).is(':disabled')) {
                    $(this).closest('.qualification-row').remove();
                    updateRemoveButtons();
                }
            });

            // Update remove button states
            function updateRemoveButtons() {
                const rows = $('.qualification-row');
                if (rows.length === 1) {
                    rows.find('.remove-qualification').prop('disabled', true);
                } else {
                    rows.find('.remove-qualification').prop('disabled', false);
                }
            }

            // Initialize remove button states
            updateRemoveButtons();
        });
    </script>
@endpush
