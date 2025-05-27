<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNCST JOB APPLICATION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .section-title {
            color: #0d6efd;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
        }

        table.table-bordered>thead>tr>th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <img src="{{ asset('assets/img/logo.png') }}" alt="company logo"
            class="d-block mx-auto object-fit-contain border rounded img-fluid" style="max-width: 100%; height: auto;">

        <h2 class="text-center mb-4">UNCST JOB APPLICATION</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data">
            @csrf
            <!-- Section 1: Post & Personal Details -->
            <div class="form-section">
                <h4 class="section-title">1. Post & Personal Details</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Post applied for</label>
                        <select class="form-select @error('personal_details.post') is-invalid @enderror"
                            name="personal_details[post]" id="postSelect">
                            <option value="">-- Select Role --</option>
                            @php
                                $oldPost = old('personal_details.post');
                            @endphp
                            @foreach ($companyJobs as $role)
                                <option value="{{ $role->company_job_id }}" data-job-code="{{ $role->job_code }}"
                                    {{ $oldPost == $role->company_job_id ? 'selected' : '' }}>
                                    {{ $role->job_code . '-' . $role->job_title }}
                                </option>
                            @endforeach
                        </select>
                        @error('personal_details.post')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Reference Number</label>
                        <input type="text"
                            class="form-control @error('personal_details.reference_number') is-invalid @enderror"
                            name="personal_details[reference_number]" id="referenceNumber"
                            value="{{ old('personal_details.reference_number') }}" readonly>
                        @error('personal_details.reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const postSelect = document.getElementById('postSelect');
                            const referenceNumberInput = document.getElementById('referenceNumber');

                            function updateReferenceNumber() {
                                const selectedOption = postSelect.options[postSelect.selectedIndex];
                                const jobCode = selectedOption.getAttribute('data-job-code') || '';
                                referenceNumberInput.value = jobCode;
                            }

                            // Update on page load in case old input exists
                            updateReferenceNumber();

                            // Update on change
                            postSelect.addEventListener('change', updateReferenceNumber);
                        });
                    </script>


                    <div class="col-md-8">
                        <label class="form-label">Full name (Surname first in CAPITALS)</label>
                        <input type="text"
                            class="form-control @error('personal_details.full_name') is-invalid @enderror"
                            name="personal_details[full_name]" value="{{ old('personal_details.full_name') }}">
                        @error('personal_details.full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date"
                            class="form-control @error('personal_details.date_of_birth') is-invalid @enderror"
                            name="personal_details[date_of_birth]" value="{{ old('personal_details.date_of_birth') }}">
                        @error('personal_details.date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('personal_details.email') is-invalid @enderror"
                            name="personal_details[email]" value="{{ old('personal_details.email') }}">
                        @error('personal_details.email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Telephone Number</label>
                        <input type="tel"
                            class="form-control @error('personal_details.telephone_number') is-invalid @enderror"
                            name="personal_details[telephone_number]"
                            value="{{ old('personal_details.telephone_number') }}">
                        @error('personal_details.telephone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Nationality & Residence -->
            <div class="form-section">
                <h4 class="section-title">2. Nationality & Residence</h4>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nationality</label>
                        <input type="text"
                            class="form-control @error('nationality_and_residence.nationality') is-invalid @enderror"
                            name="nationality_and_residence[nationality]"
                            value="{{ old('nationality_and_residence.nationality') }}">
                        @error('nationality_and_residence.nationality')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Home District</label>
                        <input type="text"
                            class="form-control @error('nationality_and_residence.home_district') is-invalid @enderror"
                            name="nationality_and_residence[home_district]"
                            value="{{ old('nationality_and_residence.home_district') }}">
                        @error('nationality_and_residence.home_district')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sub-county</label>
                        <input type="text"
                            class="form-control @error('nationality_and_residence.sub_county') is-invalid @enderror"
                            name="nationality_and_residence[sub_county]"
                            value="{{ old('nationality_and_residence.sub_county') }}">
                        @error('nationality_and_residence.sub_county')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Village</label>
                        <input type="text"
                            class="form-control @error('nationality_and_residence.village') is-invalid @enderror"
                            name="nationality_and_residence[village]"
                            value="{{ old('nationality_and_residence.village') }}">
                        @error('nationality_and_residence.village')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">NIN</label>
                        <input type="text"
                            class="form-control @error('nationality_and_residence.nin') is-invalid @enderror"
                            name="nationality_and_residence[nin]" value="{{ old('nationality_and_residence.nin') }}">
                        @error('nationality_and_residence.nin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mt-3">
                        <label class="form-label">Are you a temporary or permanent resident in Uganda?</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input
                                    class="form-check-input @error('nationality_and_residence.residency_type') is-invalid @enderror"
                                    type="radio" name="nationality_and_residence[residency_type]" id="temporary"
                                    value="Temporary"
                                    {{ old('nationality_and_residence.residency_type') == 'Temporary' ? 'checked' : '' }}>
                                <label class="form-check-label" for="temporary">
                                    Temporary
                                </label>
                            </div>

                            <div class="form-check">
                                <input
                                    class="form-check-input @error('nationality_and_residence.residency_type') is-invalid @enderror"
                                    type="radio" name="nationality_and_residence[residency_type]" id="permanent"
                                    value="Permanent"
                                    {{ old('nationality_and_residence.residency_type') == 'Permanent' ? 'checked' : '' }}>
                                <label class="form-check-label" for="permanent">
                                    Permanent
                                </label>
                            </div>
                        </div>
                        @error('nationality_and_residence.residency_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Work Background -->
            <div class="form-section">
                <h4 class="section-title">3. Work Background</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Present Ministry/Local Government/Department/Any other
                            Employer</label>
                        <input type="text"
                            class="form-control @error('work_background.present_department') is-invalid @enderror"
                            name="work_background[present_department]"
                            value="{{ old('work_background.present_department') }}">
                        @error('work_background.present_department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Present post</label>
                        <input type="text"
                            class="form-control @error('work_background.present_post') is-invalid @enderror"
                            name="work_background[present_post]" value="{{ old('work_background.present_post') }}">
                        @error('work_background.present_post')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date of appointment to current post</label>
                        <input type="date"
                            class="form-control @error('work_background.date_of_appointment_to_present_post') is-invalid @enderror"
                            name="work_background[date_of_appointment_to_present_post]"
                            value="{{ old('work_background.date_of_appointment_to_present_post') }}">
                        @error('work_background.date_of_appointment_to_present_post')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Present Salary and Scale (if applicable)</label>
                        <input type="text"
                            class="form-control @error('work_background.present_salary') is-invalid @enderror"
                            placeholder="UGX" name="work_background[present_salary]"
                            value="{{ old('work_background.present_salary') }}">
                        @error('work_background.present_salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Terms of Employment</label>
                        <div class="d-flex gap-4 flex-wrap">
                            @php $employment = old('work_background.terms_of_employment'); @endphp

                            <div class="form-check">
                                <input
                                    class="form-check-input @error('work_background.terms_of_employment') is-invalid @enderror"
                                    type="radio" name="work_background[terms_of_employment]" id="temp"
                                    value="Temporary" {{ $employment == 'Temporary' ? 'checked' : '' }}>
                                <label class="form-check-label" for="temp">Temporary</label>
                            </div>

                            <div class="form-check">
                                <input
                                    class="form-check-input @error('work_background.terms_of_employment') is-invalid @enderror"
                                    type="radio" name="work_background[terms_of_employment]" id="contract"
                                    value="Contract" {{ $employment == 'Contract' ? 'checked' : '' }}>
                                <label class="form-check-label" for="contract">Contract</label>
                            </div>

                            <div class="form-check">
                                <input
                                    class="form-check-input @error('work_background.terms_of_employment') is-invalid @enderror"
                                    type="radio" name="work_background[terms_of_employment]" id="probation"
                                    value="Probation" {{ $employment == 'Probation' ? 'checked' : '' }}>
                                <label class="form-check-label" for="probation">Probation</label>
                            </div>

                            <div class="form-check">
                                <input
                                    class="form-check-input @error('work_background.terms_of_employment') is-invalid @enderror"
                                    type="radio" name="work_background[terms_of_employment]" id="perm"
                                    value="Permanent" {{ $employment == 'Permanent' ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm">Permanent</label>
                            </div>
                        </div>
                        @error('work_background.terms_of_employment')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 4: Family Background -->
            <div class="form-section">
                <h4 class="section-title">4. Family Background</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Marital Status</label>
                        <div class="d-flex gap-4 flex-wrap">
                            @php $maritalStatus = old('family_background.marital_status'); @endphp

                            <div class="form-check">
                                <input
                                    class="form-check-input @error('family_background.marital_status') is-invalid @enderror"
                                    type="radio" name="family_background[marital_status]" id="married"
                                    value="Married" {{ $maritalStatus == 'Married' ? 'checked' : '' }}>
                                <label class="form-check-label" for="married">Married</label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input @error('family_background.marital_status') is-invalid @enderror"
                                    type="radio" name="family_background[marital_status]" id="single"
                                    value="Single" {{ $maritalStatus == 'Single' ? 'checked' : '' }}>
                                <label class="form-check-label" for="single">Single</label>
                            </div>
                        </div>
                        @error('family_background.marital_status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Section 3: Education History -->
            <div class="form-section">
                <h4 class="section-title">5. Education History</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Years/Period</th>
                            <th>School/Institution</th>
                            <th>Award/Qualifications</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                <td>
                                    <input type="text"
                                        class="form-control @error("education_history.$i.period") is-invalid @enderror"
                                        name="education_history[{{ $i }}][period]"
                                        value="{{ old("education_history.$i.period") }}">
                                    @error("education_history.$i.period")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control @error("education_history.$i.institution") is-invalid @enderror"
                                        name="education_history[{{ $i }}][institution]"
                                        value="{{ old("education_history.$i.institution") }}">
                                    @error("education_history.$i.institution")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control @error("education_history.$i.award") is-invalid @enderror"
                                        name="education_history[{{ $i }}][award]"
                                        value="{{ old("education_history.$i.award") }}">
                                    @error("education_history.$i.award")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- University Education Section -->
            <div class="form-section">
                <h4 class="section-title">6. University Education Details</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">University Name</label>
                        <input type="text" name="university[name]"
                            class="form-control @error('university.name') is-invalid @enderror"
                            value="{{ old('university.name') }}" placeholder="Enter university name">
                        @error('university.name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Course</label>
                        <input type="text" name="university[course]"
                            class="form-control @error('university.course') is-invalid @enderror"
                            value="{{ old('university.course') }}" placeholder="Enter course of study">
                        @error('university.course')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="university[start_date]"
                            class="form-control @error('university.start_date') is-invalid @enderror"
                            value="{{ old('university.start_date') }}">
                        @error('university.start_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="university[end_date]"
                            class="form-control @error('university.end_date') is-invalid @enderror"
                            value="{{ old('university.end_date') }}">
                        @error('university.end_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">CGPA</label>
                        <input type="text" name="university[cgpa]"
                            class="form-control @error('university.cgpa') is-invalid @enderror"
                            value="{{ old('university.cgpa') }}" placeholder="e.g., 3.75">
                        @error('university.cgpa')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>


            <!-- New UACE Section -->
            <div class="form-section">
                <h4 class="section-title">7. Uganda Advanced Certificate of Education (UACE) Details</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Have you passed Uganda Advanced Certificate of Education Exams
                            [UACE]?</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="uace[passed]" id="uaceYes"
                                    value="yes" {{ old('uace.passed') == 'yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="uaceYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="uace[passed]" id="uaceNo"
                                    value="no" {{ old('uace.passed') == 'no' ? 'checked' : '' }}>
                                <label class="form-check-label" for="uaceNo">No</label>
                            </div>
                        </div>
                        @error('uace.passed')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Year of UACE Examination</label>
                        <input type="text" id="uaceYear"
                            class="form-control @error('uace.year') is-invalid @enderror" name="uace[year]"
                            value="{{ old('uace.year') }}" placeholder="Enter year (e.g., 2015)">
                        @error('uace.year')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 5; $i++)
                                    <tr>
                                        <td>
                                            <input type="text"
                                                class="form-control uace-score-subject @error("uace.scores.$i.subject") is-invalid @enderror"
                                                placeholder="Subject"
                                                name="uace[scores][{{ $i }}][subject]"
                                                value="{{ old("uace.scores.$i.subject") }}">
                                            @error("uace.scores.$i.subject")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control uace-score-grade @error("uace.scores.$i.grade") is-invalid @enderror"
                                                placeholder="Grade" name="uace[scores][{{ $i }}][grade]"
                                                value="{{ old("uace.scores.$i.grade") }}">
                                            @error("uace.scores.$i.grade")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- JavaScript -->
            <script>
                function toggleUaceFields() {
                    const passed = document.querySelector('input[name="uace[passed]"]:checked')?.value;
                    const shouldDisable = passed === 'no';

                    document.getElementById('uaceYear').disabled = shouldDisable;

                    document.querySelectorAll('.uace-score-subject, .uace-score-grade').forEach(input => {
                        input.disabled = shouldDisable;
                    });
                }

                document.addEventListener('DOMContentLoaded', function() {
                    toggleUaceFields(); // Set initial state

                    document.querySelectorAll('input[name="uace[passed]"]').forEach(radio => {
                        radio.addEventListener('change', toggleUaceFields);
                    });
                });
            </script>


            <!-- UCE Section -->
            <div class="form-section">
                <h4 class="section-title">8. Uganda Certificate of Education (UCE) Details</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Have you passed Uganda Certificate of Education Exams [UCE]?</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="uce[passed]" id="uceYes"
                                    value="yes" {{ old('uce.passed') == 'yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="uceYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="uce[passed]" id="uceNo"
                                    value="no" {{ old('uce.passed') == 'no' ? 'checked' : '' }}>
                                <label class="form-check-label" for="uceNo">No</label>
                            </div>
                        </div>
                        @error('uce.passed')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Year of UCE Examination</label>
                        <input type="text" id="uceYear"
                            class="form-control @error('uce.year') is-invalid @enderror"
                            placeholder="Enter year (e.g., 2015)" name="uce[year]" value="{{ old('uce.year') }}">
                        @error('uce.year')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 10; $i++)
                                    <tr>
                                        <td>
                                            <input type="text"
                                                class="form-control uce-score-subject @error("uce.scores.$i.subject") is-invalid @enderror"
                                                placeholder="Subject"
                                                name="uce[scores][{{ $i }}][subject]"
                                                value="{{ old("uce.scores.$i.subject") }}">
                                            @error("uce.scores.$i.subject")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control uce-score-grade @error("uce.scores.$i.grade") is-invalid @enderror"
                                                placeholder="Grade" name="uce[scores][{{ $i }}][grade]"
                                                value="{{ old("uce.scores.$i.grade") }}">
                                            @error("uce.scores.$i.grade")
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- JavaScript -->
            <script>
                function toggleUceFields() {
                    const passed = document.querySelector('input[name="uce[passed]"]:checked')?.value;
                    const shouldDisable = passed === 'no';

                    document.getElementById('uceYear').disabled = shouldDisable;

                    document.querySelectorAll('.uce-score-subject, .uce-score-grade').forEach(input => {
                        input.disabled = shouldDisable;
                    });
                }

                document.addEventListener('DOMContentLoaded', function() {
                    toggleUceFields(); // Run on load for old() data

                    document.querySelectorAll('input[name="uce[passed]"]').forEach(radio => {
                        radio.addEventListener('change', toggleUceFields);
                    });
                });
            </script>


            <!-- Section 5: Employment Record -->
            <div class="form-section">
                <h4 class="section-title">9. Employment Record</h4>

                @if ($errors->has('employment_record'))
                    <div class="text-danger mb-2">
                        Please correct the errors in the employment record section below.
                    </div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Year/Period</th>
                            <th>Position Held</th>
                            <th>Employer Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 10; $i++)
                            <tr>
                                <td>
                                    <input type="text"
                                        class="form-control @error("employment_record.$i.period") is-invalid @enderror"
                                        name="employment_record[{{ $i }}][period]"
                                        value="{{ old("employment_record.$i.period") }}">
                                    @error("employment_record.$i.period")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control @error("employment_record.$i.position") is-invalid @enderror"
                                        name="employment_record[{{ $i }}][position]"
                                        value="{{ old("employment_record.$i.position") }}">
                                    @error("employment_record.$i.position")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control @error("employment_record.$i.details") is-invalid @enderror"
                                        name="employment_record[{{ $i }}][details]"
                                        value="{{ old("employment_record.$i.details") }}">
                                    @error("employment_record.$i.details")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>


            <div class="form-section">
                <h4 class="section-title">10. Criminal History</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Have you ever been convicted on a criminal charge?</label>
                        <div class="d-flex gap-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('criminalHistory') is-invalid @enderror"
                                    type="radio" name="criminalHistory" id="crimeYes" value="yes"
                                    {{ old('criminalHistory') === 'yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="crimeYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('criminalHistory') is-invalid @enderror"
                                    type="radio" name="criminalHistory" id="crimeNo" value="no"
                                    {{ old('criminalHistory') === 'no' ? 'checked' : '' }}>
                                <label class="form-check-label" for="crimeNo">No</label>
                            </div>
                            @error('criminalHistory')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">If yes, provide details including sentence imposed:</label>
                            <textarea class="form-control @error('criminal_history_details') is-invalid @enderror" rows="3"
                                name="criminal_history_details">{{ old('criminal_history_details') }}</textarea>
                            @error('criminal_history_details')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Note: Conviction for a criminal offence will not necessarily prevent employment in the
                                Public Service, but false information is an offence.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">11. Availability & Salary Expectations</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">How soon would you be available for appointment if selected?</label>
                        <input type="text"
                            class="form-control @error('availability_if_appointed') is-invalid @enderror"
                            placeholder="e.g. Immediately, 2 weeks notice" name="availability_if_appointed"
                            value="{{ old('availability_if_appointed') }}">
                        @error('availability_if_appointed')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Minimum salary expectation</label>
                        <div class="input-group">
                            <span class="input-group-text">UGX</span>
                            <input type="number"
                                class="form-control @error('minimum_salary_expected') is-invalid @enderror"
                                placeholder="Expected monthly salary" name="minimum_salary_expected"
                                value="{{ old('minimum_salary_expected') }}">
                            @error('minimum_salary_expected')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>


            <div class="form-section">
                <h4 class="section-title">12. References & Recommendations</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <h6>For applicants NOT in Government Service:</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Reference 1 (Name & Address)</label>
                                <textarea class="form-control @error('reference.0') is-invalid @enderror" rows="2" name="reference[0]">{{ old('reference.0') }}</textarea>
                                @error('reference.0')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference 2 (Name & Address)</label>
                                <textarea class="form-control @error('reference.1') is-invalid @enderror" rows="2" name="reference[1]">{{ old('reference.1') }}</textarea>
                                @error('reference.1')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-text mt-2">Provide two responsible persons (not relatives) for character
                            reference</div>
                    </div>

                    <!-- For Government Applicants -->
                    <div class="col-12 mt-4">
                        <h6>For applicants IN Government Service:</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Name of Recommending Officer</label>
                                <input type="text"
                                    class="form-control @error('recommender_name') is-invalid @enderror"
                                    name="recommender_name" value="{{ old('recommender_name') }}">
                                @error('recommender_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Title/Designation</label>
                                <input type="text"
                                    class="form-control @error('recommender_title') is-invalid @enderror"
                                    name="recommender_title" value="{{ old('recommender_title') }}">
                                @error('recommender_title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-text mt-2">Permanent Secretary/Responsible Officer's recommendation</div>
                    </div>
                </div>
            </div>

            <!-- Add this section after the References & Recommendations section -->
            <div class="form-section">
                <h4 class="section-title">13. Document Uploads</h4>
                <div class="row g-3">
                    <!-- Academic Documents Upload -->
                    <div class="col-md-6">
                        <label class="form-label">Academic Documents (Combined PDF)</label>
                        <input type="file" class="form-control @error('academic_documents') is-invalid @enderror"
                            name="academic_documents[]" multiple accept="application/pdf">
                        @error('academic_documents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Upload all academic certificates/documents in PDF format (Max 2MB each)
                        </div>
                    </div>

                    <!-- Other Documents Upload -->
                    <div class="col-md-6">
                        <label class="form-label">Supporting Documents</label>
                        <div class="mb-3">
                            <input type="file" class="form-control @error('cv') is-invalid @enderror"
                                name="cv" accept="application/pdf">
                            @error('cv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">CV/Resume (PDF, Max 2MB)</div>
                        </div>

                        <div class="mb-3">
                            <input type="file" class="form-control @error('other_documents') is-invalid @enderror"
                                name="other_documents[]" multiple accept="application/pdf,image/*">
                            @error('other_documents')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Other relevant documents (PDF/Images, Max 2MB each)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-primary btn-lg" type="submit">Submit Application</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {


            //show swal alert
            @if (session('success'))
                swal({
                    title: "Success!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    button: "OK",
                });
            @elseif (session('error'))
                swal({
                    title: "Error!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    button: "OK",
                });
            @endif

        });
    </script>
</body>

</html>
