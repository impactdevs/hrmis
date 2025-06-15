<x-app-layout>
    @php
        $rejectedEntry = collect($appraisal->appraisal_request_status)
            ->filter(fn($status) => $status === 'rejected')
            ->keys()
            ->first(); // Get the first person/role who rejected

        $rejectionReason = $appraisal->rejection_reason ?? 'No reason provided.';
    @endphp

    @if ($rejectedEntry)
        <!-- Toast for rejection -->
        <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3 text-bg-danger" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-x-octagon-fill text-danger" viewBox="0 0 16 16">
                    <path
                        d="M11.46.146A.5.5 0 0 1 12 .5v3.793a.5.5 0 0 1-.146.354l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 0 1 0-.708l7-7A.5.5 0 0 1 8.207.146L11.46.146z" />
                </svg>
                <strong class="me-auto">Rejected by {{ $rejectedEntry }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <strong>Reason:</strong> {{ $rejectionReason }}
            </div>
        </div>

        {{-- Trigger the toast via JS --}}
        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                const toastEl = document.querySelector('.toast-container .toast');
                if (toastEl) {
                    new bootstrap.Toast(toastEl).show();
                }
            });
        </script>
    @endif


    <form action="{{ route('uncst-appraisals.update', $appraisal->appraisal_id) }}" method="post" class="m-2">
        @csrf
        @method('PUT')
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
                                <x-forms.radio name="review_type" label="Select the type of review" id="review_type"
                                    value="{{ $appraisal->review_type ?? '' }}" :options="[
                                        'confirmation' => 'Confirmation',
                                        'end_of_contract' => 'End of Contract',
                                        'mid_financial_year' => 'Mid Financial Year',
                                    ]" :selected="$appraisal->review_type ?? ''" />
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-3 rounded form-section bg-light">
                            <h5 class="mb-3 text-muted">Review Period</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.input name="appraisal_start_date" label="Start Date" type="date"
                                        id="appraisal_start_date"
                                        value="{{ old('appraisal_start_date', isset($appraisal) && $appraisal->appraisal_start_date ? $appraisal->appraisal_start_date->toDateString() : '') }}" />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="appraisal_end_date" label="End Date" type="date"
                                        id="appraisal_end_date"
                                        value="{{ old('appraisal_end_date', isset($appraisal) && $appraisal->appraisal_end_date ? $appraisal->appraisal_end_date->toDateString() : '') }}" />
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
                                        <option value="{{ $user->employee->employee_id }}"
                                            {{ $user->employee && $user->employee->employee_id == $appraisal->appraiser_id ? 'selected' : '' }}>
                                            {{ $user->name }}
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
                            @foreach ($appraisal->appraisal_period_accomplishment as $appraisal_period_accomplishment)
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <x-forms.text-area
                                            name="appraisal_period_accomplishment[{{ $loop->index }}][planned_activity]"
                                            id="planned_activity_{{ $loop->index }}"
                                            label="Planned Activities/Tasks" :value="old(
                                                'appraisal_period_accomplishment.' . $loop->index . '.planned_activity',
                                                $appraisal_period_accomplishment['planned_activity'] ?? '',
                                            )" />

                                        <x-forms.text-area
                                            name="appraisal_period_accomplishment[{{ $loop->index }}][output_results]"
                                            id="output_results_{{ $loop->index }}" label="Outputs/Results"
                                            :value="old(
                                                'appraisal_period_accomplishment.' . $loop->index . '.output_results',
                                                $appraisal_period_accomplishment['output_results'] ?? '',
                                            )" />

                                        <x-forms.text-area
                                            name="appraisal_period_accomplishment[{{ $loop->index }}][remarks]"
                                            id="remarks_{{ $loop->index }}" label="Remarks" :value="old(
                                                'appraisal_period_accomplishment.' . $loop->index . '.remarks',
                                                $appraisal_period_accomplishment['remarks'] ?? '',
                                            )" />
                                    </div>
                                </div>
                            @endforeach


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
                                value="{{ $appraisal->job_compatibility ?? '' }}" id="job_compatibility"
                                :options="['yes' => 'Yes', 'no' => 'No']" :selected="$appraisal->job_compatibility ?? ''" />
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
                                @foreach ($appraisal->appraisal_period_rate as $appraisal_period_rate)
                                    <div class="mt-2 row g-3 repeater-rate-item">
                                        <div class="col-md-12">
                                            <x-forms.text-area
                                                name="appraisal_period_rate[{{ $loop->index }}][planned_activity]"
                                                id="appraisal_period_rate_planned_activity_{{ $loop->index }}"
                                                label="Planned Activities/Tasks"
                                                placeholder="Enter Planned Activities" :value="old(
                                                    'appraisal_period_rate.' . $loop->index . '.planned_activity',
                                                    $appraisal_period_rate['planned_activity'] ?? '',
                                                )" />

                                            <x-forms.text-area
                                                name="appraisal_period_rate[{{ $loop->index }}][output_results]"
                                                id="appraisal_period_rate_output_results_{{ $loop->index }}"
                                                label="Outputs/Results" placeholder="Enter Outputs"
                                                :value="old(
                                                    'appraisal_period_rate.' . $loop->index . '.output_results',
                                                    $appraisal_period_rate['output_results'] ?? '',
                                                )" />

                                            <x-forms.input
                                                name="appraisal_period_rate[{{ $loop->index }}][supervisee_score]"
                                                id="appraisal_period_rate_supervisee_score_{{ $loop->index }}"
                                                label="Supervisee's Score (out of 5)" type="number"
                                                placeholder="Supervisee Score" :value="old(
                                                    'appraisal_period_rate.' . $loop->index . '.supervisee_score',
                                                    $appraisal_period_rate['supervisee_score'] ?? '',
                                                )" />

                                            <x-forms.input
                                                name="appraisal_period_rate[{{ $loop->index }}][superviser_score]"
                                                id="appraisal_period_rate_superviser_score_{{ $loop->index }}"
                                                label="Supervisor's Score (out of 5)" type="number"
                                                placeholder="Supervisor Score" :value="old(
                                                    'appraisal_period_rate.' . $loop->index . '.superviser_score',
                                                    $appraisal_period_rate['superviser_score'] ?? '',
                                                )" />
                                        </div>
                                    </div>
                                @endforeach

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
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['appraisee_score'] ?? '' }}"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['appraiser_score'] ?? '' }}"
                                            max="5"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['agreed_score'] }}"
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
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[commitment][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['commitment']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5" @if (!$appraisal->is_appraisee) disabled @endif>
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[commitment][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['commitment']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[commitment][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['commitment']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

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
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[team_work][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['team_work']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[team_work][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['team_work']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[team_work][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['team_work']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Productivity and Organizational Skills</span>
                                        <p class="mb-0 text-muted small">Makes efficient use of time, fulfilling
                                            responsibilities and completing tasks by deadlines. Demonstrates
                                            responsiveness
                                            and structured approach to tasks.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[productivity][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['productivity']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[productivity][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['productivity']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[productivity][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['productivity']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Integrity</span>
                                        <p class="mb-0 text-muted small">Is honest and trustworthy, follows procedures,
                                            takes responsibility, and respects others. Deals with conflict
                                            professionally
                                            and values diversity.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[integrity][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['integrity']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[integrity][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['integrity']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[integrity][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['integrity']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Flexibility and Adaptability</span>
                                        <p class="mb-0 text-muted small">Willing to take on new job responsibilities or
                                            to
                                            assist the Organization through peak workloads. Able to accept the changing
                                            needs of the organization with enthusiasm.</p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[flexibility][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['flexibility']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[flexibility][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['flexibility']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[flexibility][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['flexibility']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

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
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[attendance][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['attendance']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[attendance][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['attendance']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[attendance][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['attendance']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Professional Appearance</span>
                                        <p class="mb-0 text-muted small">Maintains professional appearance, always
                                            neat,
                                            presentable, descent and keeps the work space in an orderly, clean and
                                            professional manner. </p>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[appearance][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['appearance']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[appearance][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['appearance']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[appearance][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['appearance']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

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
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[interpersonal][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['interpersonal']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[interpersonal][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['interpersonal']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[interpersonal][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['interpersonal']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

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
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[initiative][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['initiative']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[initiative][appraiser_score]"
                                            @if ($appraisal->is_appraisee) disabled @endif
                                            value="{{ $appraisal->personal_attributes_assessment['initiative']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[initiative][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['initiative']['agreed_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="5">
                                    </td>

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

                <div id="performance-planning-wrapper">
                    @foreach ($appraisal->performance_planning as $performance_planning)
                        <div class="row g-3 repeater-item">
                            <div class="col-md-6">
                                <x-forms.text-area name="performance_planning[{{ $loop->index }}][description]"
                                    label="Key Output Description" id="description"
                                    placeholder="Key Output Description" :value="old(
                                        'performance_planning[' . $loop->index . '][description]',
                                        $performance_planning['description'] ?? '',
                                    )" />
                            </div>

                            <div class="col-md-6">
                                <x-forms.text-area
                                    name="performance_planning[{{ $loop->index }}][performance_target]"
                                    label="Agreed Performance Targets" id="performance_target"
                                    placeholder="Agreed Performance Targets" :value="old(
                                        'performance_planning[' . $loop->index . '][performance_target]',
                                        $performance_planning['performance_target'] ?? '',
                                    )" />
                            </div>

                            <div class="col-md-6">
                                <x-forms.input name="performance_planning[{{ $loop->index }}][target_date]"
                                    label="Target Date" type="date" id="target_date" placeholder="Target Date"
                                    value="{{ old('performance_planning[' . $loop->index . '][target_date]', $performance_planning['target_date'] ?? '') }}" />
                            </div>
                        </div>
                    @endforeach

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
                            label="i. Strengths - Summarize employee's strengths..." id="employee_strength"
                            :value="old('employee_strength', $appraisal->employee_strength ?? '')" :isDisabled="$appraisal->is_appraisee" />

                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="employee_improvement"
                            label="ii.	Areas for Improvement - Summarize employee’s areas for improvement"
                            id="employee_improvement" :value="old('employee_improvement', $appraisal->employee_improvement ?? '')" :isDisabled="$appraisal->is_appraisee" />
                    </div>


                    <div class="col-md-12">
                        <x-forms.text-area name="superviser_overall_assessment"
                            label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                            id="superviser_overall_assessment" :value="old(
                                'superviser_overall_assessment',
                                $appraisal->superviser_overall_assessment ?? '',
                            )" :isDisabled="$appraisal->is_appraisee" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="recommendations"
                            label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                            id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" :isDisabled="$appraisal->is_appraisee" />
                    </div>
                </div>
            </fieldset>

            <!-- EVALUATION BY REVIEW PANEL   -->
            <fieldset class="p-2 mb-4 border">
                <legend class="w-auto">EVALUATION BY REVIEW PANEL</legend>

                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="panel_comment" label="(a)	Comments of the Panel." id="panel_comment"
                            :value="old('panel_comment', $appraisal->panel_comment ?? '')" :isDisabled="$appraisal->is_appraisee" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="panel_recommendation" label="(b)	Recommendation of the Panel"
                            id="panel_recommendation" :value="old('panel_recommendation', $appraisal->panel_recommendation ?? '')" :isDisabled="$appraisal->is_appraisee" />
                    </div>


                    <div class="col-md-12">
                        <x-forms.text-area name="overall_assessment"
                            label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                            id="overall_assessment" :value="old('overall_assessment', $appraisal->overall_assessment ?? '')" :isDisabled="$appraisal->is_appraisee" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="recommendations"
                            label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                            id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" :isDisabled="$appraisal->is_appraisee" />
                    </div>
                </div>
            </fieldset>

            {{-- OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY --}}
            <fieldset class="p-2 mb-4 border">
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="overall_assessment_and_comments"
                            label="OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY"
                            id="overall_assessment_and_comments" :value="old(
                                'overall_assessment_and_comments',
                                $appraisal->overall_assessment_and_comments ?? '',
                            )" :isDisabled="$appraisal->is_appraisee" />
                    </div>
                </div>
            </fieldset>

            <!-- SUBMIT SECTION -->
            <div class="py-4 bg-white sticky-bottom border-top">
                <div class="container-lg">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-text">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Thank you for filling the appraisal
                        </div>
                        <div class="gap-3 d-flex">
                            <button type="reset" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-lg btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Update Review
                            </button>
                        </div>
                        @can('approve appraisal')
                            @if (!is_null($appraisal->employee->user_id))
                                <div class="status m-2">
                                    @php $userRole = Auth::user()->roles->pluck('name')[0]; @endphp

                                    {{-- Current user's decision --}}
                                    @if (isset($appraisal->appraisal_request_status[$userRole]) &&
                                            $appraisal->appraisal_request_status[$userRole] === 'approved')
                                        <span class="badge bg-success">You Approved this Leave Request.</span>
                                    @elseif (isset($appraisal->appraisal_request_status[$userRole]) &&
                                            $appraisal->appraisal_request_status[$userRole] === 'rejected')
                                        <span class="badge bg-danger">You Rejected this Request</span>
                                        <p class="mt-1"><strong>Rejection Reason:</strong>
                                            {{ $appraisal->rejection_reason }}</p>
                                    @elseif ($appraisal->approval_status === 'approved')
                                        <span class="badge bg-danger">Approved</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif

                                    {{-- Show all who approved --}}
                                    @php
                                        $approvedBy = collect($appraisal->appraisal_request_status)
                                            ->filter(fn($status) => $status === 'approved')
                                            ->keys();

                                        $rejectedBy = collect($appraisal->appraisal_request_status)
                                            ->filter(fn($status) => $status === 'rejected')
                                            ->keys();
                                    @endphp

                                    @if ($approvedBy->isNotEmpty())
                                        <p class="mt-2"><strong>Approved by:</strong> {{ $approvedBy->join(', ') }}</p>
                                    @endif

                                    @if ($rejectedBy->isNotEmpty())
                                        <p class="mt-2"><strong>Rejected by:</strong> {{ $rejectedBy->join(', ') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <input class="btn btn-outline-primary btn-large approve-btn" value="Approve"
                                        type="button" data-appraisal-id="{{ $appraisal->appraisal_id }}">
                                    <input class="btn btn-outline-danger btn-large reject-btn" value="Reject"
                                        type="button" data-appraisal-id="{{ $appraisal->appraisal_id }}"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal">
                                </div>
                            @endif
                        @endcan


                    </div>
                </div>
            </div>
    </form>

    <!-- Bootstrap Modal for Rejection Reason -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Appraisal Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="rejectionReason">Please enter the reason for rejection:</label>
                    <textarea id="rejectionReason" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                </div>
            </div>
        </div>
    </div>


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
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" media="all">

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

                let planningIndex = @json(count($appraisal->performance_planning));

                $('#add-performance-row').click(function() {
                    let newRow = `
<div class="mt-2 row g-3 repeater-item">
<div class="col-md-6">
<x-forms.input name="performance_planning[${planningIndex}][description]" id="performance_planning[${planningIndex}][description]" label="Key Output Description"
type="text" placeholder="Key Output Description" />
</div>
<div class="col-md-6">
<x-forms.input name="performance_planning[${planningIndex}][performance_target]" id="performance_planning[${planningIndex}][performance_target]" label="Agreed Performance Targets"
type="text" placeholder="Agreed Performance Targets" />
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

                //appraisal approval wire:
                let currentAppraisalId;

                $('.approve-btn').click(function() {
                    const currentAppraisalId = $(this).data('appraisal-id');
                    updateLeaveStatus(currentAppraisalId, 'approved');
                });

                $('.reject-btn').click(function() {
                    currentAppraisalId = $(this).data('appraisal-id');
                    console.log('Training Id:', currentAppraisalId);
                });

                $('#confirmReject').click(function() {
                    const reason = $('#rejectionReason').val();
                    if (reason) {
                        updateLeaveStatus(currentAppraisalId, 'rejected', reason);
                        $('#rejectModal').modal('hide'); // Hide the modal
                    } else {
                        Toastify({
                            text: 'Please enter a rejection reason.',
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                        }).showToast();
                    }
                });

                function updateLeaveStatus(appraisalId, status, reason = null) {
                    $.ajax({
                        url: `/appraisals/${appraisalId}/status`,
                        type: 'POST',
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: JSON.stringify({
                            status: status,
                            reason: reason
                        }),
                        success: function(data) {
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                            }).showToast();

                            // Update status badge
                            const statusContainer = $('.status');

                            // Clear existing status badges
                            statusContainer.empty();

                            if (status === 'approved') {
                                statusContainer.append(
                                    '<span class="badge bg-success">You Approved this request</span>');
                            } else if (status === 'rejected') {
                                statusContainer.append(
                                    '<span class="badge bg-danger">You Rejected this request</span>');
                                if (reason) {
                                    statusContainer.append(
                                        '<p class="mt-1"><strong>Rejection Reason:</strong>' + reason +
                                        '</p>');
                                }
                            } else {
                                statusContainer.append('<span class="badge bg-warning">Pending</span>');
                            }

                            // Optionally, you could disable the approve/reject buttons once the status is updated
                            $('.approve-btn').prop('disabled', true);
                            $('.reject-btn').prop('disabled', true);
                        },
                        error: function(xhr) {
                            Toastify({
                                text: xhr.responseJSON?.error || 'An error occurred',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                            }).showToast();
                        }
                    });
                }

            });
        </script>

    @endpush
</x-app-layout>
