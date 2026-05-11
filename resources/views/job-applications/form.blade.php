{{--
    Shared form partial — used by create.blade.php and edit.blade.php

    Required variables passed via @include:
        $formAction  — route URL string
        $formMethod  — 'POST' or 'PUT'
        $job         — CompanyJob (create only, null on edit)
        $application — JobApplication (edit only, null on create)
        $encodedId   — string (edit only, null on create)
--}}
@php $isEdit = isset($application) && $application !== null; @endphp

<style>
    body { background: #f1f5f9; }
    .form-section {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .section-title {
        color: #1a56db;
        font-weight: 700;
        font-size: 1rem;
        border-bottom: 2px solid #1a56db;
        padding-bottom: 7px;
        margin-bottom: 22px;
    }
</style>

<div class="container py-5" style="max-width: 900px;">

    <div class="text-center mb-4">
        <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST" class="img-fluid mb-3" style="max-height: 80px;">
        <h2 class="fw-bold">UNCST Job Application</h2>
        @if ($isEdit)
            <p class="text-muted">Editing — Ref: <strong>{{ $application->reference_number }}</strong></p>
        @else
            <p class="text-muted">{{ $job->job_title }} &mdash; <strong>{{ $job->job_code }}</strong></p>
        @endif
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $cls)
        @if (session($key))
            <div class="alert alert-{{ $cls }} alert-dismissible fade show">
                {{ session($key) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" novalidate>
        @csrf
        @if ($isEdit) @method('PUT') @endif

        {{-- ── 1. Personal Details ──────────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">1. Post &amp; Personal Details</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Post Applied For</label>
                    <input type="text" class="form-control bg-light"
                        value="{{ $isEdit ? $application->post_applied : $job->job_title }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Reference Number</label>
                    <input type="text" class="form-control bg-light"
                        value="{{ $isEdit ? $application->reference_number : $job->job_code }}" readonly>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Full Name (Surname first in CAPITALS) <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('personal_details.full_name') is-invalid @enderror"
                        name="personal_details[full_name]"
                        value="{{ old('personal_details.full_name', $isEdit ? $application->full_name : '') }}" required>
                    @error('personal_details.full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date"
                        class="form-control @error('personal_details.date_of_birth') is-invalid @enderror"
                        name="personal_details[date_of_birth]"
                        value="{{ old('personal_details.date_of_birth', $isEdit ? $application->date_of_birth?->format('Y-m-d') : '') }}"
                        max="{{ now()->subYears(16)->format('Y-m-d') }}" required>
                    @error('personal_details.date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email"
                        class="form-control @error('personal_details.email') is-invalid @enderror"
                        name="personal_details[email]"
                        value="{{ old('personal_details.email', $isEdit ? $application->email : '') }}" required>
                    @error('personal_details.email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Telephone Number <span class="text-danger">*</span></label>
                    <input type="tel"
                        class="form-control @error('personal_details.telephone_number') is-invalid @enderror"
                        name="personal_details[telephone_number]"
                        value="{{ old('personal_details.telephone_number', $isEdit ? $application->telephone : '') }}"
                        placeholder="+256 700 000 000" required>
                    @error('personal_details.telephone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ── 2. Nationality & Residence ───────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">2. Nationality &amp; Residence</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nationality <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('nationality_and_residence.nationality') is-invalid @enderror"
                        name="nationality_and_residence[nationality]"
                        value="{{ old('nationality_and_residence.nationality', $isEdit ? $application->nationality : '') }}" required>
                    @error('nationality_and_residence.nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">NIN <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('nationality_and_residence.nin') is-invalid @enderror"
                        name="nationality_and_residence[nin]"
                        value="{{ old('nationality_and_residence.nin', $isEdit ? $application->nin : '') }}" required>
                    @error('nationality_and_residence.nin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Home District</label>
                    <input type="text" class="form-control" name="nationality_and_residence[home_district]"
                        value="{{ old('nationality_and_residence.home_district', $isEdit ? $application->home_district : '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sub-county</label>
                    <input type="text" class="form-control" name="nationality_and_residence[sub_county]"
                        value="{{ old('nationality_and_residence.sub_county', $isEdit ? $application->sub_county : '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Village</label>
                    <input type="text" class="form-control" name="nationality_and_residence[village]"
                        value="{{ old('nationality_and_residence.village', $isEdit ? $application->village : '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Residency in Uganda <span class="text-danger">*</span></label>
                    @php $residency = old('nationality_and_residence.residency_type', $isEdit ? $application->residency_type : ''); @endphp
                    <div class="d-flex gap-4 mt-1">
                        @foreach (['Temporary', 'Permanent'] as $type)
                            <div class="form-check">
                                <input class="form-check-input @error('nationality_and_residence.residency_type') is-invalid @enderror"
                                    type="radio" name="nationality_and_residence[residency_type]"
                                    id="res_{{ $type }}" value="{{ $type }}"
                                    {{ $residency === $type ? 'checked' : '' }}>
                                <label class="form-check-label" for="res_{{ $type }}">{{ $type }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('nationality_and_residence.residency_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ── 3. Work Background ────────────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">3. Work Background <span class="text-muted fw-normal small">(Current / Most Recent)</span></div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Ministry / Department / Employer</label>
                    <input type="text" class="form-control" name="work_background[present_department]"
                        value="{{ old('work_background.present_department', $isEdit ? $application->present_department : '') }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Present Post / Position</label>
                    <input type="text" class="form-control" name="work_background[present_post]"
                        value="{{ old('work_background.present_post', $isEdit ? $application->present_post : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Date of Appointment</label>
                    <input type="date" class="form-control" name="work_background[date_of_appointment_present_post]"
                        value="{{ old('work_background.date_of_appointment_present_post', $isEdit ? $application->date_of_appointment_present_post?->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Terms of Employment</label>
                    @php $terms = old('work_background.terms_of_employment', $isEdit ? $application->terms_of_employment : ''); @endphp
                    <select class="form-select" name="work_background[terms_of_employment]">
                        <option value="">— Select —</option>
                        @foreach (['Temporary', 'Contract', 'Probation', 'Permanent'] as $t)
                            <option value="{{ $t }}" {{ $terms === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ── 4. Family Background ──────────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">4. Family Background</div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Marital Status</label>
                    @php $marital = old('family_background.marital_status', $isEdit ? $application->marital_status : ''); @endphp
                    <div class="d-flex gap-4 flex-wrap mt-1">
                        @foreach (['Married', 'Single', 'Divorced', 'Widowed'] as $m)
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                    name="family_background[marital_status]"
                                    id="marital_{{ $m }}" value="{{ $m }}"
                                    {{ $marital === $m ? 'checked' : '' }}>
                                <label class="form-check-label" for="marital_{{ $m }}">{{ $m }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. Employment Record ──────────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">5. Employment Record <span class="text-muted fw-normal small">(most recent first)</span></div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Year / Period</th><th>Position Held</th><th>Employer Details</th><th style="width:50px"></th></tr>
                    </thead>
                    <tbody id="employment-tbody">
                        @php
                            $empRows = old('employment_record', $isEdit ? ($application->employment_record ?? []) : []);
                            if (empty($empRows)) $empRows = [[]];
                        @endphp
                        @foreach ($empRows as $i => $row)
                            <tr>
                                <td><input type="text" class="form-control form-control-sm"
                                    name="employment_record[{{ $i }}][period]"
                                    value="{{ $row['period'] ?? '' }}" placeholder="e.g. 2020–2023"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                    name="employment_record[{{ $i }}][position]"
                                    value="{{ $row['position'] ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                    name="employment_record[{{ $i }}][details]"
                                    value="{{ $row['details'] ?? '' }}"></td>
                                <td class="text-center">
                                    @if ($i > 0)
                                        <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-employment">+ Add Row</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── 6. Education & Training ───────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">6. Education &amp; Training <span class="text-muted fw-normal small">(most recent first)</span></div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Qualification / Award</th><th>Institution</th><th>Year</th><th style="width:50px"></th></tr>
                    </thead>
                    <tbody id="education-tbody">
                        @php
                            $eduRows = old('education_training', $isEdit ? ($application->education_training ?? []) : []);
                            if (empty($eduRows)) $eduRows = [[]];
                        @endphp
                        @foreach ($eduRows as $i => $row)
                            <tr>
                                <td><input type="text" class="form-control form-control-sm"
                                    name="education_training[{{ $i }}][qualification]"
                                    value="{{ $row['qualification'] ?? '' }}"
                                    placeholder="e.g. Bachelor of Science"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                    name="education_training[{{ $i }}][institution]"
                                    value="{{ $row['institution'] ?? '' }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                    name="education_training[{{ $i }}][year]"
                                    value="{{ $row['year'] ?? '' }}" placeholder="2020"></td>
                                <td class="text-center">
                                    @if ($i > 0)
                                        <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-education">+ Add Row</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── 7. Criminal History ───────────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">7. Criminal History</div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Have you ever been convicted on a criminal charge? <span class="text-danger">*</span></label>
                    @php $crime = old('criminalHistory', $isEdit ? ($application->criminal_convicted ? 'yes' : 'no') : ''); @endphp
                    <div class="d-flex gap-4 mt-1">
                        @foreach (['yes' => 'Yes', 'no' => 'No'] as $val => $label)
                            <div class="form-check">
                                <input class="form-check-input @error('criminalHistory') is-invalid @enderror"
                                    type="radio" name="criminalHistory"
                                    id="crime_{{ $val }}" value="{{ $val }}"
                                    {{ $crime === $val ? 'checked' : '' }}>
                                <label class="form-check-label" for="crime_{{ $val }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('criminalHistory')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                    <div id="criminalDetailsWrap" class="{{ $crime === 'yes' ? 'mt-3' : 'd-none mt-3' }}">
                        <label class="form-label fw-semibold">Provide details including sentence imposed:</label>
                        <textarea class="form-control @error('criminal_history_details') is-invalid @enderror"
                            name="criminal_history_details" rows="3">{{ old('criminal_history_details', $isEdit ? $application->criminal_details : '') }}</textarea>
                        @error('criminal_history_details')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 8. Availability & Salary ──────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">8. Availability &amp; Salary Expectations</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">How soon would you be available? <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('availability_if_appointed') is-invalid @enderror"
                        name="availability_if_appointed"
                        value="{{ old('availability_if_appointed', $isEdit ? $application->availability : '') }}"
                        placeholder="e.g. Immediately, 2 weeks notice" required>
                    @error('availability_if_appointed')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Minimum Salary Expectation (UGX) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">UGX</span>
                        <input type="number"
                            class="form-control @error('minimum_salary_expected') is-invalid @enderror"
                            name="minimum_salary_expected" min="0"
                            value="{{ old('minimum_salary_expected', $isEdit ? $application->salary_expectation : '') }}" required>
                    </div>
                    @error('minimum_salary_expected')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- ── 9. References ─────────────────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">9. References &amp; Recommendations</div>
            <div class="row g-3">
                <div class="col-12">
                    <p class="text-muted small mb-1">Provide names, telephone numbers and email addresses of three referees (not relatives).</p>
                </div>
                @php $refs = old('reference', $isEdit ? ($application->references ?? []) : []); @endphp
                @foreach ([0, 1, 2] as $i)
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Reference {{ $i + 1 }}</label>
                        <textarea class="form-control form-control-sm @error("reference.$i") is-invalid @enderror"
                            rows="2" name="reference[{{ $i }}]" placeholder="Name, Tel & Email">{{ $refs[$i] ?? '' }}</textarea>
                        @error("reference.$i")<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach

                <div class="col-12 mt-2">
                    <p class="fw-semibold mb-1 text-muted small">For applicants in Government Service — Recommending Officer</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name of Recommending Officer</label>
                    <input type="text" class="form-control" name="recommender_name"
                        value="{{ old('recommender_name', $isEdit ? $application->recommender_name : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Title / Designation</label>
                    <input type="text" class="form-control" name="recommender_title"
                        value="{{ old('recommender_title', $isEdit ? $application->recommender_title : '') }}">
                </div>
            </div>
        </div>

        {{-- ── 10. Documents ─────────────────────────────────────────── --}}
        <div class="form-section">
            <div class="section-title">10. Document Uploads</div>
            @if ($isEdit)
                <div class="alert alert-info py-2 small">Uploading new files will replace your existing ones.</div>
            @endif
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Academic Documents (PDF)</label>
                    @if ($isEdit && !empty($application->academic_documents))
                        @foreach ($application->academic_documents as $doc)
                            <a href="{{ asset('storage/'.$doc) }}" target="_blank" class="d-block small text-primary mb-1">📄 {{ basename($doc) }}</a>
                        @endforeach
                    @endif
                    <input type="file" class="form-control @error('academic_documents') is-invalid @enderror"
                        name="academic_documents[]" multiple accept="application/pdf">
                    <div class="form-text">PDF only. Max 2MB each.</div>
                    @error('academic_documents')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">CV / Resume {{ $isEdit ? '' : '*' }}</label>
                    @if ($isEdit && $application->cv)
                        <a href="{{ asset('storage/'.$application->cv) }}" target="_blank" class="d-block small text-primary mb-1">📄 {{ basename($application->cv) }}</a>
                    @endif
                    <input type="file" class="form-control @error('cv') is-invalid @enderror"
                        name="cv" accept="application/pdf">
                    <div class="form-text">PDF only. Max 2MB{{ $isEdit ? '. Leave blank to keep current.' : '.' }}</div>
                    @error('cv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Supporting Documents</label>
                    @if ($isEdit && !empty($application->other_documents))
                        @foreach ($application->other_documents as $doc)
                            <a href="{{ asset('storage/'.$doc) }}" target="_blank" class="d-block small text-primary mb-1">📄 {{ basename($doc) }}</a>
                        @endforeach
                    @endif
                    <input type="file" class="form-control @error('other_documents') is-invalid @enderror"
                        name="other_documents[]" multiple accept="application/pdf,image/*">
                    <div class="form-text">PDF or images. Max 2MB each.</div>
                    @error('other_documents')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="d-grid mb-5">
            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                {{ $isEdit ? '💾 Update Application' : '📨 Submit Application' }}
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Criminal history toggle
    document.querySelectorAll('input[name="criminalHistory"]').forEach(r =>
        r.addEventListener('change', function () {
            const wrap = document.getElementById('criminalDetailsWrap');
            wrap.classList.toggle('d-none', this.value !== 'yes');
        })
    );

    // Generic dynamic row helper
    function addTableRow(tbodyId, namePrefix, fieldDefs) {
        const tbody = document.getElementById(tbodyId);
        const idx   = tbody.querySelectorAll('tr').length;
        const cells = fieldDefs.map(({ name, placeholder }) =>
            `<td><input type="text" class="form-control form-control-sm"
                name="${namePrefix}[${idx}][${name}]"
                ${placeholder ? `placeholder="${placeholder}"` : ''}></td>`
        ).join('');
        tbody.insertAdjacentHTML('beforeend',
            `<tr>${cells}<td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
             </td></tr>`
        );
    }

    document.getElementById('add-employment').addEventListener('click', () =>
        addTableRow('employment-tbody', 'employment_record', [
            { name: 'period',   placeholder: 'e.g. 2020–2023' },
            { name: 'position', placeholder: '' },
            { name: 'details',  placeholder: '' },
        ])
    );

    document.getElementById('add-education').addEventListener('click', () =>
        addTableRow('education-tbody', 'education_training', [
            { name: 'qualification', placeholder: 'e.g. Bachelor of Science' },
            { name: 'institution',   placeholder: '' },
            { name: 'year',          placeholder: '2020' },
        ])
    );

    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove-row')) e.target.closest('tr').remove();
    });
</script>