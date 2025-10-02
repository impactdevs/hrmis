<x-app-layout>
    <section class="m-2 section dashboard">
        @if (auth()->user()->isAdminOrSecretary)
            <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- Employees Card -->
                        <div class="col-xxl-3 col-md-6">
                            <a href="{{ route('employees.index') }}">
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
                            </a>
                        </div><!-- End Employees Card -->
                        <!-- Attendees Card -->
                        <div class="col-xxl-3 col-md-6">
                            <a href="{{ route('attendances.index') }}">
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
                            </a>
                        </div><!-- End Attendees Card -->

                        <!-- Pending Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">

                                <div class="card info-card customers-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Submitted Appraisals</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-hourglass-split text-warning"></i>
                                            </div>
                                            <div class="ps-3">

                                                @if (auth()->user()->hasRole('Head of Division'))
                                                <a href="{{ route('uncst-appraisals.index', ['dashboard_filter' => 'submitted_to_HoD']) }}">
                                                    <h6>{{ $submittedAppraisalsBystaff->count() }}</h6>
                                                    <span class="pt-2 text-muted small ps-1">
                                                        Appraisals submitted to the
                                                        H.o.D
                                                    </span>
                                                </a>
                                                @elseif(auth()->user()->hasRole('HR'))
                                                <a href="{{ route('uncst-appraisals.index', ['dashboard_filter' => 'received_from_HoDs']) }}">
                                                    <h6>{{ $submittedAppraisalsByHoD->count() }}</h6>
                                                    <span class="pt-2 text-muted small ps-1">
                                                        Appraisals Received from HoDs
                                                    </span>
                                                </a>
                                                @elseif(auth()->user()->hasRole('Executive Secretary'))
                                                <a href="{{ route('uncst-appraisals.index', ['dashboard_filter' => 'completed_appraisals']) }}">
                                                    <h6>{{ $submittedAppraisalsByHR->count() }}</h6>
                                                    <span class="pt-2 text-muted small ps-1">
                                                        Appraisals you have reviewed
                                                    </span>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>


                        <!-- Ongoing Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">

                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    @if (auth()->user()->hasRole('Head of Division'))
                                        <h5 class="card-title">Appraisals Submitted To The H.R</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-arrow-repeat text-primary"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $submittedAppraisalsByHoD->count() }}</h6>Appraisals
                                                submitted to
                                                the
                                                H.R</span>
                                            </div>
                                        </div>
                                    @elseif(auth()->user()->hasRole('HR'))
                                        <a href="{{ route('uncst-appraisals.index', ['dashboard_filter'=>'submitted_to_es']) }}">
                                            <h5 class="card-title">Appraisals Submitted To The Executive Secretary</h5>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-arrow-repeat text-primary"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $submittedAppraisalsByHR->count() }}</h6>Appraisals
                                                    submitted to
                                                    the
                                                    E.S</span>
                                                </div>
                                            </div>
                                        </a>
                                    @elseif(auth()->user()->hasRole('Executive Secretary'))
                                    <a href="{{ route('uncst-appraisals.index', ['dashboard_filter'=>'from_all_supervisors']) }}">
                                        <h5 class="card-title">Appraisals Submitted By Supervisors</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-arrow-repeat text-primary"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $submittedAppraisalsByHR->count() }}</h6>Appraisals Reviewed
                                                By The HR</span>
                                            </div>
                                        </div>
                                    </a>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <a href="{{ route('uncst-appraisals.index', ['dashboard_filter'=>'completed_appraisals']) }}">
                                <div class="card info-card customers-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Complete Appraisals</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-check-circle text-success"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $completeAppraisals->count() }}</h6>
                                                <span class="pt-2 text-muted small ps-1">Approved By E.S</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        @if (!auth()->user()->hasRole('HR'))
                            <div class="col-xxl-4 col-md-6">
                                <a href="{{ route('uncst-appraisals.index', ['dashboard_filter'=>'pending_appraisals']) }}">
                                    <div class="card info-card customers-card">
                                        <div class="card-body">
                                            <h5 class="card-title">Pending Appraisals</h5>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-check-circle text-success"></i>
                                                </div>
                                                <div class="ps-3">
                                                    <h6>{{ $pendingAppraisals }}</h6>
                                                    <span class="pt-2 text-muted small ps-1">Your Draft
                                                        Appraisals</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

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
                            <a href="{{ route('leaves.index') }}">
                                <div class="card info-card customers-card">

                                    <div class="card-body">
                                        <h5 class="card-title">Employees on Leave <span>| Currently</span>
                                        </h5>

                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-people"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h4 class="card-title">Number of Employees on Leave <span>| Currently</span></h4>

                                                <h4>{{ $available_leave }}</h4>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </a>

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
                        @if (auth()->user()->hasRole('HR'))
                            <!-- Applications -->
                            <div class="col-12">
                                <div class="card shadow-sm border-0 recent-sales">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-person-lines-fill text-primary"></i>
                                                Latest Applications <span
                                                    class="badge bg-primary">{{ $entries->count() }}</span>
                                                <small class="text-muted">(Latest 5 | Today)</small>
                                            </h5>
                                            <a href="{{ route('uncst-job-applications.index') }}"
                                                class="btn btn-sm btn-outline-primary">View All</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table align-middle table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col"><i class="bi bi-person"></i> Full Name</th>
                                                        <th scope="col"><i class="bi bi-briefcase"></i> Position
                                                        </th>
                                                        <th scope="col"><i class="bi bi-calendar-event"></i>
                                                            Applied On</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($entries as $entry)
                                                        <tr>
                                                            <td>
                                                                <span
                                                                    class="badge bg-secondary">{{ $entry->id }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span
                                                                        class="avatar rounded-circle bg-primary text-white fw-bold"
                                                                        style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                                                        {{ strtoupper(substr($entry->full_name, 0, 1)) }}
                                                                    </span>
                                                                    <span>{{ $entry->full_name }}</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info text-dark">
                                                                    {{ \App\Models\CompanyJob::where('job_code', $entry->reference_number)->first()->job_title ?? 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="text-nowrap">
                                                                    <i class="bi bi-clock text-primary"></i>
                                                                    {{ $entry->created_at->format('d M, Y H:i') }}
                                                                </span>
                                                                <br>
                                                                <small
                                                                    class="text-muted">{{ $entry->created_at->diffForHumans() }}</small>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted py-4">
                                                                <i class="bi bi-emoji-frown fs-2"></i>
                                                                <div>No applications found for today.</div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endif
                        <!-- Ongoing Appraisals -->
                        <div class="col-12">
                            <div class="overflow-auto card top-selling">

                                <div class="card-body pb-0">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-arrow-repeat text-primary"></i>
                                            Submitted Appraisals <span class="badge bg-primary"><span
                                                    class="badge bg-primary">

                                                    @if (auth()->user()->hasRole('Head of Division'))
                                                        {{ $submittedAppraisalsBystaff->count() }}
                                                    @elseif(auth()->user()->hasRole('HR'))
                                                        {{ $submittedAppraisalsByHoD->count() }}
                                                    @elseif(auth()->user()->hasRole('Executive Secretary'))
                                                        {{ $submittedAppraisalsByHR->count() }}
                                                    @endif
                                                </span></span>
                                            <small class="text-muted">(Latest 5)</small>
                                        </h5>
                                        <a href="{{ route('uncst-appraisals.index') }}"
                                            class="btn btn-sm btn-outline-primary">View All</a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table align-middle table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col"><i class="bi bi-person"></i> Full Name</th>
                                                    <th scope="col"><i class="bi bi-diagram-3"></i> Department
                                                    </th>
                                                    <th scope="col"><i class="bi bi-calendar-event"></i>
                                                        Applied On
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    if (auth()->user()->hasRole('Head of Division')) {
                                                        $latestAppraisals = $submittedAppraisalsBystaff
                                                            ->sortByDesc('created_at')
                                                            ->take(5);
                                                    } elseif (auth()->user()->hasRole('HR')) {
                                                        $latestAppraisals = $submittedAppraisalsByHoD
                                                            ->sortByDesc('created_at')
                                                            ->take(5);
                                                    } elseif (auth()->user()->hasRole('Executive Secretary')) {
                                                        $latestAppraisals = $submittedAppraisalsByHR
                                                            ->sortByDesc('created_at')
                                                            ->take(5);
                                                    } else {
                                                        $latestAppraisals = collect(); // empty collection
                                                    }
                                                @endphp
                                                @forelse ($latestAppraisals as $appraisal)
                                                    @if ($appraisal->has_some_draft && !$appraisal->has_draft)
                                                        @continue
                                                    @endif
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span
                                                                    class="avatar rounded-circle bg-primary text-white fw-bold"
                                                                    style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                                                    {{ strtoupper(substr($appraisal->employee->first_name, 0, 1)) }}{{ strtoupper(substr($appraisal->employee->last_name ?? $appraisal->employee->first_name, 0, 1)) }}
                                                                </span>
                                                                <span>
                                                                    {{ $appraisal->employee->first_name . ' ' . ($appraisal->employee->last_name ?? $appraisal->employee->first_name) }}
                                                                    <br>
                                                                    <small
                                                                        class="text-muted">{{ $appraisal->employee->job_title ?? '' }}</small>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info text-dark">
                                                                {{ $appraisal->employee->department->department_name ?? '-' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-nowrap">
                                                                <i class="bi bi-clock text-primary"></i>
                                                                {{ \Carbon\Carbon::parse($appraisal->created_at)->format('d M, Y') }}
                                                            </span>
                                                            <br>
                                                            <small
                                                                class="text-muted">{{ \Carbon\Carbon::parse($appraisal->created_at)->diffForHumans() }}</small>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-4">
                                                            <i class="bi bi-emoji-frown fs-2"></i>
                                                            <div>No ongoing appraisals found.</div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
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

                    {{-- Un read Notification Reminders --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-bell-fill text-primary"></i> Latest Notifications <span
                                        class="badge bg-primary text-light">{{ $notifications->count() }}</span>
                                </h5>
                                <a href="{{ route('notifications.index') }}"
                                    class="btn btn-link btn-sm text-decoration-none">View All</a>
                            </div>
                            <section class="section dashboard px-1">
                                <div class="row">
                                    <div class="col-12">
                                        @if ($notifications->isNotEmpty())
                                            <ul class="list-group list-group-flush">
                                                @foreach ($notifications as $notification)
                                                    @php
                                                        $url = '';
                                                        if (isset($notification->data['leave_id']) && !empty($notification->data['leave_id'])) {
                                                            try {
                                                                $url = route('leaves.show', ['leave' => $notification->data['leave_id']]);
                                                            } catch (Exception $e) {
                                                                // If route generation fails, default to leaves index
                                                                $url = route('leaves.index');
                                                            }
                                                        }
                                                        if (isset($notification->data['training_id'])) {
                                                            $url = route('trainings.show', $notification->data['training_id']);
                                                        }
                                                        if (isset($notification->data['event_id'])) {
                                                            $url = route('events.show', $notification->data['event_id']);
                                                        }
                                                        if (isset($notification->data['appraisal_id'])) {
                                                            $url = route(
                                                                'uncst-appraisals.show',
                                                                $notification->data['appraisal_id'],
                                                            );
                                                        }
                                                        if (isset($notification->data['travel_training_id'])) {
                                                            $url = route(
                                                                'out-of-station-trainings.show',
                                                                $notification->data['travel_training_id'],
                                                            );
                                                        }
                                                        if (isset($notification->data['reminder_category'])) {
                                                            if (
                                                                $notification->data['reminder_category'] == 'appraisal'
                                                            ) {
                                                                $url = route('uncst-appraisals.index');
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
                                                                <i
                                                                    class="bi bi-dot {{ $isUnread ? 'text-primary' : 'text-secondary' }} fs-5"></i>
                                                                <span
                                                                    class="fw-semibold">{{ $notification->data['message'] }}</span>
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="bi bi-clock"></i>
                                                                {{ $notification->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                        <button class="btn-close ms-2 mt-1" aria-label="Close"
                                                            title="Mark as read & dismiss"></button>
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
                            <a href="{{ route('uncst-appraisals.index', ['dashboard_filter' => 'my_appraisals']) }}">
                                <div class="card info-card customers-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Submitted Appraisals</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-hourglass-split text-warning"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $submittedAppraisalsBystaff->count() }}</h6>
                                                <span class="pt-2 text-muted small ps-1">Appraisals submitted to the
                                                    H.o.D</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Ongoing Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <a href="{{ route('uncst-appraisals.index') }}">
                                <div class="card info-card customers-card">
                                    <div class="card-body">
                                        <h5 class="card-title">On Going Appraisals</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-arrow-repeat text-primary"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $ongoingAppraisals->count() }}</h6>Appraisals in Approval</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Complete Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
                            <a href="{{ route('uncst-appraisals.index') }}">
                                <div class="card info-card customers-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Complete Appraisals</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-check-circle text-success"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $submittedAppraisalsByHR->count() }}</h6>
                                                <span class="pt-2 text-muted small ps-1">Approved By E.S</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-xxl-4 col-md-6">
                            <a href="{{ route('uncst-appraisals.index') }}">
                                <div class="card info-card customers-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Pending Appraisals</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-check-circle text-success"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $pendingAppraisals }}</h6>
                                                <span class="pt-2 text-muted small ps-1">Your Draft Appraisals</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
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
                            <!-- Leave Requests Timeline -->
                            <div class="col-xxl-12 col-md-12">
                                <div class="card info-card border-0 shadow-sm leave-approval-card">
                                    <div class="card-header bg-primary text-white border-0">
                                        <h5 class="mb-0 fw-bold d-flex align-items-center">
                                            <i class="bi bi-calendar-check me-2"></i>
                                            My Recent Leave Requests
                                            <span class="badge bg-light text-primary ms-auto">{{ count($leaveApprovalData) }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body" id="leaveTrackingContainer">
                                        <div class="row" id="leaveRequestsContainer">
                                            @foreach ($leaveApprovalData as $index => $leaveData)
                                                <div class="col-12 mb-4 leave-request-item" data-leave-id="{{ $leaveData['leave']->leave_id ?? '' }}">
                                                    <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                                                        <!-- Animated border indicator -->
                                                        <div class="position-absolute top-0 start-0 w-100 h-2
                                                            @if($leaveData['esStatus'] === 'Approved') bg-success
                                                            @elseif($leaveData['status'] === 'Rejected') bg-danger
                                                            @else bg-warning @endif
                                                            progress-indicator" style="height: 4px;"></div>

                                                        <div class="card-body">
                                                            <!-- Header Section -->
                                                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                                                        <i class="bi bi-calendar-event me-1"></i>
                                                                        {{ $leaveData['leave_type_name'] ?? 'Leave' }}
                                                                    </span>
                                                                    <span class="text-muted">
                                                                        <i class="bi bi-clock me-1"></i>
                                                                        {{ \Carbon\Carbon::parse($leaveData['start_date'])->format('d M') }} -
                                                                        {{ \Carbon\Carbon::parse($leaveData['end_date'])->format('d M, Y') }}
                                                                    </span>
                                                                </div>
                                                                <div class="status-badge-container">
                                                                    @if($leaveData['esStatus'] === 'Approved')
                                                                        <span class="badge bg-success px-3 py-2 rounded-pill animate-pulse">
                                                                            <i class="bi bi-check-circle me-1"></i>Fully Approved
                                                                        </span>
                                                                    @elseif($leaveData['status'] === 'Rejected')
                                                                        <span class="badge bg-danger px-3 py-2 rounded-pill">
                                                                            <i class="bi bi-x-circle me-1"></i>Rejected
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                                                            <i class="bi bi-hourglass-split me-1"></i>
                                                                            @php
                                                                                $currentStage = 'Pending HR Approval';
                                                                                if($leaveData['hrStatus'] === 'Approved') {
                                                                                    $currentStage = 'Pending HOD Approval';
                                                                                }
                                                                                if($leaveData['hodStatus'] === 'Approved') {
                                                                                    $currentStage = 'Pending Executive Secretary';
                                                                                }
                                                                            @endphp
                                                                            {{ $currentStage }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Details Section - Responsive Grid -->
                                                            <div class="row g-3 mb-4">
                                                                @if(!empty($leaveData['rejection_reason']))
                                                                <div class="col-md-6">
                                                                    <div class="info-item d-flex align-items-start">
                                                                        <i class="bi bi-exclamation-triangle text-danger me-2 mt-1"></i>
                                                                        <div>
                                                                            <small class="text-muted d-block">Rejection Reason</small>
                                                                            <span class="fw-medium">{{ $leaveData['rejection_reason'] }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif

                                                                @if(!empty($leaveData['phone_number']))
                                                                <div class="col-md-6">
                                                                    <div class="info-item d-flex align-items-start">
                                                                        <i class="bi bi-telephone text-success me-2 mt-1"></i>
                                                                        <div>
                                                                            <small class="text-muted d-block">Contact</small>
                                                                            <span class="fw-medium">{{ $leaveData['phone_number'] }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            </div>

                                                            <!-- Sequential Approval Progress -->
                                                            <div class="approval-flow-container mb-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                                    <small class="text-muted fw-semibold">Approval Progress</small>
                                                                    <small class="text-muted">
                                                                        @php
                                                                            $approvedCount = 0;
                                                                            $rejectedCount = 0;
                                                                            if($leaveData['hrStatus'] === 'Approved') $approvedCount++;
                                                                            if($leaveData['hodStatus'] === 'Approved') $approvedCount++;
                                                                            if($leaveData['esStatus'] === 'Approved') $approvedCount++;
                                                                            if($leaveData['hrStatus'] === 'Rejected') $rejectedCount++;
                                                                            if($leaveData['hodStatus'] === 'Rejected') $rejectedCount++;
                                                                            if($leaveData['esStatus'] === 'Rejected') $rejectedCount++;
                                                                            $progressPercentage = ($leaveData['progress'] ?? 0);
                                                                        @endphp
                                                                        @if($rejectedCount > 0)
                                                                            Rejected
                                                                        @else
                                                                            {{ $approvedCount }}/3 Approved
                                                                        @endif
                                                                    </small>
                                                                </div>

                                                                <!-- Modern Progress Bar -->
                                                                <div class="progress mb-3" style="height: 8px; border-radius: 10px;">
                                                                    <div class="progress-bar progress-bar-striped
                                                                        @if($leaveData['status'] === 'Rejected') bg-danger
                                                                        @elseif($approvedCount === 3) bg-success progress-bar-animated
                                                                        @else bg-primary progress-bar-animated @endif"
                                                                        role="progressbar"
                                                                        style="width: {{ $progressPercentage }}%;"
                                                                        aria-valuenow="{{ $progressPercentage }}"
                                                                        aria-valuemin="0"
                                                                        aria-valuemax="100">
                                                                    </div>
                                                                </div>

                                                                <!-- Approval Stages - Mobile Responsive -->
                                                                <div class="row text-center g-2">
                                                                    <div class="col-4">
                                                                        <div class="approval-stage p-2 rounded
                                                                            @if($leaveData['hrStatus'] === 'Approved') bg-success-subtle text-success
                                                                            @elseif($leaveData['hrStatus'] === 'Rejected') bg-danger-subtle text-danger
                                                                            @else bg-warning-subtle text-warning @endif">
                                                                            <div class="stage-icon mb-1">
                                                                                @if($leaveData['hrStatus'] === 'Approved')
                                                                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                                                                @elseif($leaveData['hrStatus'] === 'Rejected')
                                                                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                                                                @else
                                                                                    <i class="bi bi-hourglass-split fs-5"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="stage-title">
                                                                                <small class="fw-semibold d-block">HR</small>
                                                                                <small class="d-block">
                                                                                    @if($leaveData['hrStatus'] === 'Approved') Approved
                                                                                    @elseif($leaveData['hrStatus'] === 'Rejected') Rejected
                                                                                    @else Pending @endif
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-4">
                                                                        <div class="approval-stage p-2 rounded
                                                                            @if($leaveData['hodStatus'] === 'Approved') bg-success-subtle text-success
                                                                            @elseif($leaveData['hodStatus'] === 'Rejected') bg-danger-subtle text-danger
                                                                            @else bg-warning-subtle text-warning @endif">
                                                                            <div class="stage-icon mb-1">
                                                                                @if($leaveData['hodStatus'] === 'Approved')
                                                                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                                                                @elseif($leaveData['hodStatus'] === 'Rejected')
                                                                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                                                                @else
                                                                                    <i class="bi bi-hourglass-split fs-5 @if($leaveData['hrStatus'] !== 'Approved') text-muted @endif"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="stage-title">
                                                                                <small class="fw-semibold d-block">HOD</small>
                                                                                <small class="d-block">
                                                                                    @if($leaveData['hodStatus'] === 'Approved') Approved
                                                                                    @elseif($leaveData['hodStatus'] === 'Rejected') Rejected
                                                                                    @elseif($leaveData['hrStatus'] === 'Approved') Pending
                                                                                    @else Awaiting @endif
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-4">
                                                                        <div class="approval-stage p-2 rounded
                                                                            @if($leaveData['esStatus'] === 'Approved') bg-success-subtle text-success
                                                                            @elseif($leaveData['esStatus'] === 'Rejected') bg-danger-subtle text-danger
                                                                            @else bg-warning-subtle text-warning @endif">
                                                                            <div class="stage-icon mb-1">
                                                                                @if($leaveData['esStatus'] === 'Approved')
                                                                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                                                                @elseif($leaveData['esStatus'] === 'Rejected')
                                                                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                                                                @else
                                                                                    <i class="bi bi-hourglass-split fs-5 @if(strtolower($leaveData['hodStatus']) !== 'approved') text-muted @endif"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="stage-title">
                                                                                <small class="fw-semibold d-block">ES</small>
                                                                                <small class="d-block">
                                                                                    @if($leaveData['esStatus'] === 'Approved') Approved
                                                                                    @elseif($leaveData['esStatus'] === 'Rejected') Rejected
                                                                                    @elseif(strtolower($leaveData['hodStatus']) === 'approved') Pending
                                                                                    @else Awaiting @endif
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Status Messages -->
                                                            @if($leaveData['esStatus'] === 'Approved')
                                                                <div class="alert alert-success border-0 d-flex align-items-center mb-3" role="alert">
                                                                    <i class="bi bi-check-circle-fill me-2"></i>
                                                                    <div>
                                                                        <strong>Congratulations!</strong> Your leave is fully approved.
                                                                        @if($leaveData['daysRemaining'] == 'Leave has not started')
                                                                            <small class="d-block mt-1 text-success-emphasis">Leave period has not started yet.</small>
                                                                        @elseif(is_numeric($leaveData['daysRemaining']))
                                                                            <small class="d-block mt-1 text-success-emphasis">Days remaining: <strong>{{ $leaveData['daysRemaining'] }}</strong></small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @elseif($leaveData['status'] === 'Rejected' && isset($leaveData['rejection_reason']))
                                                                <div class="alert alert-danger border-0" role="alert">
                                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                                    <strong>Rejected:</strong> {{ $leaveData['rejection_reason'] }}
                                                                </div>
                                                            @else
                                                                <div class="alert alert-info border-0" role="alert">
                                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                                    <strong>Status:</strong> Currently in {{ $currentStage ?? 'approval process' }}.
                                                                    @if($leaveData['hrStatus'] !== 'Approved')
                                                                        <small class="d-block mt-1">Waiting for HR to review your request.</small>
                                                                    @elseif(strtolower($leaveData['hodStatus']) !== 'approved')
                                                                        <small class="d-block mt-1">HR approved. Forwarded to Head of Division.</small>
                                                                    @else
                                                                        <small class="d-block mt-1">HOD approved. Forwarded to Executive Secretary.</small>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            <!-- Action Buttons -->
                                                            <div class="d-flex flex-wrap gap-2 mt-3">
                                                                @if(!empty($leaveData['handover_note_file']))
                                                                    <a href="{{ route('leaves.handover.view', ['leave' => $leaveData['leave']->leave_id]) }}"
                                                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>
                                                                        Handover Document
                                                                    </a>
                                                                @endif

                                                                @if(isset($leaveData['leave']))
                                                                    <a href="{{ route('leaves.show', ['leave' => $leaveData['leave']->leave_id]) }}"
                                                                       class="btn btn-sm btn-outline-info">
                                                                        <i class="bi bi-eye me-1"></i>
                                                                        View Details
                                                                    </a>
                                                                @endif
                                                            </div>

                                                            <!-- Handover Note Preview -->
                                                            @if(!empty($leaveData['handover_note']))
                                                                <div class="mt-3 p-3 bg-light rounded border-start border-primary border-3">
                                                                    <small class="text-muted d-block mb-1">
                                                                        <i class="bi bi-journal-text me-1"></i>Handover Note:
                                                                    </small>
                                                                    <div class="handover-note-preview" style="max-height: 60px; overflow: hidden;">
                                                                        {{ Str::limit($leaveData['handover_note'], 150) }}
                                                                    </div>
                                                                    @if(strlen($leaveData['handover_note']) > 150)
                                                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none toggle-handover-note">
                                                                            <small>Show more...</small>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-xxl-12 col-md-12">
                                <div class="card info-card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <h5 class="mb-4 fw-bold text-primary">
                                            <i class="bi bi-calendar-check"></i> No Recent Leave Requests
                                        </h5>
                                        <p class="text-muted">
                                            You have not submitted any leave requests recently. Please submit a leave
                                            request
                                            if you need to take time off.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Appraisal Progress Timeline --}}
                        @if (count($appraisalProgressData) > 0)
                            <div class="col-xxl-12 col-md-12">
                                <div class="card info-card border-0 shadow-sm appraisal-progress-card">
                                    <div class="card-body">
                                        <h5 class="mb-4 fw-bold text-primary">
                                            <i class="bi bi-clipboard-check"></i> My Appraisal Progress
                                        </h5>
                                        <ul class="timeline list-unstyled">
                                            @foreach ($appraisalProgressData as $appraisalData)
                                                <li class="timeline-item mb-5 position-relative ps-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="badge bg-primary me-2">Appraisal</span>
                                                        <span class="fw-semibold">
                                                            {{ \Carbon\Carbon::parse($appraisalData['appraisal_period_start'])->format('d M Y') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($appraisalData['appraisal_period_end'])->format('d M Y') }}
                                                        </span>
                                                        @if ($appraisalData['status'] === 'Complete')
                                                            <span class="badge bg-success ms-2">{{ $appraisalData['status'] }}</span>
                                                        @elseif (str_contains($appraisalData['status'], 'Rejected'))
                                                            <span class="badge bg-danger ms-2">{{ $appraisalData['status'] }}</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark ms-2">{{ $appraisalData['status'] }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="ms-1 mb-2">
                                                        <small>
                                                            <i class="bi bi-info-circle"></i>
                                                            <strong>Current Stage:</strong> {{ $appraisalData['current_stage'] }}
                                                        </small>
                                                    </div>

                                                    {{-- Progress Bar --}}
                                                    <div class="progress my-3" style="height: 12px;">
                                                        <div class="progress-bar {{ $appraisalData['progress'] == 100 ? 'bg-success' : (str_contains($appraisalData['status'], 'Rejected') ? 'bg-danger' : 'bg-primary') }}"
                                                            role="progressbar" style="width: {{ $appraisalData['progress'] }}%;"
                                                            aria-valuenow="{{ $appraisalData['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                                            {{ $appraisalData['progress'] }}%
                                                        </div>
                                                    </div>

                                                    {{-- Approval Stages --}}
                                                    <div class="d-flex justify-content-between text-center small mb-2">
                                                        <span>
                                                            <i class="bi bi-person-workspace"></i>
                                                            HoD<br>
                                                            @if (strtolower($appraisalData['hodStatus']) === 'approved')
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @elseif (strtolower($appraisalData['hodStatus']) === 'rejected')
                                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                            @else
                                                                <i class="bi bi-hourglass-split text-warning"></i>
                                                            @endif
                                                        </span>
                                                        <span>
                                                            <i class="bi bi-person-badge"></i>
                                                            HR<br>
                                                            @if (strtolower($appraisalData['hrStatus']) === 'approved')
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @elseif (strtolower($appraisalData['hrStatus']) === 'rejected')
                                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                            @else
                                                                <i class="bi bi-hourglass-split text-warning"></i>
                                                            @endif
                                                        </span>
                                                        <span>
                                                            <i class="bi bi-person-lines-fill"></i>
                                                            Executive<br>
                                                            @if (strtolower($appraisalData['esStatus']) === 'approved')
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @elseif (strtolower($appraisalData['esStatus']) === 'rejected')
                                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                            @else
                                                                <i class="bi bi-hourglass-split text-warning"></i>
                                                            @endif
                                                        </span>
                                                    </div>

                                                    {{-- Status Alert --}}
                                                    @if ($appraisalData['status'] === 'Complete')
                                                        <div class="alert alert-success py-2 px-3 mt-2 mb-0 d-flex align-items-center gap-2">
                                                            <i class="bi bi-emoji-laughing fs-4"></i>
                                                            <div>
                                                                <strong>Congratulations!</strong> Your appraisal is fully completed.
                                                            </div>
                                                        </div>
                                                    @elseif (str_contains($appraisalData['status'], 'Rejected'))
                                                        <div class="alert alert-danger py-2 px-3 mt-2 mb-0">
                                                            <strong>{{ $appraisalData['status'] }}:</strong>
                                                            {{ $appraisalData['current_stage'] }}
                                                        </div>
                                                    @else
                                                        <div class="alert alert-info py-2 px-3 mt-2 mb-0">
                                                            <span class="fw-semibold">{{ $appraisalData['current_stage'] }}</span>
                                                        </div>
                                                    @endif

                                                    {{-- Action Button --}}
                                                    <div class="d-flex gap-2 mt-2">
                                                        <a href="{{ route('uncst-appraisals.index', $appraisalData['appraisal']->appraisal_id) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i> View Appraisal
                                                        </a>

                                                        @if ($appraisalData['can_be_withdrawn'])

                                                            <form action="{{ route('appraisals.withdraw', $appraisalData['appraisal']) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                                    onclick="return confirm('Are you sure you want to withdraw this appraisal? This will: Remove it from approval process Reset to draft status Allow further edits Require resubmission. This action cannot be undone.')">
                                                                    <i class="bi bi-arrow-counterclockwise"></i> Withdraw
                                                                </button>
                                                            </form>

                                                        @endif
                                                    </div>
                                                    <hr class="my-4">
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-xxl-12 col-md-12">
                                <div class="card info-card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <h5 class="mb-4 fw-bold text-primary">
                                            <i class="bi bi-clipboard-check"></i> No Submitted Appraisals
                                        </h5>
                                        <p class="text-muted">
                                            You have not submitted any appraisals yet. Please submit an appraisal
                                            when the appraisal period is open.
                                        </p>
                                        <a href="{{ route('uncst-appraisals.index') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> View Appraisals
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @push('styles')
                            <style>
                                .timeline {
                                    border-left: 3px solid #0d6efd;
                                    margin-left: 1.5rem;
                                    padding-left: 0.5rem;
                                }

                                .timeline-item:before {
                                    content: '';
                                    position: absolute;
                                    left: -1.2rem;
                                    top: 0.5rem;
                                    width: 1rem;
                                    height: 1rem;
                                    background: #fff;
                                    border: 3px solid #0d6efd;
                                    border-radius: 50%;
                                    z-index: 1;
                                }

                                /* Leave Approval Card Enhancements */
                                .leave-approval-card {
                                    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
                                    border-radius: 15px;
                                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                                    overflow: hidden;
                                }

                                .leave-request-item {
                                    transition: all 0.3s ease;
                                }

                                .leave-request-item:hover {
                                    transform: translateY(-2px);
                                }

                                .leave-request-item .card {
                                    transition: all 0.3s ease;
                                    border: 1px solid rgba(0, 0, 0, 0.08);
                                }

                                .leave-request-item:hover .card {
                                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
                                    border-color: rgba(13, 110, 253, 0.25);
                                }

                                .progress-indicator {
                                    animation: shimmer 2s infinite linear;
                                }

                                @keyframes shimmer {
                                    0% { opacity: 0.6; }
                                    50% { opacity: 1; }
                                    100% { opacity: 0.6; }
                                }

                                .animate-pulse {
                                    animation: pulse 2s infinite;
                                }

                                @keyframes pulse {
                                    0%, 100% { opacity: 1; }
                                    50% { opacity: 0.7; }
                                }

                                .approval-stage {
                                    transition: all 0.3s ease;
                                    border: 1px solid transparent;
                                }

                                .approval-stage:hover {
                                    transform: scale(1.05);
                                    border-color: rgba(13, 110, 253, 0.25);
                                }

                                .stage-icon {
                                    transition: all 0.3s ease;
                                }

                                .approval-stage:hover .stage-icon {
                                    transform: scale(1.1);
                                }

                                .handover-note-preview {
                                    transition: max-height 0.3s ease;
                                }

                                .handover-note-expanded {
                                    max-height: none !important;
                                }

                                .refresh-leave-status {
                                    transition: all 0.3s ease;
                                }

                                .refresh-leave-status:hover {
                                    transform: rotate(180deg);
                                }

                                .refresh-leave-status.loading {
                                    animation: spin 1s linear infinite;
                                }

                                @keyframes spin {
                                    from { transform: rotate(0deg); }
                                    to { transform: rotate(360deg); }
                                }

                                /* Responsive improvements */
                                @media (max-width: 768px) {
                                    .leave-approval-card .card-body {
                                        padding: 1rem;
                                    }

                                    .approval-stage {
                                        padding: 0.5rem;
                                    }

                                    .stage-icon {
                                        font-size: 1rem !important;
                                    }

                                    .status-badge-container {
                                        margin-top: 0.5rem;
                                        width: 100%;
                                    }

                                    .info-item {
                                        margin-bottom: 1rem;
                                    }
                                }

                                /* Appraisal Progress Card Enhancements */
                                .appraisal-progress-card {
                                    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
                                    border-radius: 15px;
                                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                                }

                                .appraisal-progress-card .card-body {
                                    padding: 2rem;
                                }

                                .appraisal-progress-card .progress {
                                    border-radius: 10px;
                                    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
                                    background-color: #e9ecef;
                                }

                                .appraisal-progress-card .progress-bar {
                                    border-radius: 10px;
                                    font-weight: 600;
                                    font-size: 0.875rem;
                                    transition: width 0.6s ease;
                                }

                                .appraisal-progress-card .alert {
                                    border-radius: 10px;
                                    border: none;
                                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                                }

                                .appraisal-progress-card .badge {
                                    padding: 0.5rem 0.75rem;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                    border-radius: 20px;
                                }

                                .appraisal-progress-card .btn {
                                    border-radius: 8px;
                                    font-weight: 500;
                                    padding: 0.5rem 1rem;
                                    transition: all 0.3s ease;
                                }

                                .appraisal-progress-card .btn:hover {
                                    transform: translateY(-2px);
                                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                                }

                                /* Timeline specific styles for appraisal */
                                .appraisal-progress-card .timeline {
                                    border-left-color: #28a745;
                                }

                                .appraisal-progress-card .timeline-item:before {
                                    border-color: #28a745;
                                }

                                /* Hover effects for timeline items */
                                .appraisal-progress-card .timeline-item {
                                    transition: all 0.3s ease;
                                    padding: 1.5rem;
                                    margin-bottom: 2rem;
                                    background: rgba(255, 255, 255, 0.8);
                                    border-radius: 12px;
                                    border-left: 4px solid transparent;
                                }

                                .appraisal-progress-card .timeline-item:hover {
                                    background: rgba(255, 255, 255, 1);
                                    border-left-color: #0d6efd;
                                    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.1);
                                    transform: translateX(5px);
                                }
                            </style>
                        @endpush

                        @push('scripts')
                            <script>
                                $(document).ready(function() {
                                    // Handover note toggle functionality
                                    $('.toggle-handover-note').on('click', function() {
                                        const preview = $(this).siblings('.handover-note-preview');
                                        const isExpanded = preview.hasClass('handover-note-expanded');

                                        if (isExpanded) {
                                            preview.removeClass('handover-note-expanded');
                                            $(this).html('<small>Show more...</small>');
                                        } else {
                                            preview.addClass('handover-note-expanded');
                                            $(this).html('<small>Show less...</small>');
                                        }
                                    });

                                    // Refresh leave status functionality
                                    $('.refresh-leave-status').on('click', function() {
                                        const button = $(this);
                                        const leaveId = button.data('leave-id');
                                        const leaveCard = button.closest('.leave-request-item');

                                        if (!leaveId) {
                                            showToast('No leave ID found', 'error');
                                            return;
                                        }

                                        // Show loading state
                                        button.addClass('loading').prop('disabled', true);
                                        const originalText = button.html();
                                        button.html('<i class="bi bi-arrow-clockwise me-1"></i>Refreshing...');

                                        // Show loading indicator
                                        $('#leaveLoadingIndicator').removeClass('d-none');

                                        // AJAX request to refresh leave status
                                        $.ajax({
                                            url: `/leaves/${leaveId}/status-refresh`,
                                            type: 'GET',
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            success: function(response) {
                                                if (response.success) {
                                                    showToast('Leave status refreshed successfully!', 'success');
                                                    // Optionally update the UI with new status
                                                    if (response.leave) {
                                                        updateLeaveCardStatus(leaveCard, response.leave);
                                                    }
                                                } else {
                                                    showToast('Failed to refresh status: ' + (response.message || 'Unknown error'), 'error');
                                                }
                                            },
                                            error: function(xhr) {
                                                let errorMessage = 'An error occurred while refreshing status.';
                                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                                    errorMessage = xhr.responseJSON.message;
                                                } else if (xhr.status === 404) {
                                                    errorMessage = 'Leave request not found.';
                                                } else if (xhr.status === 403) {
                                                    errorMessage = 'You are not authorized to view this leave request.';
                                                }
                                                showToast(errorMessage, 'error');
                                            },
                                            complete: function() {
                                                // Hide loading state
                                                button.removeClass('loading').prop('disabled', false);
                                                button.html(originalText);
                                                $('#leaveLoadingIndicator').addClass('d-none');
                                            }
                                        });
                                    });

                                    // Function to update leave card status (if needed)
                                    function updateLeaveCardStatus(leaveCard, leaveData) {
                                        // This function can be enhanced to update specific parts of the card
                                        // based on the refreshed leave data
                                        console.log('Updated leave data:', leaveData);
                                        // Add logic here to update specific status elements
                                    }

                                    // Toast notification function
                                    function showToast(message, type = 'info') {
                                        // If you have a toast library like Toastr, use it here
                                        // For now, we'll use a simple alert (you can enhance this)
                                        const colors = {
                                            success: '#28a745',
                                            error: '#dc3545',
                                            info: '#17a2b8',
                                            warning: '#ffc107'
                                        };

                                        // Create a simple toast element
                                        const toast = $(`
                                            <div class="toast-notification position-fixed" style="
                                                top: 20px;
                                                right: 20px;
                                                background: ${colors[type] || colors.info};
                                                color: white;
                                                padding: 1rem 1.5rem;
                                                border-radius: 8px;
                                                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                                                z-index: 9999;
                                                max-width: 300px;
                                                font-size: 0.9rem;
                                                animation: slideInRight 0.3s ease;
                                            ">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                                                    <span>${message}</span>
                                                </div>
                                            </div>
                                        `);

                                        $('body').append(toast);

                                        // Auto remove after 4 seconds
                                        setTimeout(() => {
                                            toast.fadeOut(() => toast.remove());
                                        }, 4000);
                                    }

                                    // Add CSS animation for toast
                                    $('<style>').prop('type', 'text/css').html(`
                                        @keyframes slideInRight {
                                            from {
                                                opacity: 0;
                                                transform: translateX(100%);
                                            }
                                            to {
                                                opacity: 1;
                                                transform: translateX(0);
                                            }
                                        }
                                    `).appendTo('head');
                                });
                            </script>
                        @endpush
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
                                    <i class="bi bi-bell-fill text-primary"></i> Latest Notifications <span
                                        class="badge bg-primary text-light">{{ $notifications->count() }}</span>
                                </h5>
                                <a href="{{ route('notifications.index') }}"
                                    class="btn btn-link btn-sm text-decoration-none">View All</a>
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
                                                            $url = route('leaves.show', ['leave' => $notification->data['leave_id']]);
                                                        }
                                                        if (isset($notification->data['training_id'])) {
                                                            $url = route('trainings.show', $notification->data['training_id']);
                                                        }
                                                        if (isset($notification->data['event_id'])) {
                                                            $url = route('events.show', $notification->data['event_id']);
                                                        }
                                                        if (isset($notification->data['appraisal_id'])) {

                                                        }
                                                        if (isset($notification->data['travel_training_id'])) {
                                                            $url = route(
                                                                'out-of-station-trainings.show',
                                                                $notification->data['travel_training_id'],
                                                            );
                                                        }
                                                        if (isset($notification->data['reminder_category'])) {
                                                            if (
                                                                $notification->data['reminder_category'] == 'appraisal'
                                                            ) {
                                                                $url = route('uncst-appraisals.index');
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
                                                                <i
                                                                    class="bi bi-dot {{ $isUnread ? 'text-primary' : 'text-secondary' }} fs-5"></i>
                                                                <span
                                                                    class="fw-semibold">{{ $notification->data['message'] }}</span>
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="bi bi-clock"></i>
                                                                {{ $notification->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                        <button class="btn-close ms-2 mt-1" aria-label="Close"
                                                            title="Mark as read & dismiss"></button>
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
<div class="modal fade" id="consent" tabindex="-1" aria-labelledby="consentLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="consentLabel">
                    <i class="bi bi-shield-check"></i> Data Protection Law
                </h5>
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
                <button type="button" class="btn btn-primary" id="applyButton">Accept</button>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <!-- Withdraw Confirmation Modal
    <div class="modal fade" id="withdrawConfirmModal" tabindex="-1" aria-labelledby="withdrawConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawConfirmModalLabel">
                        <i class="bi bi-exclamation-triangle text-warning"></i> Confirm Withdrawal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Warning:</strong> You are about to withdraw your appraisal submission.
                    </div>
                    <p>
                        This action will:
                    </p>
                    <ul>
                        <li>Remove your appraisal from the approval process</li>
                        <li>Reset the appraisal status to draft</li>
                        <li>Allow you to make further edits</li>
                        <li>Require resubmission for approval</li>
                    </ul>
                    <p class="text-muted">
                        <strong>Note:</strong> This action cannot be undone once confirmed.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <form id="withdrawForm" method="POST" action="">
                    @csrf
                    @method('POST')
                    <button type="button" class="btn btn-warning" id="confirmWithdrawBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Confirm Withdrawal
                    </button>
                        </form>
                </div>
            </div>
        </div>
    </div> -->




    @push('scripts')
        @vite(['resources/js/custom-dashboard.js'])

        <script type="module">
            $(document).ready(function() {
                var leaveTypes = {!! $leaveTypesJson !!}; // Leave type names
                var allocatedDays = {!! $chartDataJson !!}; // Allocated leave days
                var employeeData = {!! $chartEmployeeDataJson !!};
                console.log(window.isAdminOrSecretary);
                // open the consent modal here
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    console.log('jQuery ready - checking agreement');

    @if (!auth()->user()->agreed_to_data_usage)
        console.log('Showing modal via jQuery');

        // Initialize and show the modal
        $('#consent').modal({
            backdrop: 'static',
            keyboard: false
        });

        // Show it immediately
        $('#consent').modal('show');

        // Handle accept button
        $('#applyButton').click(function() {
            console.log('Accept button clicked');
            $('#consentForm').submit();
        });
    @endif
});
</script>

@section('scripts')
<script>
                // Handle accept button click
            document.getElementById('applyButton').addEventListener('click', function() {
                console.log('Accept button clicked');
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

                document.addEventListener('DOMContentLoaded', function() {
    // Set up the withdrawal modal
    const withdrawModal = document.getElementById('withdrawConfirmModal');
    const withdrawForm = document.getElementById('withdrawForm');
    let currentAppraisalId = '';

    // When modal is shown, set the form action
    withdrawModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        currentAppraisalId = button.getAttribute('data-appraisal-id');
        withdrawForm.action = `/appraisals/${currentAppraisalId}/withdraw`;
    });

    // Handle form submission
    withdrawForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('confirmWithdrawBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spinner"></i> Withdrawing...';

        // Submit the form via AJAX for better UX
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', data.message || 'Appraisal withdrawn successfully');

                // Close the modal
                const modal = bootstrap.Modal.getInstance(withdrawModal);
                modal.hide();

                // Reload the page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Failed to withdraw appraisal');
            }
        })
        .catch(error => {
            // Show error message
            showToast('error', error.message || 'Failed to withdraw appraisal: Unknown error');

            // Reset button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Toast notification function
    function showToast(type, message) {
        // You can use your preferred toast library or create a simple one
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove toast after it hides
        toast.addEventListener('hidden.bs.toast', function () {
            document.body.removeChild(toast);
        });
    }
});
</script>
@endsection
           
        </script>
    @endpush
</x-app-layout>