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
        <h2 class="text-center mb-4">APPLICATION FOR APPOINTMENT TO THE UGANDA PUBLIC SERVICE</h2>

        <!-- Section 1: Post & Personal Details -->
        <div class="form-section">
            <h4 class="section-title">1. Post & Personal Details</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Post applied for</label>
                    <input type="text" class="form-control" name="personal_details[post]">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Reference Number</label>
                    <input type="text" class="form-control" name="personal_details[reference_number]">
                </div>

                <div class="col-md-8">
                    <label class="form-label">Full name (Surname first in CAPITALS)</label>
                    <input type="text" class="form-control" name="personal_details[full_name]">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="personal_details[date_of_birth]">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="personal_details[email]">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telephone Number</label>
                    <input type="tel" class="form-control" name="personal_details[telephone_number]">
                </div>
            </div>
        </div>
        <!-- Section 2: Nationality & Residence -->
        <div class="form-section">
            <h4 class="section-title">2. Nationality & Residence</h4>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Nationality</label>
                    <input type="text" class="form-control" name="nationality_and_residence[nationality]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Home District</label>
                    <input type="text" class="form-control" name="nationality_and_residence[home_district]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sub-county</label>
                    <input type="text" class="form-control" name="nationality_and_residence[sub_county]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Village</label>
                    <input type="text" class="form-control" name="nationality_and_residence[village]">
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label">Are you a temporary or permanent resident in Uganda?</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="nationality_and_residence[residency_type]" id="temporary">
                            <label class="form-check-label" for="temporary">
                                Temporary
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="nationality_and_residence[residency_type]" id="permanent">
                            <label class="form-check-label" for="permanent">
                                Permanent
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Work Background -->
        <div class="form-section">
            <h4 class="section-title">3. Work Background</h4>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Present Ministry/Local Government/Department/Any other Employer</label>
                    <input type="text" class="form-control" name="work_background[present_department]">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Present post</label>
                    <input type="text" class="form-control" name="work_background[present_post]">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date of appointment to current post</label>
                    <input type="date" class="form-control"
                        name="work_background[date_of_appointment_to_present_post]">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Terms of Employment</label>
                    <div class="d-flex gap-4 flex-wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="work_background[terms_of_employment]" id="temp">
                            <label class="form-check-label" for="temp">Temporary</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="work_background[terms_of_employment]" id="contract">
                            <label class="form-check-label" for="contract">Contract</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="work_background[terms_of_employment]" id="probation">
                            <label class="form-check-label" for="probation">Probation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="work_background[terms_of_employment]" id="perm">
                            <label class="form-check-label" for="perm">Permanent</label>
                        </div>
                    </div>
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
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="family_background[marital_status]"
                                id="married">
                            <label class="form-check-label" for="married">Married</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="family_background[marital_status]"
                                id="single">
                            <label class="form-check-label" for="single">Single</label>
                        </div>
                        </div>
                    </div>
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
                    <tr>
                        <td><input type="text" class="form-control" name="education_history[0][period]"></td>
                        <td><input type="text" class="form-control" name="education_history[0][institution]"></td>
                        <td><input type="text" class="form-control" name="education_history[0][award]"></td>
                    </tr>

                    <tr>
                        <td><input type="text" class="form-control" name="education_history[1][period]"></td>
                        <td><input type="text" class="form-control" name="education_history[1][institution]"></td>
                        <td><input type="text" class="form-control" name="education_history[1][award]"></td>
                    </tr>

                    <tr>
                        <td><input type="text" class="form-control" name="education_history[2][period]"></td>
                        <td><input type="text" class="form-control" name="education_history[2][institution]"></td>
                        <td><input type="text" class="form-control" name="education_history[2][ward]"></td>
                    </tr>

                    <tr>
                        <td><input type="text" class="form-control" name="education_history[3][period]"></td>
                        <td><input type="text" class="form-control" name="education_history[3][institution]"></td>
                        <td><input type="text" class="form-control" name="education_history[3][award]"></td>
                    </tr>

                    <tr>
                        <td><input type="text" class="form-control" name="education_history[4][period]"></td>
                        <td><input type="text" class="form-control" name="education_history[4][institution]"></td>
                        <td><input type="text" class="form-control" name="education_history[4][award]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="education_history[5][period]"></td>
                        <td><input type="text" class="form-control" name="education_history[5][institution]"></td>
                        <td><input type="text" class="form-control" name="education_history[5][award]"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-section">
            <h4 class="section-title">6. Uganda Certificate of Education (UCE) Details</h4>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Have you passed Uganda Certificate of Education Exams [UCE]?</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="uce[passed]" id="uceYes"
                                value="yes">
                            <label class="form-check-label" for="uceYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="uce[passed]" id="uceNo"
                                value="no">
                            <label class="form-check-label" for="uceNo">No</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year of UCE Examination</label>
                    <input type="text" class="form-control" placeholder="Enter year (e.g., 2015)"
                        name="uce[year]">
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
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][0][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][0][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][1][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][1][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][2][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][2][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][3][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][3][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][4][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][4][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][5][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][5][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][6][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][6][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][7][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][7][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][8][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][8][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uce[scores][9][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uce[scores][9][grade]"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <!-- New UCE Section -->
        <div class="form-section">
            <h4 class="section-title">7. Uganda Advanced Certificate of Education (UACE) Details</h4>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Have you passed Uganda Advanced Certificate of Education Exams
                        [UCE]?</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="uace[passed]" id="uaceYes"
                                value="yes">
                            <label class="form-check-label" for="uaceYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="uace[passed]" id="uceNo"
                                value="no">
                            <label class="form-check-label" for="uaceNo">No</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year of UACE Examination</label>
                    <input type="text" class="form-control" placeholder="Enter year (e.g., 2015)"
                        name="uace[year]">
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
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uace[scores][0][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uace[scores][0][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uace[scores][1][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uace[scores][1][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uace[scores][2][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uace[scores][2][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uace[scores][3][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uace[scores][3][grade]"></td>
                            </tr>
                            <tr>
                                <td><input type="text" class="form-control" placeholder="Subject"
                                        name="uace[scores][4][subject]"></td>
                                <td><input type="text" class="form-control" placeholder="Grade"
                                        name="uace[scores][4][grade]"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="form-section">
            <h4 class="section-title">7. Employment Record</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Year/Period</th>
                        <th>Position Held</th>
                        <th>Employer Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[0][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[0][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[0][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[1][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[1][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[1][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[2][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[2][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[2][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[3][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[3][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[3][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[4][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[4][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[4][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[5][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[5][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[5][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[6][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[6][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[6][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[7][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[7][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[7][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[8][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[8][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[8][details]"></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="form-control" name="employment_record[9][period]"></td>
                        <td><input type="text" class="form-control" name="employment_record[9][position]"></td>
                        <td><input type="text" class="form-control" name="employment_record[9][details]"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-section">
            <h4 class="section-title">8. Criminal History</h4>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Have you ever been convicted on a criminal charge?</label>
                    <div class="d-flex gap-4 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="criminalHistory" id="crimeYes">
                            <label class="form-check-label" for="crimeYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="criminalHistory" id="crimeNo">
                            <label class="form-check-label" for="crimeNo">No</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">If yes, provide details including sentence imposed:</label>
                        <textarea class="form-control" rows="3" name="criminal_history_details"></textarea>
                        <div class="form-text">Note: Conviction for a criminal offence will not necessarily prevent
                            employment in the Public Service, but false information is an offence.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4 class="section-title">9. Availability & Salary Expectations</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">How soon would you be available for appointment if selected?</label>
                    <input type="text" class="form-control" placeholder="e.g. Immediately, 2 weeks notice"
                        name="availability_if_appointed">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Minimum salary expectation</label>
                    <div class="input-group">
                        <span class="input-group-text">UGX</span>
                        <input type="number" class="form-control" placeholder="Expected monthly salary"
                            name="minimum_salary_expected">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h4 class="section-title">10. References & Recommendations</h4>
            <div class="row g-3">
                <div class="col-12">
                    <h6>For applicants NOT in Government Service:</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Reference 1 (Name & Address)</label>
                            <textarea class="form-control" rows="2" name="reference[0]"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference 2 (Name & Address)</label>
                            <textarea class="form-control" rows="2" name="reference[1]"></textarea>
                        </div>
                    </div>
                    <div class="form-text mt-2">Provide two responsible persons (not relatives) for character reference
                    </div>
                </div>

                <!-- For Government Applicants -->
                <div class="col-12 mt-4">
                    <h6>For applicants IN Government Service:</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Name of Recommending Officer</label>
                            <input type="text" class="form-control" name="recommender_name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title/Designation</label>
                            <input type="text" class="form-control" name="recommender_title">
                        </div>
                    </div>
                    <div class="form-text mt-2">Permanent Secretary/Responsible Officer's recommendation</div>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-primary btn-lg" type="submit">Submit Application</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
