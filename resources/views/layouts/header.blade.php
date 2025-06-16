<!-- ======= Header ======= -->
<header id="header" class="navbar sticky-top header fixed-top d-flex align-items-center">
    <div class="flex-row d-flex flex-grow-1 align-items-center">
        <!-- Add this toggle button before the sidebar -->
        <button class="mt-2 navbar-toggler d-md-none position-fixed start-0 no-print" type="button" id="sidebarToggle">
            <span class="navbar-toggler-icon"></span>
        </button>

        <p class="text-primary fw-bold fs-4 ms-5">
            @php
                $title = '';

                if (request()->routeIs('dashboard')) {
                    $title = 'Dashboard';
                }

                if (request()->routeIs('leaves.index')) {
                    $title = auth()->user()->isAdminOrSecretary ? 'Staff Leave Requests' : 'Apply For Leave';
                }

                if (request()->routeIs('leaves.create')) {
                    $title = auth()->user()->isAdminOrSecretary ? 'Staff Leave Requests' : 'Apply For Leave';
                }

                if (request()->routeIs('leave-roster.index') || request()->routeIs('leave-roster-tabular.index')) {
                    $title = auth()->user()->isAdminOrSecretary ? 'Leave Roster' : 'My Leave Schedule';
                }

                if (request()->routeIs('leave-management')) {
                    $title = 'Leave Management';
                }

                if (request()->routeIs('uncst-appraisals.index')) {
                    $title = auth()->user()->isAdminOrSecretary ? 'Appraisals' : 'My Appraisals';
                }
                if (request()->routeIs('uncst-appraisals.edit')) {
                    $appraisal = request()->route('uncst_appraisal'); // This is likely a model, not an ID
                    $title =
                        'Appraisal for ' . $appraisal->employee->first_name . ' ' . $appraisal->employee->last_name;
                }

                if (request()->routeIs('appraisals.create')) {
                    $title = 'Applying for an Appraisal';
                }

                if (request()->routeIs('attendances.index')) {
                    $title = auth()->user()->isAdminOrSecretary ? 'Attendances' : 'My Attendance History';
                }

                if (request()->routeIs('contract.create')) {
                    $title = 'Add contract';
                }

                                if (request()->routeIs('notifications.index')) {
                    $title = 'Software Notifications';
                }

                if (request()->routeIs('contract.edit')) {
                    $title = 'Edit contract';
                }

                if (request()->routeIs('contract.show')) {
                    $title = 'Contract Details';
                }

                if (
                    request()->routeIs('trainings.index') ||
                    request()->routeIs('trainings.show') ||
                    request()->routeIs('trainings.edit') ||
                    request()->routeIs('trainings.create') ||
                    request()->routeIs('out-of-station-trainings.index') ||
                    request()->routeIs('out-of-station-trainings.create') ||
                    request()->routeIs('out-of-station-trainings.edit') ||
                    request()->routeIs('out-of-station-trainings.show') ||
                    request()->routeIs('apply')
                ) {
                    $title = 'Trainings/Travels';
                }

                if (request()->routeIs('events.index') || request()->routeIs('events.show')) {
                    $title = 'Events';
                }

                if (request()->routeIs('events.create')) {
                    $title = 'Create an Event';
                }

                if (request()->routeIs('events.edit')) {
                    $title = 'Edit an Event';
                }

                if (request()->routeIs('employees.index')) {
                    $title = auth()->user()->isAdminOrSecretary ? 'Employees' : 'About Me';
                }

                if (request()->routeIs('employees.show')) {
                    $title = auth()->user()->isAdminOrSecretary ? 'Employees' : 'About Me';
                }

                if (request()->routeIs('employees.create')) {
                    $title = 'Add An Employee';
                }

                if (request()->routeIs('uncst-job-applications.index')) {
                    $title = 'Applications';
                }

                if (
                    request()->routeIs('recruitments.index') ||
                    request()->routeIs('recruitments.show') ||
                    request()->routeIs('recruitments.edit') ||
                    request()->routeIs('recruitments.create')
                ) {
                    $title = 'Staff Recruitment';
                }

                if (request()->routeIs('leave-types.index')) {
                    $title = 'Leave Types';
                }

                if (request()->routeIs('company-jobs.index')) {
                    $title = 'Company Jobs';
                }

                if (request()->routeIs('positions.index')) {
                    $title = 'Positions';
                }

                if (request()->routeIs('roles.index')) {
                    $title = 'Roles';
                }

                if (request()->routeIs('permissions.index')) {
                    $title = 'Permissions';
                }

                if (request()->routeIs('users.index')) {
                    $title = 'User Management';
                }

                if (request()->routeIs('departments.index')) {
                    $title = 'Departments';
                }
            @endphp

            {{ $title }}
        </p>
    </div>


    <nav class="header-nav ms-auto no-print">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown">
                <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <span class="badge bg-primary badge-number" id="notification-badge">0</span>
                </a>
                <!-- Notification Dropdown -->
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                    <li class="dropdown-header">
                        You have <span id="notification-count">0</span> new notifications
                        <a href="/notifications"><span class="p-2 badge rounded-pill bg-primary ms-2">View
                                all</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    @if (auth()->user()->employee->passport_photo)
                        <img src="{{ asset('storage/' . auth()->user()->employee->passport_photo) }}"
                            alt="Passport Photo" class="img-fluid rounded-circle" width="70%">
                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ auth()->user()->name }}</span>
                    @else
                        <img src="/assets/img/profile.jpg" alt="Profile" class="rounded-circle">
                    @endif
                </a>
                <!-- Profile Dropdown -->
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ auth()->user()->name }}</h6>
                        <span>{{ optional(optional(auth()->user()->employee)->position)->position_name }}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav><!-- End Icons Navigation -->
</header><!-- End Header -->

@push('scripts')
    <script>
        $(document).ready(function() {
            // Function to fetch the unread notification count
            function fetchNotificationCount() {
                $.ajax({
                    url: '/get-count',
                    type: 'GET',
                    success: function(data) {
                        $('#notification-badge').text(data.count);
                        console.log(data)
                        $('#notification-count').text(data.count);
                    }.bind(this), // Bind 'this' to access the clicked element
                    error: function(xhr) {
                        console.error('Error:', xhr);
                    }
                });
            }

            // Fetch the count initially
            fetchNotificationCount();
        });
    </script>
@endpush
