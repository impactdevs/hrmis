@php $isEdit = isset($companyJob); @endphp

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ $isEdit ? 'Edit Job Posting' : 'New Job Posting' }}</h5>
    </div>
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST"
            action="{{ $isEdit ? route('hr.company-jobs.update', $companyJob->company_job_id) : route('hr.company-jobs.store') }}">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            {{-- Basic details --}}
            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Job Details</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Job Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('job_code') is-invalid @enderror"
                        name="job_code"
                        value="{{ old('job_code', $isEdit ? $companyJob->job_code : '') }}"
                        placeholder="e.g. UNCST/2025/01" required>
                    <div class="form-text">Used as the application reference number.</div>
                    @error('job_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-semibold">Job Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('job_title') is-invalid @enderror"
                        name="job_title"
                        value="{{ old('job_title', $isEdit ? $companyJob->job_title : '') }}" required>
                    @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Application Opens <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('will_become_active_at') is-invalid @enderror"
                        name="will_become_active_at"
                        value="{{ old('will_become_active_at', $isEdit ? $companyJob->will_become_active_at?->format('Y-m-d\TH:i') : '') }}" required>
                    @error('will_become_active_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Application Closes <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('will_become_inactive_at') is-invalid @enderror"
                        name="will_become_inactive_at"
                        value="{{ old('will_become_inactive_at', $isEdit ? $companyJob->will_become_inactive_at?->format('Y-m-d\TH:i') : '') }}" required>
                    @error('will_become_inactive_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Job Description</label>
                    <textarea class="form-control" name="job_description" rows="4"
                        placeholder="Duties, responsibilities, requirements...">{{ old('job_description', $isEdit ? $companyJob->job_description : '') }}</textarea>
                </div>
            </div>

            {{-- Screening criteria --}}
            <h6 class="fw-bold text-primary mb-1 border-bottom pb-2">Screening Criteria</h6>
            <p class="text-muted small mb-3">
                Applications that fail <strong>any</strong> of these hard filters are automatically rejected
                with a rejection email. Passing applications are scored 0–100 using the weights below.
            </p>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Minimum Qualification</label>
                    <select class="form-select @error('criteria_min_qualification') is-invalid @enderror"
                        name="criteria_min_qualification">
                        <option value="">No minimum</option>
                        @foreach ($qualificationLevels as $level)
                            <option value="{{ $level }}"
                                {{ old('criteria_min_qualification', $isEdit ? $companyJob->criteria_min_qualification : '') === $level ? 'selected' : '' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                    @error('criteria_min_qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Min. Years Experience</label>
                    <input type="number" class="form-control @error('criteria_min_experience_years') is-invalid @enderror"
                        name="criteria_min_experience_years" min="0" max="50"
                        value="{{ old('criteria_min_experience_years', $isEdit ? $companyJob->criteria_min_experience_years : '') }}"
                        placeholder="e.g. 3">
                    @error('criteria_min_experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Min. Age</label>
                    <input type="number" class="form-control @error('criteria_min_age') is-invalid @enderror"
                        name="criteria_min_age" min="18" max="100"
                        value="{{ old('criteria_min_age', $isEdit ? $companyJob->criteria_min_age : '') }}"
                        placeholder="e.g. 25">
                    @error('criteria_min_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Max. Age</label>
                    <input type="number" class="form-control @error('criteria_max_age') is-invalid @enderror"
                        name="criteria_max_age" min="18" max="100"
                        value="{{ old('criteria_max_age', $isEdit ? $companyJob->criteria_max_age : '') }}"
                        placeholder="e.g. 45">
                    @error('criteria_max_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Required Keywords</label>
                    <input type="text" class="form-control @error('criteria_required_keywords') is-invalid @enderror"
                        name="criteria_required_keywords"
                        value="{{ old('criteria_required_keywords', $isEdit ? implode(', ', $companyJob->criteria_required_keywords ?? []) : '') }}"
                        placeholder="e.g. Computer Science, Data Analysis, Python">
                    <div class="form-text">
                        Comma-separated. <strong>All</strong> keywords must appear somewhere in the
                        candidate's education or employment records. Missing any one = auto-rejected.
                    </div>
                    @error('criteria_required_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Scoring weights --}}
            <h6 class="fw-bold text-primary mb-1 border-bottom pb-2">Scoring Weights</h6>
            <p class="text-muted small mb-3">
                These weights determine how much each factor contributes to the final 0–100 score.
                They are normalised automatically, so they don't need to sum to 100.
            </p>
            <div class="row g-3 mb-4">
                @php
                    $weightFields = [
                        'weight_qualification'  => ['label' => 'Qualification Level', 'default' => 30],
                        'weight_experience'     => ['label' => 'Years of Experience',  'default' => 40],
                        'weight_keyword_match'  => ['label' => 'Keyword Match',         'default' => 20],
                        'weight_age_fit'        => ['label' => 'Age Fit',               'default' => 10],
                    ];
                @endphp
                @foreach ($weightFields as $field => ['label' => $label, 'default' => $default])
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ $label }}</label>
                        <div class="input-group">
                            <input type="number" class="form-control @error($field) is-invalid @enderror"
                                name="{{ $field }}" min="0" max="100"
                                value="{{ old($field, $isEdit ? $companyJob->$field : $default) }}"
                                required>
                            <span class="input-group-text">pts</span>
                        </div>
                        @error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Save Changes' : 'Create Job Posting' }}
                </button>
                <a href="{{ route('hr.company-jobs.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>