<div class="row mb-3">
    <div class="col-md-12">
        <x-forms.input name="job_code" label="Job Code" type="text" id="job_code" placeholder="Enter Job Code"
            value="{{ old('job_code', $companyJob->job_code ?? '') }}" />
    </div>

    <div class="col-md-12">
        <x-forms.input name="job_title" label="Role" type="text" id="job_title" placeholder="Enter the Job Title"
            value="{{ old('job_title', $companyJob->job_title ?? '') }}" />
    </div>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
