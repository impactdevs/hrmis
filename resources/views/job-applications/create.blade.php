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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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

        <form method="POST" action="{{ route('uncst-job-applications.store') }}" enctype="multipart/form-data">
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
                    <div class="col-md-6">
                        <label class="form-label">Nationality</label>
                        <input type="text"
                            class="form-control @error('nationality_and_residence.nationality') is-invalid @enderror"
                            name="nationality_and_residence[nationality]"
                            value="{{ old('nationality_and_residence.nationality') }}">
                        @error('nationality_and_residence.nationality')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
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

            <!-- Section 5: Employment Record -->
            <div class="form-section">
                <h4 class="section-title">3. Employment Record(only if you have ever been employed)</h4>

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
                    <tbody id="employment-record-tbody">
                        @php
                            $employmentRecords = old('employment_record', []);
                            $rowCount = max(count($employmentRecords), 1);
                        @endphp
                        @for ($i = 0; $i < $rowCount; $i++)
                            <tr>
                                <td>
                                    <input type="text"
                                        class="form-control @error("employment_record.$i.period") is-invalid @enderror"
                                        name="employment_record[{{ $i }}][period]" placeholder="e.g. 2020-2023"
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
                                <td class="text-center align-middle">
                                    @if ($i > 0)
                                        <button type="button"
                                            class="btn btn-danger btn-sm remove-employment-row">&times;</button>
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    id="add-employment-row">
                                    + Add Row
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                    <script>
                        $(document).ready(function() {
                            let rowIdx = $('#employment-record-tbody tr').length;

                            $('#add-employment-row').on('click', function() {
                                let newRow = `
                <tr>
                    <td>
                        <input type="text" class="form-control" name="employment_record[${rowIdx}][period]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="employment_record[${rowIdx}][position]">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="employment_record[${rowIdx}][details]">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm remove-employment-row">&times;</button>
                    </td>
                </tr>
            `;
                                $('#employment-record-tbody').append(newRow);
                                rowIdx++;
                            });

                            $(document).on('click', '.remove-employment-row', function() {
                                $(this).closest('tr').remove();
                            });
                        });
                    </script>
                </table>
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
            <!-- Section 5: Summary of Education and Training (Start with most Recent) -->
            <div class="form-section">
                <h4 class="section-title">5. Summary of Education and Training (Start with most Recent)</h4>
                @if ($errors->has('education_training'))
                    <div class="text-danger mb-2">
                        Please correct the errors in the education and training section below.
                    </div>
                @endif
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Qualification</th>
                            <th>Institution</th>
                            <th>Year of Award</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="education-training-tbody">
                        @php
                            $educationTraining = old('education_training', []);
                            $eduRowCount = max(count($educationTraining), 1);
                        @endphp
                        @for ($i = 0; $i < $eduRowCount; $i++)
                            <tr>
                                <td>
                                    <input type="text"
                                        class="form-control @error("education_training.$i.qualification") is-invalid @enderror"
                                        name="education_training[{{ $i }}][qualification]"
                                        value="{{ old("education_training.$i.qualification") }}">
                                    @error("education_training.$i.qualification")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control @error("education_training.$i.institution") is-invalid @enderror"
                                        name="education_training[{{ $i }}][institution]"
                                        value="{{ old("education_training.$i.institution") }}">
                                    @error("education_training.$i.institution")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control @error("education_training.$i.year") is-invalid @enderror"
                                        name="education_training[{{ $i }}][year]" placeholder="e.g. 2020"
                                        value="{{ old("education_training.$i.year") }}">
                                    @error("education_training.$i.year")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center align-middle">
                                    @if ($i > 0)
                                        <button type="button" class="btn btn-danger btn-sm remove-education-row">&times;</button>
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-education-row">
                                    + Add Row
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <script>
                    $(document).ready(function() {
                        let eduRowIdx = $('#education-training-tbody tr').length;

                        $('#add-education-row').on('click', function() {
                            let newRow = `
                            <tr>
                                <td>
                                    <input type="text" class="form-control" name="education_training[${eduRowIdx}][qualification]">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="education_training[${eduRowIdx}][institution]">
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="education_training[${eduRowIdx}][year]">
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-danger btn-sm remove-education-row">&times;</button>
                                </td>
                            </tr>
                            `;
                            $('#education-training-tbody').append(newRow);
                            eduRowIdx++;
                        });

                        $(document).on('click', '.remove-education-row', function() {
                            $(this).closest('tr').remove();
                        });
                    });
                </script>
            </div>



            <div class="form-section">
                <h4 class="section-title">6. Criminal History</h4>
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">7. Availability & Salary Expectations</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">How soon would you be available for appointment if
                            selected?</label>
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
                <h4 class="section-title">8. References & Recommendations</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <h6>Provide Names, Telephone Numbers and Email addresses of three refrees to be contacted
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Reference 1 (Name, Telephone Number & Email)</label>
                                <textarea class="form-control @error('reference.0') is-invalid @enderror" rows="2" name="reference[0]">{{ old('reference.0') }}</textarea>
                                @error('reference.0')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference 2 (Name, Telephone Number & Email)</label>
                                <textarea class="form-control @error('reference.1') is-invalid @enderror" rows="2" name="reference[1]">{{ old('reference.1') }}</textarea>
                                @error('reference.1')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Reference 3 (Name, Telephone Number & Email)</label>
                                <textarea class="form-control @error('reference.2') is-invalid @enderror" rows="2" name="reference[2]">{{ old('reference.2') }}</textarea>
                                @error('reference.2')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add this section after the References & Recommendations section -->
            <div class="form-section">
                <h4 class="section-title">9. Document Uploads</h4>
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
                            <div class="form-text">CV/Resume (PDF, Max 2MB), Cover Letter</div>
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
