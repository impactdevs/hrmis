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

    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="next_of_kin" label="Next Of Kin" type="text" id="next_of_kin"
                placeholder="Employee Next Of Kin" value="{{ old('next_of_kin', $employee->next_of_kin ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.repeater name="qualifications_details" label="Qualifications" :values="$employee->qualifications_details ?? []" />
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
        });
    </script>
@endpush
