<x-app-layout>
    @php
        $rejectedEntry = collect($appraisal->appraisal_request_status)
            ->filter(fn($status) => $status === 'rejected')
            ->keys()
            ->first(); // Get the first person/role who rejected

        $rejectionReason = $appraisal->rejection_reason ?? 'No reason provided.';
    @endphp
    <div class="gap-2 p-2 bg-white border rounded shadow position-fixed top-50 end-0 translate-middle-y d-flex align-items-center border-primary no-print"
        style="z-index: 9999; cursor: pointer;" role="button" onclick="window.print();" title="Print this page">

        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="blue" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="bi bi-printer">
            <path d="M6 9V2h12v7" />
            <path d="M6 18h12a2 2 0 002-2v-5H4v5a2 2 0 002 2zm0 0v2h12v-2" />
        </svg>

    </div>

    <div class="top-0 p-3 toast-container position-fixed start-50 translate-middle-x text-bg-danger approval"
        role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-x-octagon-fill text-danger" viewBox="0 0 16 16">
                <path
                    d="M11.46.146A.5.5 0 0 1 12 .5v3.793a.5.5 0 0 1-.146.354l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 0 1 0-.708l7-7A.5.5 0 0 1 8.207.146L11.46.146z" />
            </svg>
            <strong class="me-auto" id="totalScore"></strong>
            <button type="button" class="btn-close no-print" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>

    </div>


    @if ($rejectedEntry)
        <!-- Toast for rejection -->
        <div class="top-0 p-3 toast-container position-fixed start-50 translate-middle-x text-bg-danger approval"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-x-octagon-fill text-danger" viewBox="0 0 16 16">
                    <path
                        d="M11.46.146A.5.5 0 0 1 12 .5v3.793a.5.5 0 0 1-.146.354l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 0 1 0-.708l7-7A.5.5 0 0 1 8.207.146L11.46.146z" />
                </svg>
                <strong class="me-auto">Rejected by {{ $rejectedEntry }}</strong>
                <button type="button" class="btn-close no-print" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <strong>Reason:</strong> {{ $rejectionReason }}
            </div>
        </div>
    @endif


    <form action="{{ route('appraisals.update', $appraisal->appraisal_id) }}" method="post" class="m-2">
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
                                        <option value="{{ optional($user->employee)->employee_id }}"
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
                <h4 class="mb-0">SECTION 1</h4>
                <small class="fw-light">(To be completed by staff under review)</small>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12">

                        <div class="mt-4 col-12">
                            <p class="fw-bold">a. Job Compatibility</p>
                            <x-forms.radio name="job_compatibility" :isDisabled="!$appraisal->is_appraisee"
                                label="Is the job and tasks performed compatible with your qualifications and experience?"
                                value="{{ $appraisal->job_compatibility ?? '' }}" id="job_compatibility"
                                :options="['yes' => 'Yes', 'no' => 'No']" :selected="$appraisal->job_compatibility ?? ''" />
                            <x-forms.text-area name="if_no_job_compatibility" :isDisabled="!$appraisal->is_appraisee"
                                label="If No, explain:" id="if_no_job_compatibility" :value="old('if_no_job_compatibility', $appraisal->if_no_job_compatibility ?? '')" />
                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">b. Challenges</p>
                            <x-forms.text-area name="unanticipated_constraints" :isDisabled="!$appraisal->is_appraisee"
                                label="Briefly state unanticipated constraints/problems that you encountered and how they affected the achievements of the objectives."
                                id="unanticipated_constraints" :value="old(
                                    'unanticipated_constraints',
                                    $appraisal->unanticipated_constraints ?? '',
                                )" />
                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">c. Personal Initiatives</p>
                            <x-forms.text-area name="personal_initiatives" :isDisabled="!$appraisal->is_appraisee"
                                label="Outline personal initiatives and any other factors that you think contributed to your achievements and successes."
                                id="personal_initiatives" :value="old('personal_initiatives', $appraisal->personal_initiatives ?? '')" />
                        </div>

                        <div class="mt-4 col-12">
                            <p class="fw-bold">d. Training Support Needs</p>
                            <x-forms.text-area name="training_support_needs" :isDisabled="!$appraisal->is_appraisee"
                                label="Indicate the nature of training support you may need to effectively perform your duties. Training support should be consistent with the job requirements and applicable to UNCST policies and regulations."
                                id="training_support_needs" :value="old('training_support_needs', $appraisal->training_support_needs ?? '')" />
                        </div>


                    </div>
                </div>
            </div>
        </div>


        <!-- PERSONAL ATTRIBUTES SECTION -->
        <div class="mb-4 shadow card">
            <div class="text-white card-header bg-primary">
                <h4 class="mb-0">SECTION 2</h4>
                <small class="fw-light">(To be completed by Staff Under Review (Appraisee) and Supervisor
                    (Appraiser))</small>
                <p>The Appraiser should take into consideration the Appraisee’s job description and the actual
                    activities
                    performed and outputs produced, constraints encountered as described by the Appraisee in Section
                    1, as
                    well as any other relevant information</p>
            </div>
            <div class="card-body">
                <div class="mt-4 col-12">
                    <p class="fw-bold">KEY DUTIES AND RESPONSIBILITIES</p>
                    <p>List and rate the major planned activities during the appraisal period, including
                        outputs/results attained.
                        You may include activities outside your job description but falling in line with your
                        duties.
                    </p>
                    <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
                        <table class="min-w-full text-base border border-gray-200">

                            <thead class="text-sm text-gray-700 uppercase bg-blue-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 border border-gray-200 w-12">No.</th>
                                    <th class="px-6 py-3 border border-gray-200">Planned Tasks (Target)</th>
                                    <th class="px-6 py-3 border border-gray-200">Output / Results</th>
                                    <th class="px-6 py-3 border border-gray-200 w-32">Supervisee (/6)</th>
                                    <th class="px-6 py-3 border border-gray-200 w-32">Supervisor (/6)</th>
                                    <th class="px-6 py-3 border border-gray-200 w-40">Average %</th>
                                </tr>
                            </thead>

                            <tbody>
                                @for ($i = 1; $i <= 4; $i++)
                                    @php
                                        $plannedActivity =
                                            $appraisal->appraisal_period_rate[$i - 1]['planned_activity'] ?? '';
                                        $outputResults =
                                            $appraisal->appraisal_period_rate[$i - 1]['output_results'] ?? '';
                                        $superviseeScore =
                                            $appraisal->appraisal_period_rate[$i - 1]['supervisee_score'] ?? 0;
                                        $supervisorScore =
                                            $appraisal->appraisal_period_rate[$i - 1]['supervisor_score'] ?? 0;
                                        $supervisorComment =
                                            $appraisal->appraisal_period_rate[$i - 1]['supervisor_comment'] ?? '';
                                    @endphp

                                    <tr class="hover:bg-blue-50 transition-colors" data-row="{{ $i }}">
                                        <td class="px-6 py-4 border border-gray-200 font-bold text-gray-900 align-top"
                                            rowspan="2">
                                            {{ $i }}.
                                        </td>

                                        {{-- Planned Activity --}}
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="editable-cell p-2 rounded {{ empty($plannedActivity) ? 'text-muted' : '' }}"
                                                contenteditable="{{ $appraisal->is_appraisee ? 'true' : 'false' }}"
                                                data-placeholder="Enter task" oninput="updateHiddenInput(this)"
                                                @unless ($appraisal->is_appraisee)
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top"
                                                    title="Editing is disabled for your role"
                                                    onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                @endunless
                                                oninput="updateHiddenInput(this)">
                                                {{ $plannedActivity ?: 'Enter task' }}
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[{{ $i - 1 }}][planned_activity]"
                                                value="{{ $plannedActivity }}">
                                        </td>

                                        {{-- Output Results --}}
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="editable-cell p-2 rounded {{ empty($outputResults) ? 'text-muted' : '' }}"
                                                contenteditable="{{ $appraisal->is_appraisee ? 'true' : 'false' }}"
                                                data-placeholder="Enter result" oninput="updateHiddenInput(this)"
                                                @unless ($appraisal->is_appraisee)
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top"
                                                    title="Editing is disabled for your role"
                                                    onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                @endunless>
                                                {{ $outputResults ?: 'Enter result' }}
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[{{ $i - 1 }}][output_results]"
                                                value="{{ $outputResults }}">
                                        </td>

                                        {{-- Supervisee Score --}}
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="score-cell"
                                                contenteditable="{{ $appraisal->is_appraisee ? 'true' : 'false' }}"
                                                data-type="score" oninput="updateScoreInput(this)"
                                                @unless ($appraisal->is_appraisee)
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top"
                                                    title="Editing is disabled for your role"
                                                    onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                @endunless>
                                                {{ $superviseeScore }}
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[{{ $i - 1 }}][supervisee_score]"
                                                value="{{ $superviseeScore }}">
                                        </td>

                                        {{-- Supervisor Score --}}
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="score-cell"
                                                contenteditable="{{ $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                data-type="score" oninput="updateScoreInput(this)"
                                                @unless ($appraisal->is_appraisor)
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top"
                                                    title="Editing is disabled for your role"
                                                    onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                @endunless>
                                                {{ $supervisorScore }}
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[{{ $i - 1 }}][supervisor_score]"
                                                value="{{ $supervisorScore }}">
                                        </td>

                                        <td class="px-6 py-2 border border-gray-200 bg-blue-50">
                                            <div class="percentage-display text-center font-medium">0%</div>
                                        </td>
                                    </tr>

                                    {{-- Supervisor Comment --}}
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-2 border border-gray-200 bg-gray-50 italic text-gray-600"
                                            colspan="5">
                                            <div class="editable-cell p-2 rounded {{ empty($supervisorComment) ? 'text-muted' : '' }}"
                                                contenteditable="{{ $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                data-placeholder="Supervisor's comment..."
                                                oninput="updateHiddenInput(this)"
                                                @unless ($appraisal->is_appraisor)
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top"
                                                    title="Editing is disabled for your role"
                                                    onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                @endunless>
                                                {{ $supervisorComment ?: "Supervisor's comment..." }}
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[{{ $i - 1 }}][supervisor_comment]"
                                                value="{{ $supervisorComment }}">
                                        </td>
                                    </tr>
                                @endfor

                                <!-- Total Row -->
                                <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 font-semibold">
                                    <td class="px-6 py-4 border border-gray-200" colspan="6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="text-lg font-bold text-blue-700">
                                                    Overall Average:
                                                    <span id="overallAverage" class="text-2xl ml-2">0</span>%
                                                </div>
                                                <div
                                                    class="relative w-48 h-3 bg-gray-200 rounded-full overflow-hidden">
                                                    <div id="averageProgress"
                                                        class="absolute left-0 top-0 h-full bg-gradient-to-r from-blue-400 to-indigo-600 transition-all duration-500"
                                                        style="width: 0%">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="performanceStatus" class="text-sm font-medium text-gray-600">
                                                Enter scores to view performance
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>


                        </table>
                    </div>

                </div>
                <div class="table-responsive">
                    <h6 class="h1">ASSESSMENT OF PERSONAL ATTRIBUTES</h6>
                    <p>The Appraisee should score her/his attributes in relation to performance.
                    </p>
                    <table class="table align-middle table-striped table-hover table-borderless table-primary"
                        id="personal-attributes">
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
                                <td class="text-center">4</td>
                                <td class="text-center"><input type="number"
                                        name="personal_attributes_assessment[technical_knowledge][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['appraisee_score'] ?? '' }}"
                                        max="4"></td>
                                <td class="text-center"><input type="number"
                                        name="personal_attributes_assessment[technical_knowledge][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['appraiser_score'] ?? '' }}"
                                        max="4"></td>
                                <td class="text-center"><input type="number"
                                        name="personal_attributes_assessment[technical_knowledge][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4"></td>
                            </tr>
                            <tr class="table-primary">
                                <td><span class="fw-bold">Commitment to Mission</span>
                                    <p class="mb-0 text-muted small">Understands and exhibits a sense of working
                                        for
                                        the UNCST & at all times projects the interest of the Organization as a
                                        priority.</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[commitment][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['commitment']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4" @if (!$appraisal->is_appraisee) readonly @endif>
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[commitment][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['commitment']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[commitment][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['commitment']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
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
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[team_work][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['team_work']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[team_work][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['team_work']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[team_work][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['team_work']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>

                            </tr>
                            <tr class="table-primary">
                                <td><span class="fw-bold">Productivity and Organizational Skills</span>
                                    <p class="mb-0 text-muted small">Makes efficient use of time, fulfilling
                                        responsibilities and completing tasks by deadlines. Demonstrates
                                        responsiveness
                                        and structured approach to tasks.</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[productivity][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['productivity']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[productivity][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['productivity']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[productivity][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['productivity']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>

                            </tr>
                            <tr class="table-primary">
                                <td><span class="fw-bold">Integrity</span>
                                    <p class="mb-0 text-muted small">Is honest and trustworthy, follows procedures,
                                        takes responsibility, and respects others. Deals with conflict
                                        professionally
                                        and values diversity.</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[integrity][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['integrity']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[integrity][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['integrity']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[integrity][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['integrity']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>

                            </tr>
                            <tr class="table-primary">
                                <td><span class="fw-bold">Flexibility and Adaptability</span>
                                    <p class="mb-0 text-muted small">Willing to take on new job responsibilities or
                                        to
                                        assist the Organization through peak workloads. Able to accept the changing
                                        needs of the organization with enthusiasm.</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[flexibility][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['flexibility']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[flexibility][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['flexibility']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[flexibility][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['flexibility']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
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
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[attendance][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['attendance']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[attendance][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['attendance']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[attendance][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['attendance']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>

                            </tr>
                            <tr class="table-primary">
                                <td><span class="fw-bold">Professional Appearance</span>
                                    <p class="mb-0 text-muted small">Maintains professional appearance, always
                                        neat,
                                        presentable, descent and keeps the work space in an orderly, clean and
                                        professional manner. </p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[appearance][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['appearance']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[appearance][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['appearance']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[appearance][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['appearance']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
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
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[interpersonal][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['interpersonal']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[interpersonal][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['interpersonal']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[interpersonal][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['interpersonal']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
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
                                <td class="text-center">4</td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[initiative][appraisee_score]"
                                        @if (!$appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['initiative']['appraisee_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[initiative][appraiser_score]"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        value="{{ $appraisal->personal_attributes_assessment['initiative']['appraiser_score'] ?? '' }}"
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                        name="personal_attributes_assessment[initiative][agreed_score]"
                                        value="{{ $appraisal->personal_attributes_assessment['initiative']['agreed_score'] ?? '' }}"
                                        @if ($appraisal->is_appraisee) readonly @endif
                                        class="form-control form-control-sm score-input" min="0"
                                        max="4">
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
                    <p><strong>Overall Score (max 40%):</strong> <span id="overall-40pct"></span></p>

                    <div id="performance-table-wrapper" class="table-responsive">
                        <h6 class="h1">PERFORMANCE PLANNING</h6>
                        <p>The Appraiser and Appraisee discuss and agree on the key outputs for the next performance
                            cycle.
                        </p>
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 text-center">No.</th>
                                    <th class="py-3">Key Output Description</th>
                                    <th class="py-3">Agreed Performance Targets</th>
                                    <th class="py-3">Target Dates</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($j = 0; $j < 8; $j++)
                                    <tr>
                                        <td class="text-center align-middle">{{ $j + 1 }}.</td>

                                        {{-- Key Output --}}
                                        <td>
                                            @php
                                                $keyOutput = $appraisal->performance_planning[$j]['description'] ?? '';
                                            @endphp
                                            <div class="editable-cell {{ empty($keyOutput) ? 'text-muted' : '' }}"
                                                contenteditable="{{ $appraisal->is_appraisee || $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                data-placeholder="Enter key output..."
                                                oninput="updateHiddenInput(this)">
                                                {{ $keyOutput ?: 'Enter key output...' }}</div>
                                            <input type="hidden"
                                                name="performance_planning[{{ $j }}][description]"
                                                value="{{ $keyOutput }}">
                                        </td>

                                        {{-- Performance Targets --}}
                                        <td>
                                            @php
                                                $target =
                                                    $appraisal->performance_planning[$j]['performance_target'] ?? '';
                                            @endphp
                                            <div class="editable-cell {{ empty($target) ? 'text-muted' : '' }}"
                                                contenteditable="{{ $appraisal->is_appraisee || $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                data-placeholder="Enter performance targets..."
                                                oninput="updateHiddenInput(this)">
                                                {{ $target ?: 'Enter performance target...' }}</div>
                                            <input type="hidden"
                                                name="performance_planning[{{ $j }}][performance_target]"
                                                value="{{ $target }}">
                                        </td>

                                        {{-- Target Date --}}
                                        <td>
                                            <input type="date" class="form-control form-control-sm date-input"
                                                name="performance_planning[{{ $j }}][target_date]"
                                                value="{{ $appraisal->performance_planning[$j]['target_date'] ?? '' }}">
                                        </td>
                                    </tr>
                                @endfor

                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>

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
                        :value="old('employee_strength', $appraisal->employee_strength ?? '')" :isDisabled="$appraisal->is_appraisor" />

                </div>

                <div class="col-md-12">
                    <x-forms.text-area name="employee_improvement"
                        label="ii.	Areas for Improvement - Summarize employee’s areas for improvement"
                        id="employee_improvement" :value="old('employee_improvement', $appraisal->employee_improvement ?? '')" :isDisabled="$appraisal->is_appraisor" />
                </div>


                <div class="col-md-12">
                    <x-forms.text-area name="superviser_overall_assessment"
                        label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                        id="superviser_overall_assessment" :value="old(
                            'superviser_overall_assessment',
                            $appraisal->superviser_overall_assessment ?? '',
                        )" :isDisabled="$appraisal->is_appraisor" />
                </div>

                <div class="col-md-12">
                    <x-forms.text-area name="recommendations"
                        label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                        id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" :isDisabled="$appraisal->is_appraisor" />
                </div>
            </div>
        </fieldset>

        <!-- EVALUATION BY REVIEW PANEL   -->
        <fieldset class="p-2 mb-4 border">
            <legend class="w-auto">SECTION 4</legend>
            <P>EVALUATION BY REVIEW PANEL (When deemed necessary by UNCST Management and
                with approval of the Executive Secretary)
            </P>
            <div class="mb-3 row">
                <div class="col-md-12">
                    <x-forms.text-area name="panel_comment" label="(a)	Comments of the Panel." id="panel_comment"
                        :value="old('panel_comment', $appraisal->panel_comment ?? '')" :isDisabled="!$appraisal->is_es" />
                </div>

                <div class="col-md-12">
                    <x-forms.text-area name="panel_recommendation" label="(b)	Recommendation of the Panel"
                        id="panel_recommendation" :value="old('panel_recommendation', $appraisal->panel_recommendation ?? '')" :isDisabled="!$appraisal->is_es" />
                </div>


                <div class="col-md-12">
                    <x-forms.text-area name="overall_assessment"
                        label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                        id="overall_assessment" :value="old('overall_assessment', $appraisal->overall_assessment ?? '')" :isDisabled="!$appraisal->is_es" />
                </div>

                <div class="col-md-12">
                    <x-forms.text-area name="recommendations"
                        label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                        id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" :isDisabled="!$appraisal->is_es" />
                </div>
            </div>
        </fieldset>

        {{-- OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY --}}
        <fieldset class="p-2 mb-4 border">
            <legend>SECTION 5</legend>
            <div class="mb-3 row">
                <div class="col-md-12">
                    <x-forms.text-area name="overall_assessment_and_comments"
                        label="OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY"
                        id="overall_assessment_and_comments" :value="old(
                            'overall_assessment_and_comments',
                            $appraisal->overall_assessment_and_comments ?? '',
                        )" :isDisabled="!$appraisal->is_es" />
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
                    @if ($appraisal->is_appraisee || $appraisal->is_appraisor)
                        <div class="gap-3 d-flex no-print">
                            <button type="reset" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>

                            <button type="submit" class="btn btn-lg btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Update Review
                            </button>
                        </div>
                    @endif
                    @can('approve appraisal')
                        @if (!is_null($appraisal->employee->user_id))
                            @php
                                $userRole = Auth::user()->roles->pluck('name')[0];
                                $roleNames = [
                                    'Head of Division' => 'Head of Division',
                                    'HR' => 'HR',
                                    'Executive Secretary' => 'Executive Secretary',
                                ];
                                $currentApprover = $appraisal->current_approver ?? 'Executive Secretary';
                                $previousApprover = 'None';
                                $isHR = $appraisal->appraiser_id == auth()->user()->employee->employee_id;

                                if ($userRole == 'HR' && !$isHR) {
                                    $previousApprover = 'Head of Division';
                                }

                                if ($userRole == 'Executive Secretary') {
                                    $previousApprover = 'HR';
                                }
                                $hasBeenRejected = collect($appraisal->appraisal_request_status)->contains(
                                    fn($status) => $status === 'rejected',
                                );

                                $approvedBy = collect($appraisal->appraisal_request_status)
                                    ->filter(fn($status) => $status === 'approved')
                                    ->keys();
                                $rejectedBy = collect($appraisal->appraisal_request_status)
                                    ->filter(fn($status) => $status === 'rejected')
                                    ->keys();
                            @endphp

                            <div class="m-2 status">

                                {{-- Current User's Decision --}}
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

                                {{-- Approved By List --}}
                                @if ($approvedBy->isNotEmpty())
                                    <p class="mt-2"><strong>Approved by:</strong> {{ $approvedBy->join(', ') }}</p>
                                @endif

                                {{-- Rejected By List --}}
                                @if ($rejectedBy->isNotEmpty())
                                    <p class="mt-2"><strong>Rejected by:</strong> {{ $rejectedBy->join(', ') }}</p>
                                @endif
                            </div>

                            {{-- Approval / Rejection Controls --}}
                            @if ($userRole == $currentApprover || $isHR)
                                @if ($previousApprover != 'None' && $appraisal->appraisal_request_status[$previousApprover] == 'approved')
                                    <div class="form-group no-print mt-3 d-flex gap-2">
                                        <input class="btn btn-outline-primary btn-large approve-btn" type="button"
                                            value="Approve" data-appraisal-id="{{ $appraisal->appraisal_id }}">
                                        <input class="btn btn-outline-danger btn-large reject-btn" type="button"
                                            value="Reject" data-appraisal-id="{{ $appraisal->appraisal_id }}"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    </div>
                                @elseif ($previousApprover != 'None' && $appraisal->appraisal_request_status[$previousApprover] == 'rejected')
                                    <p>Rejected by the {{ $previousApprover }}</p>
                                @else
                                    <div class="form-group no-print mt-3 d-flex gap-2">
                                        <input class="btn btn-outline-primary btn-large approve-btn" type="button"
                                            value="Approve" data-appraisal-id="{{ $appraisal->appraisal_id }}">
                                        <input class="btn btn-outline-danger btn-large reject-btn" type="button"
                                            value="Reject" data-appraisal-id="{{ $appraisal->appraisal_id }}"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    </div>
                                @endif
                            @else
                                <p class="mt-3">
                                    Waiting for approval from:
                                    <strong>{{ $roleNames[$currentApprover] ?? $currentApprover }}</strong>
                                </p>
                            @endif
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

        @media print {

            /* Remove Bootstrap container padding if needed */
            .container,
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            /* Ensure background colors show (some browsers strip them) */
            .bg-primary,
            .bg-secondary,
            .bg-info,
            .bg-danger,
            .bg-success,
            .bg-warning {
                -webkit-print-color-adjust: exact !important;
                /* Chrome/Safari */
                print-color-adjust: exact !important;
                color: white !important;
            }

            /* Ensure text colors are preserved */
            .text-white {
                color: white !important;
            }

            .text-primary,
            .text-danger,
            .text-muted {
                print-color-adjust: exact !important;
            }

            /* Hide elements with Bootstrap display utilities */
            .no-print,
            .d-none,
            .d-print-none {
                display: none !important;
            }

            /* Show hidden print-only elements */
            .d-print-block {
                display: block !important;
            }

            .d-print-inline {
                display: inline !important;
            }

            .d-print-inline-block {
                display: inline-block !important;
            }

            /* Force table styles */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 5px !important;
            }

            /* Remove box shadows and rounded corners */
            .shadow,
            .card,
            .rounded,
            .border {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
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
            width: 80px;
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

        .editable-cell {
            min-height: 10vh;
            line-height: 1.5;
            font-family: Verdana, Geneva, Tahoma, sans-serif
        }

        .editable-cell[data-placeholder]:empty::before {
            content: attr(data-placeholder);
            color: #9ca3af;
        }

        .score-cell {
            min-height: 2rem;
            min-width: 3rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            text-align: center;
            transition: all 0.2s ease;
            background-color: #f8fafc;
            font-weight: 600;
            color: #1e40af;
        }

        .score-cell:focus {
            background-color: #e0f2fe;
            outline: none;
        }

        .percentage-display {
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            transition: color 0.3s ease;
        }

        #averageProgress {
            transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.7s ease;
        }

        #performance-table-wrapper .date-input {
            border: none;
            width: 100%;
            padding: 1rem;
            background-color: transparent;
            font-size: 1rem;
            color: #212529;
        }

        #performance-table-wrapper .date-input:focus {
            outline: none;
            background-color: #f8f9fa;
        }
    </style>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize all components
                initScoreValidation();
                initSelect2();
                initAppraisalApproval();
                initActivityRatingTable();
                initPersonalAttributesTable();
                initPerformanceTableAutoSave();

                // 1. Score Input Validation
                function initScoreValidation() {
                    function clampScore($input, max = 4, min = 0) {
                        let val = parseFloat($input.val());
                        if (val > max) $input.val(max);
                        else if (val < min) $input.val(min);
                    }

                    $('.score-input, .score-input-component').on('input', function() {
                        clampScore($(this));
                    });
                }

                // 2. Select2 Initialization
                function initSelect2() {
                    $('.employees').select2({
                        theme: "bootstrap-5",
                        placeholder: $(this).data('placeholder'),
                        dropdownParent: $('.appraisal-information')
                    });
                }
                // Expose this function globally
                window.updateHiddenInput = function(element) {
                    const text = element.textContent.trim();
                    const placeholder = element.dataset.placeholder || 'Enter text';
                    const hiddenInput = element.nextElementSibling;

                    // If the field is empty, reapply placeholder and muted style
                    if (!text) {
                        element.textContent = placeholder;
                        element.classList.add('text-muted');
                        if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                            hiddenInput.value = '';
                        }
                    } else {
                        // If user types, remove muted style and update input
                        if (element.classList.contains('text-muted')) {
                            element.classList.remove('text-muted');
                        }
                        if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                            hiddenInput.value = text;
                        }
                    }
                };

                document.querySelectorAll('.editable-cell').forEach(cell => {
                    const placeholder = cell.dataset.placeholder;
                    const text = cell.textContent.trim();

                    // If empty, show placeholder
                    if (!text && placeholder) {
                        cell.textContent = placeholder;
                        cell.classList.add('text-muted');
                    }

                    cell.addEventListener('focus', () => {
                        if (cell.classList.contains('text-muted')) {
                            cell.textContent = '';
                            cell.classList.remove('text-muted');
                        }
                    });

                    cell.addEventListener('blur', () => {
                        if (!cell.textContent.trim()) {
                            cell.textContent = placeholder;
                            cell.classList.add('text-muted');
                        }
                    });
                });


                // Expose this function globally
                window.updateScoreInput = function(scoreCell) {
                    const value = Math.min(Math.max(parseInt(scoreCell.textContent) || 0, 0), 6);
                    scoreCell.textContent = value;
                    const hiddenInput = scoreCell.nextElementSibling;
                    if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                        hiddenInput.value = value;
                    }
                }

                // 3. Appraisal Approval Logic
                function initAppraisalApproval() {
                    let currentAppraisalId;

                    $('.approve-btn').click(function() {
                        currentAppraisalId = $(this).data('appraisal-id');
                        approveAppraisal(currentAppraisalId, 'approved');
                    });

                    $('.reject-btn').click(function() {
                        currentAppraisalId = $(this).data('appraisal-id');
                    });

                    $('#confirmReject').click(function() {
                        const reason = $('#rejectionReason').val();
                        if (reason) {
                            approveAppraisal(currentAppraisalId, 'rejected', reason);
                            $('#rejectModal').modal('hide');
                        } else {
                            showToast('Please enter a rejection reason.');
                        }
                    });

                    function approveAppraisal(appraisalId, status, reason = null) {
                        $.ajax({
                            url: `/appraisals/${appraisalId}/status`,
                            type: 'POST',
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: JSON.stringify({
                                status,
                                reason
                            }),
                            success: function(data) {
                                handleApprovalSuccess(data, status, reason);
                                $('.approve-btn, .reject-btn').prop('disabled', true);
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.error || 'An error occurred');
                            }
                        });
                    }

                    function handleApprovalSuccess(data, status, reason) {
                        showToast(data.message);
                        const $statusContainer = $('.status').empty();

                        if (status === 'approved') {
                            $statusContainer.append(`<span class="badge bg-success">You Approved this request</span>`);
                        } else if (status === 'rejected') {
                            $statusContainer.append(`<span class="badge bg-danger">You Rejected this request</span>`);
                            if (reason) {
                                $statusContainer.append(
                                    `<p class="mt-1"><strong>Rejection Reason:</strong> ${reason}</p>`);
                            }
                        }
                    }
                }

                // 4. Activity Rating Table Logic
                function initActivityRatingTable() {
                    const rows = document.querySelectorAll('tr[data-row]');
                    const overallAverageElement = document.getElementById('overallAverage');
                    const averageProgress = document.getElementById('averageProgress');
                    const performanceStatus = document.getElementById('performanceStatus');

                    const statusMessages = [{
                            threshold: 90,
                            message: 'Exceptional Performance',
                            color: 'from-green-400 to-emerald-600'
                        },
                        {
                            threshold: 75,
                            message: 'Exceeds Expectations',
                            color: 'from-blue-400 to-indigo-600'
                        },
                        {
                            threshold: 50,
                            message: 'Meets Expectations',
                            color: 'from-yellow-400 to-amber-600'
                        },
                        {
                            threshold: 25,
                            message: 'Needs Improvement',
                            color: 'from-orange-400 to-red-600'
                        },
                        {
                            threshold: 0,
                            message: 'Requires Immediate Attention',
                            color: 'from-red-400 to-rose-600'
                        }
                    ];

                    rows.forEach(row => {
                        row.querySelectorAll('.score-cell').forEach(cell => {
                            cell.addEventListener('input', handleScoreInput);
                            cell.addEventListener('focus', () => cell.classList.add('ring-2',
                                'ring-blue-200'));
                            cell.addEventListener('blur', () => cell.classList.remove('ring-2',
                                'ring-blue-200'));
                        });
                    });

                    function handleScoreInput(e) {
                        const cell = e.target;
                        let value = parseInt(cell.textContent);
                        cell.textContent = isNaN(value) ? '' : Math.min(Math.max(value, 0), 6);
                        updateRowPercentage(cell.closest('tr'));
                        updateOverallAverage();
                    }

                    function updateRowPercentage(row) {
                        const scores = Array.from(row.querySelectorAll('.score-cell')).map(cell => {
                            const val = parseInt(cell.textContent);
                            return isNaN(val) ? 0 : Math.min(Math.max(val, 0), 6);
                        });

                        const avg = calculateRowAverage(scores);
                        const percentageDisplay = row.querySelector('.percentage-display');
                        percentageDisplay.textContent = `${avg.toFixed(1)}%`;
                        percentageDisplay.style.color = avg >= 75 ? '#16a34a' : avg >= 50 ? '#2563eb' : avg >= 25 ?
                            '#ca8a04' : '#dc2626';
                        return avg;
                    }

                    function calculateRowAverage(scores) {
                        const validScores = scores.filter(score => !isNaN(score));
                        const sum = validScores.reduce((a, b) => a + b, 0);
                        return validScores.length > 0 ? (sum / (validScores.length * 6)) * 100 : 0;
                    }

                    function updateOverallAverage() {
                        const rowAverages = Array.from(rows).map(row => {
                            const val = parseFloat(row.querySelector('.percentage-display')?.textContent);
                            return isNaN(val) ? 0 : val;
                        });

                        const overallAvg = rowAverages.length > 0 ?
                            rowAverages.reduce((a, b) => a + b, 0) / rowAverages.length : 0;
                        const scaledAvg = (Math.min(overallAvg, 100) * 0.6);
                        overallAverageElement.textContent = scaledAvg.toFixed(1); // Show 0–60%
                        averageProgress.style.width = `${Math.min(overallAvg, 100)}%`; // Visual 0–100%



                        const status = statusMessages.find(s => overallAvg >= s.threshold);
                        performanceStatus.textContent = status.message;
                        averageProgress.className =
                            `absolute left-0 top-0 h-full bg-gradient-to-r ${status.color} transition-all duration-500`;

                        window.keyDutiesContribution = overallAvg * 0.6;
                        updateTotalScore();
                    }

                    // Initial calculations
                    rows.forEach(updateRowPercentage);
                    updateOverallAverage();
                }

                function initPersonalAttributesTable() {
                    const $table = $('#personal-attributes');

                    function recalc() {
                        let sumAppraisee = 0,
                            sumAppraiser = 0,
                            sumAgreed = 0,
                            rowCount = 0,
                            maxPerRow = 4;

                        $table.find('tbody tr').each(function() {
                            const $row = $(this);
                            // Get values directly from inputs
                            const agreed = parseFloat($row.find('input[name*="[agreed_score]"]').val()) || 0;

                            sumAgreed += agreed;
                            rowCount++;

                            // For display purposes only
                            const a = parseFloat($row.find('input[name*="[appraisee_score]"]').val()) || 0;
                            const b = parseFloat($row.find('input[name*="[appraiser_score]"]').val()) || 0;
                            sumAppraisee += a;
                            sumAppraiser += b;
                        });

                        const $tfootTds = $table.find('tfoot tr:first td');
                        $tfootTds.eq(0).text(sumAppraisee.toFixed(2)); // Appraisee total
                        $tfootTds.eq(1).text(sumAppraiser.toFixed(2)); // Appraiser total
                        $tfootTds.eq(2).text(sumAgreed.toFixed(2)); // Agreed total

                        const totalMax = rowCount * maxPerRow;
                        const pct40 = totalMax > 0 ? (sumAgreed / totalMax) * 40 : 0;
                        $('#overall-40pct').text(`${pct40.toFixed(2)}% of 40%`);

                        window.personalAttributesContribution = pct40;
                        updateTotalScore();
                    }

                    // Listen for changes on ALL score input types
                    $table.on('input change', '.score-input', recalc);
                    recalc(); // Initial calculation
                }

                function updateTotalScore() {
                    const total = (window.keyDutiesContribution || 0) +
                        (window.personalAttributesContribution || 0);
                    document.getElementById('totalScore').textContent = `${total.toFixed(1)}%`;
                }

                // 6. Performance Table Auto-Save
                function initPerformanceTableAutoSave() {
                    const wrapper = document.querySelector('#performance-table-wrapper');
                    if (!wrapper) return;

                    const today = new Date().toISOString().split('T')[0];
                    let saveTimeout;

                    wrapper.querySelectorAll('.date-input').forEach(input => {
                        input.setAttribute('min', today);
                    });

                    wrapper.querySelectorAll('[ contenteditable="true"]').forEach(cell => {
                        const placeholder = cell.getAttribute('data-placeholder');
                        if (!cell.textContent.trim()) {
                            cell.textContent = placeholder;
                            cell.classList.add('text-muted');
                        }

                        cell.addEventListener('input', autoSave);
                        cell.addEventListener('focus', handleCellFocus);
                        cell.addEventListener('blur', handleCellBlur);
                    });

                    wrapper.querySelectorAll('.date-input').forEach(input => {
                        input.addEventListener('change', autoSave);
                    });

                    loadSavedData();

                    function autoSave() {
                        clearTimeout(saveTimeout);
                        saveTimeout = setTimeout(() => {
                            const data = [];
                            wrapper.querySelectorAll('tbody tr').forEach(row => {
                                data.push({
                                    keyOutput: row.querySelector('.editable-cell:nth-child(2)')
                                        .textContent.trim(),
                                    targets: row.querySelector('.editable-cell:nth-child(3)')
                                        .textContent.trim(),
                                    date: row.querySelector('input.date-input').value
                                });
                            });
                            localStorage.setItem('performanceData', JSON.stringify(data));
                            showStatus('Auto-saved successfully!', 'success');
                        }, 1000);
                    }

                    function handleCellFocus(e) {
                        const cell = e.target;
                        const placeholder = cell.getAttribute('data-placeholder')?.trim() || '';

                        // Remove muted style
                        cell.classList.remove('text-muted');

                        // Clear placeholder if it matches
                        if (cell.textContent.trim() === placeholder) {
                            cell.textContent = '';
                        }

                        // Add editing class
                        cell.classList.add('editing');
                    }


                    function handleCellBlur(e) {
                        const cell = e.target;
                        cell.classList.remove('editing');
                        if (!cell.textContent.trim()) {
                            cell.textContent = cell.getAttribute('data-placeholder');
                            cell.classList.add('text-muted');
                        }
                    }

                    function loadSavedData() {
                        const savedData = localStorage.getItem('performanceData');
                        if (savedData) {
                            JSON.parse(savedData).forEach((rowData, index) => {
                                const row = wrapper.querySelector(`tbody tr:nth-child(${index + 1})`);
                                if (!row) return;

                                const keyOutput = row.querySelector('.editable-cell:nth-child(2)');
                                const targets = row.querySelector('.editable-cell:nth-child(3)');
                                const date = row.querySelector('input.date-input');

                                if (keyOutput) keyOutput.textContent = rowData.keyOutput;
                                if (targets) targets.textContent = rowData.targets;
                                if (date) date.value = rowData.date;
                            });
                        }
                    }
                }

                // Utility Functions
                function showToast(message) {
                    Toastify({
                        text: message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)"
                    }).showToast();
                }

                function showStatus(message, type = 'success') {
                    const status = document.createElement('div');
                    status.className = `status-message alert alert-${type}`;
                    status.textContent = message;
                    document.body.appendChild(status);

                    setTimeout(() => status.remove(), 2000);
                }
            });
        </script>
    @endpush
</x-app-layout>
