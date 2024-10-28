<!-- Personal Information Group -->
<fieldset class="border p-2 mb-4">
    <legend class="w-auto">Personal Information</legend>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="first_name" label="First Name" type="text" id="first_name"
                placeholder="Enter Employee First Name" value="{{ old('first_name', $employee->first_name ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="last_name" label="Last Name" type="text" id="last_name"
                placeholder="Enter Employee Last Name" value="{{ old('last_name', $employee->last_name ?? '') }}" />
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="title" label="Title" type="text" id="title"
                placeholder="Enter Employee Title" value="{{ old('title', $employee->title ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="staff_id" label="Staff ID" type="text" id="staff_id"
                placeholder="Enter Employee Staff ID" value="{{ old('staff_id', $employee->staff_id ?? '') }}" />
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
            <x-forms.input name="date_of_entry" label="Date of Entry" type="date" id="date_of_entry" placeholder="Date of Entry"
                value="{{ old('date_of_entry', isset($employee) && $employee->date_of_entry ? $employee->date_of_entry->toDateString() : '') }}" />
        </div>

        <div class="col-md-6">
            <x-forms.input name="contract_expiry_date" label="Contract Expiry Date" type="date" placeholder="Contact Expiry"
                id="contract_expiry_date"
                value="{{ old('contract_expiry_date', isset($employee) && $employee->contract_expiry_date ? $employee->contract_expiry_date->toDateString() : '') }}" />
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
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="home_district" label="Home District" type="text" id="home_district"
                placeholder="Employee Home District"
                value="{{ old('home_district', $employee->home_district ?? '') }}" />
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
            <x-forms.input name="tin_no" label="TIN Number" type="text" id="tin_no"
                placeholder="Employee TIN Number" value="{{ old('tin_no', $employee->tin_no ?? '') }}" />
        </div>
    </div>
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

<!-- Emergency Contact Group -->
<fieldset class="border p-2 mb-4">
    <legend class="w-auto">Emergency Contact</legend>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="next_of_kin" label="Next Of Kin" type="text" id="next_of_kin"
                placeholder="Employee Next Of Kin" value="{{ old('next_of_kin', $employee->next_of_kin ?? '') }}" />
        </div>
        <div class="col-md-6">
            <x-forms.upload name="passport_photo" label="Passport Photo" id="passport_photo" form_text_id=""
                value="{{ old('passport_photo', $employee->passport_photo ?? '') }}" />
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <x-forms.input name="date_of_birth" label="Date of Birth" type="date" id="date_of_birth" placeholder="Date of birth"
                value="{{ old('date_of_birth', isset($employee) && $employee->date_of_birth ? $employee->date_of_birth->toDateString() : '') }}" />
        </div>
    </div>

</fieldset>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
