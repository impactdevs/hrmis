<x-app-layout>
    <section class="m-2 section dashboard">
        @if (auth()->user()->isAdminOrSecretary)
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- Employees Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card sales-card">

                                <div class="card-body">
                                    <h5 class="card-title">Employees</h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $number_of_employees }}</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Employees Card -->
                        <!-- Attendees Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card revenue-card">

                                <div class="card-body">
                                    <h5 class="card-title">Attendees</h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $attendances }}</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Attendees Card -->

                        <!-- Pending Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Appraisals</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-hourglass-split text-warning"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $pendingAppraisals }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Pending evaluations</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ongoing Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Ongoing Appraisals</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-arrow-repeat text-primary"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $ongoingAppraisals }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Awaiting final approval</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Complete Appraisals</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $completeAppraisals }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Complete evaluations</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Running Contracts</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $runningContracts }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Running Contracts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Expired Contracts</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $expiredContracts }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Expired Contracts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Leaves Card -->
                        <div class="col-xxl-3 col-xl-12">

                            <div class="card info-card customers-card">

                                <div class="card-body">
                                    <h5 class="card-title">Number of Employees on Leave <span>| Currently</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $available_leave }}</h6>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div><!-- End Leaves Card -->

                        <!-- Birthdays Card -->
                        <div class="col-xxl-3 col-xl-12">

                            <div class="card info-card customers-card">

                                <div class="card-body">
                                    <h5 class="card-title">Birthdays</h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ count($todayBirthdays) }}</h6>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div><!-- End Birthdays Card -->

                        <!-- Reports -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Clock Ins <span>/Today</span></h5>

                                    <!-- Line Chart -->
                                    <div id="reportsChart"></div>
                                    <!-- End Line Chart -->

                                </div>

                            </div>
                        </div><!-- End Reports -->

                        <!-- Applications -->
                        {{-- <div class="col-12">
                            <div class="overflow-auto card recent-sales">
                                <div class="card-body">
                                    <h5 class="card-title">Applications <span>| Today</span></h5>

                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">First Name</th>
                                                <th scope="col">Last Name</th>
                                                <th scope="col">Job</th>
                                            </tr>
                                        </thead>
                                        <tbody> --}}
                        {{-- @foreach ($entries as $entry)
                                                @php --}}
                        {{-- // Assuming $response contains the JSON string
                                                    $data = json_decode($entry->entry->responses, true); // true for associative array
                                                @endphp --}}
                        {{-- <tr>
                                                    <td><a href="#"
                                                            class="btn btn-outline-danger">{{ $entry->job->job_code }}</a>
                                                        </th>
                                                    <td>{{ $data[93] }}</td>
                                                    <td>{{ $data[94] }}</td>
                                                    <td><span
                                                            class="badge bg-success">{{ $entry->job->job_title }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div> --}}

                        <!-- Ongoing Appraisals -->
                        {{-- <div class="col-12">
                            <div class="overflow-auto card top-selling">

                                <div class="pb-0 card-body">
                                    <h5 class="card-title">On Going Appraisals</h5>

                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th scope="col">Full Name</th>
                                                <th scope="col">Position</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($appraisals as $appraisal)
                                                @php
                                                    // Assuming $response contains the JSON string
                                                    $data = json_decode($appraisal->entry->responses, true); // true for associative array
                                                @endphp
                                                <tr>
                                                    <td>{{ $appraisal->employee->first_name . ' ' . $appraisal->employee->first_name }}
                                                    </td>
                                                    <td>{{ $appraisal->employee->position->position_name }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div> --}}
                        <!-- End Ongoing Appraisals -->

                        {{-- <div class="col-12">
                            <div class="card">


                                <div id="svg-tree">

                                </div>
                            </div>
                        </div> --}}

                        <!-- Allocated Leave -->
                        <div class="col-12">
                            <div class="card">
                                <div class="pb-0 card-body">
                                    <h5 class="card-title">Allocated Leave Analysis <span>| This Month</span></h5>

                                    <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                                </div>
                            </div>
                        </div>
                        <!-- Allocated Leave -->

                    </div>
                </div><!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-4">
                    <!-- Employee Distribution -->
                    <div class="card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                    class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>

                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>

                        <div class="pb-0 card-body">
                            <h5 class="card-title">Employee Distribution</h5>

                            <div id="trafficChart" style="min-height: 400px;" class="echart"></div>


                        </div>
                    </div><!-- End Website Traffic -->

                    <!-- Events -->
                    <div class="card">
                        <div class="pb-0 card-body">
                            <h5 class="card-title">Events &amp; Trainings <span>| Ongoing, Due Today & Tomorrow</span>
                            </h5>

                            <div class="news">
                                @foreach ($events as $event)
                                    <div class="clearfix post-item">
                                        <img src="{{ 'assets/img/event.gif' }}" alt="">
                                        <h4><a
                                                href="{{ route('events.show', $event->event_id) }}">{{ $event->event_title }}</a>
                                        </h4>
                                        <p class="description">{{ $event->event_description }}</p>
                                        <p>
                                            @if (\Carbon\Carbon::parse($event->event_start_date)->isToday())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Today</span>
                                            @elseif (\Carbon\Carbon::parse($event->event_start_date)->isTomorrow())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Tomorrow</span>
                                            @elseif (
                                                \Carbon\Carbon::parse($event->event_start_date)->isPast() &&
                                                    \Carbon\Carbon::parse($event->event_end_date)->isFuture())
                                                <strong>Status:</strong> <span
                                                    class="badge text-bg-secondary">Ongoing</span>
                                            @endif
                                        </p>
                                    </div>
                                @endforeach

                                @foreach ($trainings as $training)
                                    <div class="clearfix post-item">
                                        <img src="{{ 'assets/img/training.gif' }}" alt="">
                                        <h4><a
                                                href="{{ route('trainings.show', $training->training_id) }}">{{ $training->training_title }}</a>
                                        </h4>
                                        <p class="description">{{ $training->training_description }}</p>
                                        <p>
                                            @if (\Carbon\Carbon::parse($training->training_start_date)->isToday())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Today</span>
                                            @elseif (\Carbon\Carbon::parse($training->training_start_date)->isTomorrow())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Tomorrow</span>
                                            @elseif (
                                                \Carbon\Carbon::parse($training->training_start_date)->isPast() &&
                                                    \Carbon\Carbon::parse($training->training_end_date)->isFuture())
                                                <strong>Status:</strong> <span
                                                    class="badge text-bg-secondary">Ongoing</span>
                                            @endif
                                        </p>
                                    </div>
                                @endforeach

                                {{-- if events and or trainings are empty, display a message --}}
                                @if (count($events) == 0 && count($trainings) == 0)
                                    <div class="text-center text-danger">
                                        <h4 class="title-danger">No events or trainings available</h4>
                                    </div>
                                @endif

                            </div><!-- End sidebar recent posts-->

                        </div>
                    </div><!-- End Events & Trainings -->




                </div><!-- End Right side columns -->

            </div>
        @elseif (auth()->user()->hasRole('Staff'))
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- Sales Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">

                                <div class="card-body">
                                    <h5 class="card-title">Annual Leave Days</h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ auth()->user()->employee->entitled_leave_days }}</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">

                                <div class="card-body">
                                    <h5 class="card-title">Leave Utilized <span>| This Year</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ auth()->user()->employee->totalLeaveDays() }}</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Revenue Card -->

                        <!-- Pending Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Pending Appraisals</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-hourglass-split text-warning"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $pendingAppraisals }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Pending evaluations</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ongoing Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Ongoing Appraisals</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-arrow-repeat text-primary"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $ongoingAppraisals }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Awaiting final approval</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Complete Appraisals</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $completeAppraisals }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Complete evaluations</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Running Contracts</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $runningContracts }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Running Contracts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Expired Contracts</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $expiredContracts }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Expired Contracts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- End Customers Card -->
                        @if (count($leaveApprovalData) > 0)
                            <!-- Leave Approval Progress -->
                            <div class="col-xxl-12 col-md-12">
                                <div class="border card info-card leave-approval-card border-5 border-primary">
                                    <div class="card-body">
                                        <h6 class="mb-2">Leave Requests</h6>

                                        @foreach ($leaveApprovalData as $leaveData)
                                            <div class="mb-3 leave-approval-item">
                                                @if ($leaveData['esStatus'] === 'Approved')
                                                    <!-- Hide progress area -->
                                                    <div class="congratulations-message">
                                                        <div class="balloons-css">
                                                            <div class="balloon"></div>
                                                            <div class="balloon"></div>
                                                            <div class="balloon"></div>
                                                            <div class="balloon"></div>
                                                        </div>
                                                        <p class="text-success fw-bold">🎉 Congratulations! 🎈 Your
                                                            Leave Request was approved!!</p>
                                                        @if ($leaveData['daysRemaining'] == 'Leave has not started')
                                                            <p>Leave has not started yet!!</p>
                                                        @else
                                                            <p class="text-success fw-bold">Days remaining to complete
                                                                leave:
                                                                {{ $leaveData['daysRemaining'] }}</p>
                                                        @endif
                                                    </div>
                                                @elseif ($leaveData['status'] === 'Pending' && isset($leaveData['rejection_reason']))
                                                    <div class="rejected-message">
                                                        <h6>Status: Rejected</h6>
                                                        <p>Reason: {{ $leaveData['rejection_reason'] }}</p>
                                                    </div>
                                                @else
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $leaveData['progress'] }}%;"
                                                            aria-valuenow="{{ $leaveData['progress'] }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                            {{ $leaveData['status'] }}
                                                        </div>
                                                    </div>
                                                    <small>HR Review: {{ $leaveData['hrStatus'] }}</small><br>
                                                    <small>HOD Review: {{ $leaveData['hodStatus'] }}</small><br>
                                                    <small>Executive Staff Review: {{ $leaveData['esStatus'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div><!-- End Leave Approval Progress Card -->
                        @endif
                        @foreach ($contracts as $contract)
                            @if ($contract->days_until_end >= 0 && $contract->days_until_end <= 90)
                                <div class="col-xxl-12 col-md-12">
                                    @php
                                        $bgClass =
                                            $contract->days_until_end === 0 ? 'text-bg-danger' : 'text-bg-warning';
                                        $icon =
                                            $contract->days_until_end === 0
                                                ? 'bi-exclamation-triangle-fill'
                                                : 'bi-hourglass-split';
                                        $title =
                                            $contract->days_until_end === 0
                                                ? 'Contract Expiring Today!'
                                                : 'Contract Expiry Notice';
                                        $message =
                                            $contract->days_until_end === 0
                                                ? 'Your contract is expiring <strong>today</strong>. Please take immediate action!'
                                                : "Your contract will expire in <strong>{$contract->days_until_end}</strong> days. Please plan accordingly.";
                                    @endphp

                                    <div class="bottom-0 p-3 position-fixed end-0" style="z-index: 9999;">
                                        <div class="toast show {{ $bgClass }} border-0 shadow-lg rounded-3"
                                            role="alert" aria-live="assertive" aria-atomic="true">
                                            <div class="bg-transparent border-0 toast-header">
                                                <i class="bi {{ $icon }} me-2 fs-5 text-white"></i>
                                                <strong class="text-white me-auto">{{ $title }}</strong>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="toast" aria-label="Close"></button>
                                            </div>
                                            <div class="text-white toast-body">
                                                <div class="mb-2">
                                                    <strong>Description:</strong> {{ $contract->description }}
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Start Date:</strong>
                                                    {{ $contract->start_date->format('d M Y') }}<br>
                                                    <strong>End Date:</strong>
                                                    {{ $contract->end_date->format('d M Y') }}
                                                </div>
                                                <div class="fw-semibold">
                                                    {!! $message !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach





                        <!-- Allocated Leave -->
                        <div class="col-12">
                            <div class="card">
                                <div class="pb-0 card-body">
                                    <h5 class="card-title">Allocated Leave Analysis <span>| This Month</span></h5>

                                    <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                                </div>
                            </div>
                        </div>
                        <!-- Allocated Leave -->


                    </div>
                </div>

                <!-- Right side columns -->
                <div class="col-lg-4">
                    <!-- Events -->
                    <div class="card">
                        <div class="pb-0 card-body">
                            <h5 class="card-title">Events &amp; Trainings <span>| Ongoing, Due Today & Tomorrow</span>
                            </h5>

                            <div class="news">
                                @foreach ($events as $event)
                                    <div class="clearfix post-item">
                                        <img src="{{ 'assets/img/event.gif' }}" alt="">
                                        <h4><a
                                                href="{{ route('events.show', $event->event_id) }}">{{ $event->event_title }}</a>
                                        </h4>
                                        <p class="description">{{ $event->event_description }}</p>
                                        <p>
                                            @if (\Carbon\Carbon::parse($event->event_start_date)->isToday())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Today</span>
                                            @elseif (\Carbon\Carbon::parse($event->event_start_date)->isTomorrow())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Tomorrow</span>
                                            @elseif (
                                                \Carbon\Carbon::parse($event->event_start_date)->isPast() &&
                                                    \Carbon\Carbon::parse($event->event_end_date)->isFuture())
                                                <strong>Status:</strong> <span
                                                    class="badge text-bg-secondary">Ongoing</span>
                                            @endif
                                        </p>
                                    </div>
                                @endforeach

                                @foreach ($trainings as $training)
                                    <div class="clearfix post-item">
                                        <img src="{{ 'assets/img/training.gif' }}" alt="">
                                        <h4><a
                                                href="{{ route('trainings.show', $training->training_id) }}">{{ $training->training_title }}</a>
                                        </h4>
                                        <p class="description">{{ $training->training_description }}</p>
                                        <p>
                                            @if (\Carbon\Carbon::parse($training->training_start_date)->isToday())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Today</span>
                                            @elseif (\Carbon\Carbon::parse($training->training_start_date)->isTomorrow())
                                                <strong>Status:</strong> <span class="badge text-bg-secondary">Due
                                                    Tomorrow</span>
                                            @elseif (
                                                \Carbon\Carbon::parse($training->training_start_date)->isPast() &&
                                                    \Carbon\Carbon::parse($training->training_end_date)->isFuture())
                                                <strong>Status:</strong> <span
                                                    class="badge text-bg-secondary">Ongoing</span>
                                            @endif
                                        </p>
                                    </div>
                                @endforeach

                            </div><!-- End sidebar recent posts-->

                        </div>
                    </div><!-- End Events & Trainings -->

                    {{-- Un read Notification Reminders --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-bell-fill text-primary"></i> Latest Notifications <span class="badge bg-primary text-light">{{ $notifications->count() }}</span>
                                </h5>
                                <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm text-decoration-none">View All</a>
                            </div>
                            <section class="section dashboard px-1">
                                <div class="row">
                                    <div class="col-12">
                                        @if ($notifications->isNotEmpty())
                                            <ul class="list-group list-group-flush">
                                                @foreach ($notifications as $notification)
                                                    @php
                                                        $url = '';
                                                        if (isset($notification->data['leave_id'])) {
                                                            $url = url('leaves', $notification->data['leave_id']);
                                                        }
                                                        if (isset($notification->data['training_id'])) {
                                                            $url = url('trainings', $notification->data['training_id']);
                                                        }
                                                        if (isset($notification->data['event_id'])) {
                                                            $url = url('events', $notification->data['event_id']);
                                                        }
                                                        if (isset($notification->data['appraisal_id'])) {
                                                            $url = url('uncst-appraisals', $notification->data['appraisal_id']);
                                                        }
                                                        if (isset($notification->data['travel_training_id'])) {
                                                            $url = url('out-of-station-trainings', $notification->data['travel_training_id']);
                                                        }
                                                        if (isset($notification->data['reminder_category'])) {
                                                            if ($notification->data['reminder_category'] == 'appraisal') {
                                                                $url = url('uncst-appraisals');
                                                            }
                                                        }
                                                        $isUnread = is_null($notification->read_at);
                                                    @endphp
                                                    <li class="list-group-item notification-item d-flex align-items-start justify-content-between gap-2 rounded-2 mb-2 px-3 py-2 border-0 {{ $isUnread ? 'bg-light shadow-sm' : '' }}"
                                                        data-url="{{ $url }}"
                                                        data-id="{{ $notification->id }}"
                                                        data-type="{{ $notification->type }}"
                                                        style="cursor:pointer; transition: background 0.2s;">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <i class="bi bi-dot {{ $isUnread ? 'text-primary' : 'text-secondary' }} fs-5"></i>
                                                                <span class="fw-semibold">{{ $notification->data['message'] }}</span>
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                        <button class="btn-close ms-2 mt-1" aria-label="Close" title="Mark as read & dismiss"></button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                                <p class="mt-2 text-muted">No new notifications.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    @push('scripts')
                        <script>
                            $(document).ready(function() {
                                $('.notification-item').on('click', function() {
                                    const notificationUrl = $(this).data('url');
                                    const notificationId = $(this).data('id');

                                    // AJAX request to mark notification as read
                                    $.ajax({
                                        url: `/notifications/${notificationId}/read`,
                                        type: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                                        },
                                        success: function(data) {
                                            if (data.success) {
                                                $(this).addClass(
                                                    'list-group-item-success'); // Optionally add a success class
                                                window.location.href = notificationUrl;
                                            }
                                        }.bind(this), // Bind 'this' to access the clicked element
                                        error: function(xhr) {
                                            console.error('Error:', xhr);
                                        }
                                    });
                                });

                                $('.btn-close').on('click', function(event) {
                                    event.stopPropagation(); // Prevent the parent click event

                                    const notificationItem = $(this).closest('.notification-item');
                                    const notificationId = notificationItem.data('id');
                                    const notificationUrl = notificationItem.data('url');

                                    // AJAX request to mark notification as read
                                    $.ajax({
                                        url: `/notifications/${notificationId}/read`,
                                        type: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
                                        },
                                        success: function(data) {
                                            if (data.success) {
                                                notificationItem
                                            .remove(); // Remove the notification item from the UI
                                            }
                                        },
                                        error: function(xhr) {
                                            console.error('Error:', xhr);
                                        }
                                    });
                                });
                            });
                        </script>
                    @endpush
                </div><!-- End Right side columns -->

            </div>
        @endif

        @if (count($todayBirthdays))
            @php
                $authUserBirthday = $todayBirthdays->firstWhere('employee_id', auth()->user()->employee->employee_id);
                $sharedBirthdays = $todayBirthdays->where('employee_id', '!=', auth()->user()->employee->employee_id);
            @endphp

            @if ($authUserBirthday)
                <!-- Toast for the authenticated user's birthday -->
                <div class="bottom-0 p-3 toast-container position-fixed start-50 translate-middle-x text-bg-success"
                    role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-cake2" viewBox="0 0 16 16">
                            <path d="..." />
                        </svg>
                        <strong class="me-auto">Happy Birthday,
                            {{ auth()->user()->employee->first_name }}! 🎉</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Wishing you a fantastic day filled with joy and success! 🎂🎈
                    </div>
                </div>

                @if ($sharedBirthdays->count())
                    <!-- Toast for others sharing the birthday -->
                    <div class="bottom-0 p-3 toast-container position-fixed end-0 translate-middle-x text-bg-info"
                        role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-cake2" viewBox="0 0 16 16">
                                <path d="..." />
                            </svg>
                            <strong class="me-auto">Others Sharing Your Birthday 🎂</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            @foreach ($sharedBirthdays as $sharedBirthday)
                                <p>{{ $sharedBirthday->first_name }} {{ $sharedBirthday->last_name }}
                                    - {{ $sharedBirthday->department->department_name }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <!-- General Toast for birthdays -->
                <div class="bottom-0 p-3 toast-container position-fixed start-50 translate-middle-x text-bg-primary"
                    role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-cake2" viewBox="0 0 16 16">
                            <path d="..." />
                        </svg>
                        <strong class="me-auto">Today's Birthdays</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        @foreach ($todayBirthdays as $todayBirthday)
                            <p>{{ $todayBirthday->first_name }} {{ $todayBirthday->last_name }}
                                - {{ optional($todayBirthday->department)->department_name }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </section>
    <!-- Modal -->
    <div class="modal fade" id="consent" tabindex="-1" aria-labelledby="consent" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">
                        <i class="bi bi-calendar-plus"></i> Data Protection Law
                    </h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body">
                    <form id="consentForm" action="{{ route('agree') }}" method="post">
                        @csrf
                        <input type="hidden" name="agreed_to_data_usage" value="true">
                    </form>
                    <p>
                        In accordance with the Data Protection and Privacy Act of Uganda, we request your consent to
                        collect, process, and use your personal data for employment and human resource management
                        purposes. Your information will be handled securely and in compliance with applicable laws and
                        regulations. It will only be used for legitimate employment-related purposes and other
                        authorized activities in line with the policies and obligations of the Government of Uganda.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyButton"
                        data-bs-dismiss="modal">Accept</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/custom-dashboard.js'])

        <script type="module">
            $(document).ready(function() {
                var leaveTypes = {!! $leaveTypesJson !!}; // Leave type names
                var allocatedDays = {!! $chartDataJson !!}; // Allocated leave days
                var employeeData = {!! $chartEmployeeDataJson !!};
                console.log(window.isAdminOrSecretary);
                // open the consent modal here
                @if (!auth()->user()->agreed_to_data_usage)
                    var consentModal = new bootstrap.Modal(document.getElementById('consent'));
                    consentModal.show();
                @endif

                document.getElementById('applyButton').addEventListener('click', function() {
                    document.getElementById('consentForm').submit();
                });


                var budgetChart = echarts.init(document.querySelector("#budgetChart"));
                budgetChart.setOption({
                    legend: {
                        data: ['Allocated Leave Days']
                    },
                    radar: {
                        indicator: leaveTypes.map(type => ({
                            name: type,
                            max: 30
                        })) // Adjust max as necessary
                    },
                    series: [{
                        name: 'Allocated Leave Days',
                        type: 'radar',
                        data: [{
                            value: allocatedDays,
                            name: 'Allocated Leave Days'
                        }]
                    }]
                });

                if (window.isAdminOrSecretary) {

                    echarts.init(document.querySelector("#trafficChart")).setOption({
                        tooltip: {
                            trigger: 'item'
                        },
                        // legend: {
                        //     top: '5%',
                        //     left: 'center'
                        // },
                        series: [{
                            name: 'Employees by Department',
                            type: 'pie',
                            radius: ['40%', '70%'],
                            avoidLabelOverlap: false,
                            label: {
                                show: false,
                            },
                            data: employeeData
                        }]
                    });
                }
                if (window.isAdminOrSecretary) {

                    //attendance analysis
                    new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                            name: 'Today',
                            data: @json($todayCounts),
                        }, {
                            name: 'Yesterday',
                            data: @json($yesterdayCounts),
                        }, {
                            name: 'Late Arrivals',
                            data: @json($lateCounts),
                        }],
                        chart: {
                            height: 350,
                            type: 'area',
                            toolbar: {
                                show: false
                            },
                        },
                        markers: {
                            size: 4
                        },
                        colors: ['#4154f1', '#ff771d', '#ffbc00'], // Different colors for each series
                        fill: {
                            type: "gradient",
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.3,
                                opacityTo: 0.4,
                                stops: [0, 90, 100]
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        xaxis: {
                            type: 'datetime',
                            categories: @json($hours),
                        },
                        tooltip: {
                            x: {
                                format: 'dd/MM/yy HH:mm'
                            },
                        }
                    }).render();
                }

            });
            //real notifications
        </script>
    @endpush
</x-app-layout>
