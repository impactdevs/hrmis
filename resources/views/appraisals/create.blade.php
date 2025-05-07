<x-app-layout>
    <form action="{{ route('appraisals.store') }}" method="post" class="m-2">
        @csrf
        <!-- IMPORTANT NOTES -->
        <div class="p-3 mb-4 border shadow card border-primary">
            <div class="p-0 mb-2 bg-white border-0 card-header">
                <legend class="w-auto text-primary d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                    <span class="mb-0 h5">IMPORTANT NOTES:</span>
                </legend>
                <small class="text-muted ms-4">Please read carefully before proceeding</small>
            </div>
            <div class="p-0 card-body">
                <ol type="I" style="list-style-type: upper-roman; padding-left: 20px;">
                    <li class="mb-2">
                        Completing Staff Performance Assessment Forms is mandatory for all UNCST members of staff,
                        including those who are on probation or temporary terms of service. Any employee on leave or
                        absent for any reason should have a review completed within 15 days of return to work.
                    </li>
                    <li class="mb-2">
                        The Appraisal process offers an opportunity to the appraiser and appraisee to discuss and obtain
                        feedback on performance, therefore participatory approach to the appraisal process, consistence
                        and objectivity are very important aspects of this exercise.
                    </li>
                    <li class="mb-2">
                        Oral interviews and appearance before a UNCST Management Assessment Panel may be done (under
                        Section 4) when deemed necessary and with the approval of the Executive Secretary before making
                        his/her overall assessment and final comments.
                    </li>
                    <li class="mb-0">
                        In cases where information to be filled in form does not fit in the space provided, the back
                        face of the same sheet may be used with an indication of a “PTO” where applicable.
                    </li>
                </ol>
            </div>
        </div>

        <!-- TYPE OF REVIEW -->
        <div class="mb-4 shadow card">
            <div class="text-white card-header bg-secondary">
                <h4 class="mb-0">TYPE OF REVIEW</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="p-3 rounded form-section bg-light">
                            <h5 class="mb-3 text-muted">Review Type</h5>
                            <div class="flex-wrap gap-4 d-flex">
                                <x-forms.radio name="review_type" label="Select the type of review"
                                    id="job_compatibility" :options="[
                                        'confirmation' => 'Confirmation',
                                        'end_of_contract' => 'End of Contract',
                                        'mid_financial_year' => 'Mid Financial Year',
                                    ]" />
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-3 rounded form-section bg-light">
                            <h5 class="mb-3 text-muted">Review Period</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control" name="appraisal_start_date"
                                        placeholder="Select start date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">End Date</label>
                                    <input type="date" class="form-control" name="appraisal_end_date"
                                        placeholder="Select end date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- APPRAISAL INFORMATION -->
        <div class="mb-4 shadow card appraisal-information">
            <div class="text-white card-header bg-secondary">
                <h4 class="mb-0">APPRAISAL INFORMATION</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="p-3 rounded form-section bg-light">
                            <div class="flex-wrap gap-4 d-flex">
                                <label for="supervisor">Appraiser</label>
                                <select class="employees form-control" name="appraiser_id" id="appraiser_id"
                                    data-placeholder="Choose the Appraiser" required>
                                    @foreach ($users as $user)
                                        <option value=""></option>
                                        <option value="{{ $user->employee->employee_id }}">{{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-3 rounded form-section bg-light">
                            <h5 class="mb-3 text-muted">Your Personal Details</h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <p> FULL NAME:
                                        {{ auth()->user()->employee->first_name . ' ' . auth()->user()->employee->last_name }}
                                    </p>
                                </div>
                                <div class="col-md-12">
                                    <p> POSITION:
                                        {{ optional(auth()->user()->employee->position)->position_name }}
                                    </p>
                                </div>

                                <div class="col-md-12">
                                    <p> DIVISION:
                                        {{ optional(auth()->user()->employee->department)->department_name }}
                                    </p>
                                </div>
                                <div class="col-md-12">
                                    <p> DATE OF 1ST APPOINTMENT:
                                        {{ \Carbon\Carbon::parse(auth()->user()->employee->date_of_entry)->toFormattedDateString() }}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERFORMANCE EVALUATION -->
        <div class="mb-4 shadow card">
            <div class="text-white card-header bg-info">
                <h4 class="mb-0">Performance Evaluation</h4>
            </div>
            <div class="card-body">
                <p>The following ratings should be used to ensure consistency on overall ratings: (provide supporting
                    comments to justify ratings of Excellent/outstanding 80% – 100%, Very good 70% - 79%, Satisfactory
                    60% - 69%, Average 50% - 59%, Unsatisfactory 0% - 49%.)
                    The overall total Score for the evaluation is 100% i.e., 60% - Key result areas and 40% for personal
                    attributes.
                </p>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%">Rating</th>
                                <th style="width: 60%">Description</th>
                                <th style="width: 10%">Score</th>
                                <th style="width: 15%">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-white bg-success">Excellent/Outstanding</td>
                                <td>Consistently exceeds work expectations and job requirements. Employee has exceeded
                                    all targets and has consistently produced outputs/results of excellent quality.</td>
                                <td>5</td>
                                <td>80–100%</td>
                            </tr>
                            <tr>
                                <td class="text-white bg-primary">Very Good</td>
                                <td>Consistently meets work expectations and job requirements. Employee achieved all
                                    planned outputs, and the quality of work overall was very good.</td>
                                <td>4</td>
                                <td>70–79%</td>
                            </tr>
                            <tr>
                                <td class="text-white bg-info">Satisfactory</td>
                                <td>Performance consistently meets most work expectations and job requirements. Achieved
                                    most but not all of the agreed outputs, with no supporting rationale for inability
                                    to meet all commitments.</td>
                                <td>3</td>
                                <td>60–69%</td>
                            </tr>
                            <tr>
                                <td class="bg-warning text-dark">Average</td>
                                <td>Does not consistently meet work expectations and requirements but achieved minimal
                                    outputs compared to planned outputs, with no supporting rationale for inability to
                                    meet commitments.</td>
                                <td>2</td>
                                <td>50–59%</td>
                            </tr>
                            <tr>
                                <td class="text-white bg-danger">Unsatisfactory</td>
                                <td>Consistently below expectations and job requirements. Employee has not achieved most
                                    of the planned outputs, with no supporting rationale for not achieving them, and has
                                    demonstrated inability or unwillingness to improve.</td>
                                <td>1</td>
                                <td>0–49%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- KEY DUTIES SECTION -->
        <div class="mb-4 shadow card">
            <div class="text-white card-header bg-primary">
                <h4 class="mb-0">SECTION 1 - KEY DUTIES & TASKS</h4>
                <small class="fw-light">Staff Self-Assessment</small>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12">
                        <p class="fw-bold">a. Planned Activities and Outputs</p>
                        <p>List the major planned activities and indicate the extent of accomplishment during the
                            appraisal period, including outputs/results attained. You may include activities outside
                            your job description but falling in line with your duties.</p>
                        <div id="repeater-wrapper">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <x-forms.text-area name="appraisal_period_accomplishment[0][planned_activity]"
                                        label="Planned Activities/Tasks" id="planned_activity" :value="old('planned_activity', $appraisal->planned_activity ?? '')" />

                                    <x-forms.text-area name="appraisal_period_accomplishment[0][output_results]"
                                        label="Outputs/Results" id="output_results" :value="old('output_results', $appraisal->output_results ?? '')" />

                                    <x-forms.text-area name="appraisal_period_accomplishment[0][remarks]"
                                        label="Remarks" id="remarks" :value="old('remarks', $appraisal->remarks ?? '')" />


                                </div>

                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" id="add-repeater">Add
                                    Activities</button>
                            </div>
                            <span>Click the button to add on your list</span>

                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">b. Job Compatibility</p>
                            <x-forms.radio name="job_compatibility"
                                label="Is the job and tasks performed compatible with your qualifications and experience?"
                                id="job_compatibility" :options="['yes' => 'Yes', 'no' => 'No']" />
                            <x-forms.text-area name="if_no_job_compatibility" label="If No, explain:"
                                id="if_no_job_compatibility" :value="old('if_no_job_compatibility', $appraisal->if_no_job_compatibility ?? '')" />
                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">c. Challenges</p>
                            <x-forms.text-area name="unanticipated_constraints"
                                label="Briefly state unanticipated constraints/problems that you encountered and how they affected the achievements of the objectives."
                                id="unanticipated_constraints" :value="old(
                                    'unanticipated_constraints',
                                    $appraisal->unanticipated_constraints ?? '',
                                )" />
                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">d. Personal Initiatives</p>
                            <x-forms.text-area name="personal_initiatives"
                                label="Outline personal initiatives and any other factors that you think contributed to your achievements and successes."
                                id="personal_initiatives" :value="old('personal_initiatives', $appraisal->personal_initiatives ?? '')" />
                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">e. Training Support Needs</p>
                            <x-forms.text-area name="training_support_needs"
                                label="Indicate the nature of training support you may need to effectively perform your duties. Training support should be consistent with the job requirements and applicable to UNCST policies and regulations."
                                id="training_support_needs" :value="old('training_support_needs', $appraisal->training_support_needs ?? '')" />
                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">f. Rating of Major Planned Activities</p>
                            <p>Rate the list of major planned activities and indicate the extent of accomplishment
                                during
                                the appraisal period, including outputs/results attained. You may also rate activities
                                outside your job description but falling in line with your duties.</p>
                            <div id="rate-repeater-wrapper">
                                <div class="mt-2 row g-3 repeater-rate-item">
                                    <div class="col-md-12">
                                        <x-forms.text-area name="appraisal_period_rate[0][planned_activity]"
                                            label="Planned Activities/Tasks"
                                            id="appraisal_period_rate_0_planned_activity"
                                            placeholder="Enter Planned Activities" :value="old('planned_activity', $appraisal->planned_activity ?? '')" />

                                        <x-forms.text-area name="appraisal_period_rate[0][output_results]"
                                            label="Outputs/Results" id="appraisal_period_rate_0_output_results"
                                            placeholder="Enter Outputs" :value="old('output_results', $appraisal->output_results ?? '')" />

                                        <x-forms.input name="appraisal_period_rate[0][supervisee_score]"
                                            label="Supervisee's Score (out of 5)" type="number"
                                            id="appraisal_period_rate_0_supervisee_score"
                                            placeholder="Supervisee Score"
                                            value="{{ old('supervisee_score', $appraisal->supervisee_score ?? '') }}" />

                                        <x-forms.input name="appraisal_period_rate[0][superviser_score]" 
                                            label="Supervisor's Score (out of 5)" type="number"
                                            id="appraisal_period_rate_0_superviser_score"
                                            placeholder="Supervisor Score"
                                            value="{{ old('superviser_score', $appraisal->superviser_score ?? '') }}" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" id="add-rate-repeater">Add
                                        Rate</button>
                                </div>
                                <span>click the button to add a rate</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- PERSONAL ATTRIBUTES SECTION -->
            <div class="mb-4 shadow card">
                <div class="text-white card-header bg-primary">
                    <h4 class="mb-0">SECTION 2 - PERSONAL ATTRIBUTES</h4>
                    <small class="fw-light">Joint Assessment</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-striped table-hover table-borderless table-primary">
                            <caption>Rating: 80-100 – Excellent 70-79 – Very Good 60-69 - Satisfactory 50-59 – Average
                                0-49
                                - Unsatisfactory </caption>
                            <thead class="table-light">
                                <tr>
                                    <th>Measurable Indicators/Personal Attributes</th>
                                    <th>Maximum Score</th>
                                    <th>Appraisee’s Score</th>
                                    <th>Appraiser’s Score</th>
                                    <th>Agreed Score</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Technical and Professional Knowledge</span>
                                        <p class="mb-0 text-muted small">Exhibits basic technical and professional
                                            knowledge of the assigned tasks</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Commitment to Mission</span>
                                        <p class="mb-0 text-muted small">Understands and exhibits a sense of working
                                            for
                                            the UNCST & at all times projects the interest of the Organization as a
                                            priority.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[commitment][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[commitment][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[commitment][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Team Work</span>
                                        <p class="mb-0 text-muted small">Is reliable, cooperates with other staff, is
                                            willing to share information, resources and knowledge with others. Exhibits
                                            sensitivity to deadlines and to the time constraints of other
                                            staff/departments.
                                        </p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[team_work][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[team_work][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[team_work][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Productivity and Organizational Skills</span>
                                        <p class="mb-0 text-muted small">Makes efficient use of time, fulfilling
                                            responsibilities and completing tasks by deadlines. Demonstrates
                                            responsiveness
                                            and structured approach to tasks.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[productivity][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[productivity][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[productivity][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Integrity</span>
                                        <p class="mb-0 text-muted small">Is honest and trustworthy, follows procedures,
                                            takes responsibility, and respects others. Deals with conflict
                                            professionally
                                            and values diversity.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[integrity][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[integrity][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[integrity][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Flexibility and Adaptability</span>
                                        <p class="mb-0 text-muted small">Willing to take on new job responsibilities or
                                            to
                                            assist the Organization through peak workloads. Able to accept the changing
                                            needs of the organization with enthusiasm.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[flexibility][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[flexibility][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[flexibility][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Attendance and Punctuality</span>
                                        <p class="mb-0 text-muted small">Maintains agreed upon work schedule and does
                                            not
                                            abuse leave/sick time. Maintains agreed upon work hours and does not abuse
                                            break/lunch policies. Keeps supervisor other staff informed of itinerary all
                                            the
                                            time.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[attendance][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[attendance][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[attendance][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Professional Appearance</span>
                                        <p class="mb-0 text-muted small">Maintains professional appearance, always
                                            neat,
                                            presentable, descent and keeps the work space in an orderly, clean and
                                            professional manner. </p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[appearance][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[appearance][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[appearance][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Interpersonal Relations</span>
                                        <p class="mb-0 text-muted small">Maintains a positive and balanced disposition
                                            towards fellow employees, the Organization and the assigned job
                                            responsibilities. Deals directly with people in order to establish
                                            harmonious
                                            working relationships, offering positive win-win solutions in dealing with
                                            problem or conflict situations.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[interpersonal][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[interpersonal][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[interpersonal][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Initiative</span>
                                        <p class="mb-0 text-muted small">In unsupervised situations able to anticipate,
                                            and
                                            act on, the needs of the Organization. Proactively seeks out new
                                            responsibilities and offers solutions on improving efficiency and
                                            productivity
                                            and also demonstrates the ability to perceive alternatives and make good
                                            decisions. </p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[initiative][appraisee_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[initiative][appraiser_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[initiative][agreed_score]"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5"></td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th>Total Score</th>
                                    <th>40%</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>

            <!-- PERFORMANCE PLANNING  -->
            <fieldset class="p-2 mb-4 border">
                <legend class="w-auto">PERFORMANCE PLANNING </legend>
                <div class="mb-3 row">
                    <p>The Appraiser and Appraisee discuss and agree on the key outputs for the next performance cycle.
                    </p>
                </div>
                <div id="performance-planning-wrapper">
                    <div class="row g-3 repeater-item">
                        <div class="col-md-6">
                            <x-forms.text-area name="performance_planning[0][description]"
                                label="Key Output Description" id="description" placeholder="Key Output Description"
                                :value="old('performance_planning.0.description', $appraisal->description ?? '')" />
                        </div>

                        <div class="col-md-6">
                            <x-forms.text-area name="performance_planning[0][performance_target]"
                                label="Agreed Performance Targets" id="performance_target"
                                placeholder="Agreed Performance Targets" :value="old(
                                    'performance_planning.0.performance_target',
                                    $appraisal->performance_target ?? '',
                                )" />
                        </div>

                        <div class="col-md-6">
                            <x-forms.input name="performance_planning[0][target_date]" label="Target Date"
                                type="date" id="target_date" placeholder="Target Date"
                                value="{{ old('performance_planning.0.target_date', $appraisal->target_date ?? '') }}" />
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="add-performance-row">Add Plan</button>
                    </div>
                </div>

            </fieldset>

            <!-- IMMEDIATE SUPERVISOR'S REPORT  -->
            <fieldset class="p-2 mb-4 border">
                <legend class="w-auto">SECTION 3</legend>
                <H1>IMMEDIATE SUPERVISOR'S REPORT</H1>
                <p>(To be completed by Appraiser (Supervisor-Head of Division) after taking into consideration
                    information
                    provided in sections 1 and 2 above)</p>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="employee_strength"
                            label="i. Strengths - Summarize employee's strengths as demonstrated since the last performance review."
                            id="employee_strength" :value="old('employee_strength', $appraisal->employee_strength ?? '')" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="employee_improvement"
                            label="ii.	Areas for Improvement - Summarize employee’s areas for improvement"
                            id="employee_improvement" :value="old('employee_improvement', $appraisal->employee_improvement ?? '')" />
                    </div>


                    <div class="col-md-12">
                        <x-forms.text-area name="superviser_overall_assessment"
                            label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                            id="superviser_overall_assessment" :value="old(
                                'superviser_overall_assessment',
                                $appraisal->superviser_overall_assessment ?? '',
                            )" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="recommendations"
                            label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                            id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" />
                    </div>
                </div>
            </fieldset>

            <!-- EVALUATION BY REVIEW PANEL   -->
            <fieldset class="p-2 mb-4 border">
                <legend class="w-auto">EVALUATION BY REVIEW PANEL</legend>

                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="panel_comment" label="(a)	Comments of the Panel." id="panel_comment"
                            :value="old('panel_comment', $appraisal->panel_comment ?? '')" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="panel_recommendation" label="(b)	Recommendation of the Panel"
                            id="panel_recommendation" :value="old('panel_recommendation', $appraisal->panel_recommendation ?? '')" />
                    </div>


                    <div class="col-md-12">
                        <x-forms.text-area name="overall_assessment"
                            label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                            id="overall_assessment" :value="old('overall_assessment', $appraisal->overall_assessment ?? '')" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="recommendations"
                            label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                            id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" />
                    </div>
                </div>
            </fieldset>

            {{-- OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY --}}
            <fieldset class="p-2 mb-4 border">
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="executive_secretary_comments"
                            label="OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY"
                            id="executive_secretary_comments" :value="old(
                                'executive_secretary_comments',
                                $appraisal->executive_secretary_comments ?? '',
                            )" />
                    </div>
                </div>
            </fieldset>

            <!-- SUBMIT SECTION -->
            <div class="py-4 bg-white sticky-bottom border-top">
                <div class="container-lg">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-text">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            All fields are required unless marked optional
                        </div>
                        <div class="gap-3 d-flex">
                            <button type="reset" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-lg btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Review
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </form>

    <style>
        .notes-list {
            counter-reset: section;
            padding-left: 0;
        }

        .notes-list li {
            counter-increment: section;
            list-style: none;
            position: relative;
            padding-left: 2.5rem;
        }

        .notes-list li::before {
            content: counter(section, upper-roman);
            position: absolute;
            left: 0;
            top: -0.1em;
            font-weight: bold;
            color: #0d6efd;
            font-size: 1.2em;
        }

        .form-section {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .form-section:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }

        .score-input {
            width: 60px;
            margin: 0 auto;
            text-align: center;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .sticky-bottom {
            position: sticky;
            bottom: 0;
            z-index: 1020;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
    @push('scripts')
        <script>
            $(document).ready(function() {


                $('.employees').select2({
                    theme: "bootstrap-5",
                    placeholder: $(this).data('placeholder'),
                    dropdownParent: $('.appraisal-information') // or a higher container without overflow hidden
                });


                let repeaterIndex = 1;

                $('#add-repeater').click(function() {
                    console.log("clicked")
                    let newRow = `
        <div class="mt-2 row g-3 repeater-item">
            <div class="col-md-12">
                <x-forms.text-area name="appraisal_period_accomplishment[${repeaterIndex}][planned_activity]"
                    id="appraisal_period_accomplishment[${repeaterIndex}][planned_activity]"
                    label="Planned Activities/Tasks" placeholder="Enter Planned Activities" />

                <x-forms.text-area name="appraisal_period_accomplishment[${repeaterIndex}][output_results]"
                    id="appraisal_period_accomplishment[${repeaterIndex}][output_results]"
                    label="Outputs/Results" placeholder="Enter Outputs" />

                <x-forms.text-area name="appraisal_period_accomplishment[${repeaterIndex}][remarks]"
                    id="appraisal_period_accomplishment[${repeaterIndex}][remarks]"
                    label="Remarks" placeholder="Remarks" />
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-repeater">Remove</button>
            </div>
        </div>`;

                    $('#repeater-wrapper').append(newRow);
                    repeaterIndex++;
                });


                $(document).on('click', '.remove-repeater', function() {
                    $(this).closest('.repeater-item').remove();
                });

                let rateRepeaterIndex = 1;

                $('#add-rate-repeater').click(function() {
                    let newRateRow = `
        <div class="mt-2 row g-3 repeater-rate-item">
            <div class="col-md-12">
                <x-forms.text-area name="appraisal_period_rate[${rateRepeaterIndex}][planned_activity]"
                    label="Planned Activities/Tasks" id="appraisal_period_rate_${rateRepeaterIndex}_planned_activity"
                    placeholder="Enter Planned Activities" />

                <x-forms.text-area name="appraisal_period_rate[${rateRepeaterIndex}][output_results]"
                    label="Outputs/Results" id="appraisal_period_rate_${rateRepeaterIndex}_output_results"
                    placeholder="Enter Outputs" />

                <x-forms.input name="appraisal_period_rate[${rateRepeaterIndex}][supervisee_score]"
                    label="Supervisee's Score (out of 5)" type="number" id="appraisal_period_rate_${rateRepeaterIndex}_supervisee_score"
                    placeholder="Supervisee Score" />

                <x-forms.input name="appraisal_period_rate[${rateRepeaterIndex}][superviser_score]" 
                    label="Supervisor's Score (out of 5)" type="number" id="appraisal_period_rate_${rateRepeaterIndex}_superviser_score"
                    placeholder="Supervisor Score" />
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-rate-repeater">Remove</button>
            </div>
        </div>`;
                    $('#rate-repeater-wrapper').append(newRateRow);
                    rateRepeaterIndex++;
                });

                // Optional: remove handler
                $(document).on('click', '.remove-rate-repeater', function() {
                    $(this).closest('.repeater-rate-item').remove();
                });

                let planningIndex = 1;

                $('#add-performance-row').click(function() {
                    let newRow = `
                                    <div class="mt-2 row g-3 repeater-item">
                                    <div class="col-md-6">
                      <x-forms.text-area
                        name="performance_planning[${planningIndex}][description]"
                        label="Key Output Description"
                        id="performance_planning[${planningIndex}][description]"
                        placeholder="Key Output Description"
                        :value="old('performance_planning.0.description', $appraisal->description ?? '')" />
                    </div>
                    <div class="col-md-6">


                                            <x-forms.text-area name="performance_planning[${planningIndex}][performance_target]"
                                label="Agreed Performance Targets" id="performance_planning[${planningIndex}][performance_target]"
                                placeholder="Agreed Performance Targets" :value="old(
                                    'performance_planning.0.performance_target',
                                    $appraisal->performance_target ?? '',
                                )" />
        </div>
        <div class="col-md-6">
            <x-forms.input name="performance_planning[${planningIndex}][target_date]" id="performance_planning[${planningIndex}][target_date]" label="Target Date"
                type="text" placeholder="Target Date" />
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-planning-row">Remove</button>
        </div>
    </div>`;

                    $('#performance-planning-wrapper').append(newRow);
                    planningIndex++;
                });

                $(document).on('click', '.remove-planning-row', function() {
                    $(this).closest('.repeater-item').remove();
                });

            });
        </script>
    @endpush
</x-app-layout>
