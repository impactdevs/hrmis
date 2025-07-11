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
                        </div>

                        @if (!auth()->user()->hasRole('Staff'))
                            <!-- Ongoing Appraisals Card -->
                            <div class="col-xxl-4 col-md-6">
                                <div class="card info-card customers-card">
                                    <div class="card-body">
                                        <h5 class="card-title">To The H.R</h5>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-arrow-repeat text-primary"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $submittedAppraisalsByHoD->count() }}</h6>Appraisals submitted to
                                                the
                                                H.R</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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
                                            <h6>{{ $submittedAppraisalsByHR->count() }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Approved By E.S</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="col-xxl-4 col-md-6">
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
                                                            $url = url(
                                                                'uncst-appraisals',
                                                                $notification->data['appraisal_id'],
                                                            );
                                                        }
                                                        if (isset($notification->data['travel_training_id'])) {
                                                            $url = url(
                                                                'out-of-station-trainings',
                                                                $notification->data['travel_training_id'],
                                                            );
                                                        }
                                                        if (isset($notification->data['reminder_category'])) {
                                                            if (
                                                                $notification->data['reminder_category'] == 'appraisal'
                                                            ) {
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
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">Submitted Appraisals</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-hourglass-split text-warning"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $appraisals->count() }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Appraisals submitted to the
                                                H.o.D</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ongoing Appraisals Card -->
                        <div class="col-xxl-4 col-md-6">
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
                                            <h6>{{ $submittedAppraisalsByHR->count() }}</h6>
                                            <span class="pt-2 text-muted small ps-1">Approved By E.S</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                                                <div class="col-xxl-4 col-md-6">
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
                                    <div class="card-body">
                                        <h5 class="mb-4 fw-bold text-primary">
                                            <i class="bi bi-calendar-check"></i> My Recent Leave Requests
                                        </h5>
                                        <ul class="timeline list-unstyled">
                                            @foreach ($leaveApprovalData as $leaveData)
                                                <li class="timeline-item mb-5 position-relative ps-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span
                                                            class="badge bg-primary me-2">{{ $leaveData['leave_type_name'] ?? 'Leave' }}</span>
                                                        <span
                                                            class="fw-semibold">{{ \Carbon\Carbon::parse($leaveData['start_date'])->format('d M') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($leaveData['end_date'])->format('d M, Y') }}</span>
                                                        @if ($leaveData['esStatus'] === 'Approved')
                                                            <span class="badge bg-success ms-2">Approved</span>
                                                        @elseif ($leaveData['status'] === 'Rejected')
                                                            <span class="badge bg-danger ms-2">Rejected</span>
                                                        @else
                                                            <span
                                                                class="badge bg-warning text-dark ms-2">Pending</span>
                                                        @endif
                                                    </div>
                                                    <div class="ms-1 mb-2">
                                                        <small>
                                                            <i class="bi bi-chat-left-text"></i>
                                                            <strong>Reason:</strong> {{ $leaveData['reason'] ?? '-' }}
                                                        </small>
                                                    </div>
                                                    <div class="ms-1 mb-2">
                                                        <small>
                                                            <i class="bi bi-person-check"></i>
                                                            <strong>Handover:</strong>
                                                            @if (!empty($leaveData['my_work_will_be_done_by']))
                                                                {{ is_array($leaveData['my_work_will_be_done_by']) ? implode(', ', $leaveData['my_work_will_be_done_by']) : $leaveData['my_work_will_be_done_by'] }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <div class="ms-1 mb-2">
                                                        <small>
                                                            <i class="bi bi-telephone"></i>
                                                            <strong>Contact:</strong>
                                                            {{ $leaveData['phone_number'] ?? '-' }}
                                                        </small>
                                                    </div>
                                                    <!-- Approval Progress Bar -->
                                                    <div class="progress my-3" style="height: 8px;">
                                                        <div class="progress-bar bg-{{ $leaveData['hrStatus'] === 'Approved' ? 'success' : ($leaveData['hrStatus'] === 'Rejected' ? 'danger' : 'warning') }}"
                                                            role="progressbar" style="width: 33%;" aria-valuenow="33"
                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                        <div class="progress-bar bg-{{ strtolower($leaveData['hodStatus']) === 'approved' ? 'success' : (strtolower($leaveData['hodStatus']) === 'rejected' ? 'danger' : 'warning') }}"
                                                            role="progressbar" style="width: 33%;" aria-valuenow="33"
                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                        <div class="progress-bar bg-{{ $leaveData['esStatus'] === 'Approved' ? 'success' : ($leaveData['esStatus'] === 'Rejected' ? 'danger' : 'warning') }}"
                                                            role="progressbar" style="width: 34%;" aria-valuenow="34"
                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between text-center small mb-2">
                                                        <span>
                                                            <i class="bi bi-person-badge"></i>
                                                            HR<br>
                                                            @if ($leaveData['hrStatus'] === 'Approved')
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @elseif ($leaveData['hrStatus'] === 'Rejected')
                                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                            @else
                                                                <i class="bi bi-hourglass-split text-warning"></i>
                                                            @endif
                                                        </span>
                                                        <span>
                                                            <i class="bi bi-person-workspace"></i>
                                                            HOD<br>
                                                            @if (strtolower($leaveData['hodStatus']) === 'approved' || strtolower($leaveData['hodStatus']) === 'apprroved')
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @elseif (strtolower($leaveData['hodStatus']) === 'rejected')
                                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                            @else
                                                                <i class="bi bi-hourglass-split text-warning"></i>
                                                            @endif
                                                        </span>
                                                        <span>
                                                            <i class="bi bi-person-lines-fill"></i>
                                                            Executive<br>
                                                            @if ($leaveData['esStatus'] === 'Approved')
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            @elseif ($leaveData['esStatus'] === 'Rejected')
                                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                            @else
                                                                <i class="bi bi-hourglass-split text-warning"></i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <!-- Status & Actions -->
                                                    @if ($leaveData['esStatus'] === 'Approved')
                                                        <div
                                                            class="alert alert-success py-2 px-3 mt-2 mb-0 d-flex align-items-center gap-2">
                                                            <i class="bi bi-emoji-laughing fs-4"></i>
                                                            <div>
                                                                <strong>Congratulations!</strong> Your leave is fully
                                                                approved.
                                                                @if ($leaveData['daysRemaining'] == 'Leave has not started')
                                                                    <span class="d-block">Leave has not started
                                                                        yet.</span>
                                                                @else
                                                                    <span class="d-block">Days remaining:
                                                                        <strong>{{ $leaveData['daysRemaining'] }}</strong></span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @elseif ($leaveData['status'] === 'Rejected' && isset($leaveData['rejection_reason']))
                                                        <div class="alert alert-danger py-2 px-3 mt-2 mb-0">
                                                            <strong>Rejected:</strong>
                                                            {{ $leaveData['rejection_reason'] }}
                                                        </div>
                                                    @elseif ($leaveData['status'] === 'Pending')
                                                        <div class="alert alert-warning py-2 px-3 mt-2 mb-0">
                                                            <span class="fw-semibold">Pending Approval</span>
                                                        </div>
                                                    @endif
                                                    <!-- Handover Note Download & Text -->
                                                    <div class="d-flex flex-wrap gap-3 mt-2">
                                                        @if (!empty($leaveData['handover_note_file']))
                                                            <a href="{{ asset('storage/' . $leaveData['handover_note_file']) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                                target="_blank">
                                                                <i class="bi bi-file-earmark-arrow-down"></i> Handover
                                                                Note
                                                            </a>
                                                        @endif
                                                        @if (!empty($leaveData['handover_note']))
                                                            <span
                                                                class="badge bg-light text-dark border border-primary">
                                                                <i class="bi bi-journal-text"></i>
                                                                {{ $leaveData['handover_note'] }}
                                                            </span>
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
                            </style>
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
                                                            $url = url('leaves', $notification->data['leave_id']);
                                                        }
                                                        if (isset($notification->data['training_id'])) {
                                                            $url = url('trainings', $notification->data['training_id']);
                                                        }
                                                        if (isset($notification->data['event_id'])) {
                                                            $url = url('events', $notification->data['event_id']);
                                                        }
                                                        if (isset($notification->data['appraisal_id'])) {
                                                            $url = url(
                                                                'uncst-appraisals',
                                                                $notification->data['appraisal_id'],
                                                            );
                                                        }
                                                        if (isset($notification->data['travel_training_id'])) {
                                                            $url = url(
                                                                'out-of-station-trainings',
                                                                $notification->data['travel_training_id'],
                                                            );
                                                        }
                                                        if (isset($notification->data['reminder_category'])) {
                                                            if (
                                                                $notification->data['reminder_category'] == 'appraisal'
                                                            ) {
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
