<div class="container">
    <!-- Position and Department Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <x-forms.input name="position" label="Position To Be Filled" type="text" id="position"
                value="{{ old('position', isset($staffRecruitment) ? $staffRecruitment->position : '') }}"
                class="form-control" />
        </div>
        <div class="col-md-6">
            <x-forms.dropdown name="department_id" label="Department/Unit" id="department_id" :options="$departments"
                :selected="isset($staffRecruitment) ? $staffRecruitment->department_id : ''" class="form-control" />
        </div>
    </div>

    <!-- Number of Staff and Date of Recruitment Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <x-forms.input name="number_of_staff" label="Total Number of Staff Required" type="number"
                id="number_of_staff"
                value="{{ old('number_of_staff', isset($staffRecruitment) ? $staffRecruitment->number_of_staff : '') }}"
                class="form-control" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="date_of_recruitment" label="Date When Staff are Required" type="date"
                id="date_of_recruitment"
                value="{{ old('date_of_recruitment', isset($staffRecruitment) && $staffRecruitment->date_of_recruitment ? $staffRecruitment->date_of_recruitment->toDateString() : '') }}"
                class="form-control" />
        </div>
    </div>

    <!-- Sourcing Method Row -->
    <div class="row mb-4">
        <div class="col-md-12">
            <p class="h5 font-weight-bold">Sourcing Method</p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="sourcing_method" id="internal" value="internal"
                    {{ old('sourcing_method', isset($staffRecruitment) ? $staffRecruitment->sourcing_method : '') == 'internal' ? 'checked' : '' }} />
                <label class="form-check-label" for="internal">Internal</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="sourcing_method" id="external" value="external"
                    {{ old('sourcing_method', isset($staffRecruitment) ? $staffRecruitment->sourcing_method : '') == 'external' ? 'checked' : '' }} />
                <label class="form-check-label" for="external">External</label>
            </div>
            @error('sourcing_method')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <p class="h5 font-weight-bold">Funding Budget</p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="funding_budget" id="government"
                    value="governement of uganda"
                    {{ old('funding_budget', isset($staffRecruitment) ? $staffRecruitment->funding_budget : '') == 'governement of uganda' ? 'checked' : '' }} />
                <label class="form-check-label" for="government">Government of Uganda</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="funding_budget" id="project" value="project"
                    {{ old('funding_budget', isset($staffRecruitment) ? $staffRecruitment->funding_budget : '') == 'project' ? 'checked' : '' }} />
                <label class="form-check-label" for="project">Project</label>
            </div>
            @error('funding_budget')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Employment Basis Row -->
    <div class="row mb-4">
        <div class="col-md-12">
            <p class="h5 font-weight-bold">Employment Basis</p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="employment_basis" id="Contract" value="Contract"
                    {{ old('employment_basis', isset($staffRecruitment) ? $staffRecruitment->employment_basis : '') == 'Contract' ? 'checked' : '' }} />
                <label class="form-check-label" for="Contract">Contract</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="employment_basis" id="Part-time" value="Part-time"
                    {{ old('employment_basis', isset($staffRecruitment) ? $staffRecruitment->employment_basis : '') == 'Part-time' ? 'checked' : '' }} />
                <label class="form-check-label" for="Part-time">Part-time</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="employment_basis" id="fulltime" value="Fulltime"
                    {{ old('employment_basis', isset($staffRecruitment) ? $staffRecruitment->employment_basis : '') == 'Fulltime' ? 'checked' : '' }} />
                <label class="form-check-label" for="fulltime">Fulltime</label>
            </div>
            @error('employment_basis')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Justification Row -->
    <div class="row mb-4">
        <div class="col-md-12">
            <x-forms.text-area name="justification" label="Justification" id="justification" :value="old('justification', isset($staffRecruitment) ? $staffRecruitment->justification : '')"
                class="form-control" />

        </div>
    </div>

    <!-- Submit Button -->
    <div class="form-group">
        <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Submit' }}">
    </div>
</div>

<!-- Custom CSS for minor styling tweaks -->
<style>
    .form-check-label {
        font-size: 1.1rem;
    }

    .h5 {
        color: #333;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
</style>
