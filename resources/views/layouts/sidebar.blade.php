<div class="sidebar bg-primary border-start border-start-5 shadow rounded-start-5 border-dashed mobile-menu col-12 col-md-3 col-lg-2 col-xl-2"
    style="position: sticky; top: 0; height: 100vh; overflow-y: auto;">
    <div class="border-bottom border-bottom-5 h-25 d-flex align-items-center justify-content-center">
        <img src="{{ asset('assets/img/logo.png') }}" alt="company logo"
            class="object-fit-contain border rounded img-fluid" style="max-width: 100%; height: auto;">
    </div>
    <div class="d-md-flex flex-column p-0 pt-lg-3">
        <h6
            class="text-uppercase px-3 text-body-secondary text-light d-flex justify-content-between align-items-center my-3">
            <span class="text-light">GENERAL</span>
            <i class="bi bi-bar-chart text-light"></i>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('dashboard')) bg-secondary @endif"
                    href="{{ route('dashboard') }}">
                    <i class="bi bi-bar-chart"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('leave-roster.index') || request()->routeIs('leave-roster-tabular.index')) bg-secondary @endif"
                    href="{{ route('leave-roster.index') }}">
                    <i class="bi bi-calendar-plus"></i>
                    {{ auth()->user()->isAdminOrSecretary ? 'Leave Roster' : 'My Leave Schedule' }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('leaves.index')) bg-secondary @endif"
                    href="{{ route('leaves.index') }}">
                    <i class="bi bi-bus-front"></i>
                    {{ auth()->user()->isAdminOrSecretary ? 'Staff Leave Requests' : 'Apply For Leave' }}
                </a>
            </li>


            @if (auth()->user()->isAdminOrSecretary)
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('leave-management')) bg-secondary @endif"
                        href="{{ route('leave-management') }}">
                        <i class="bi bi-card-checklist"></i>
                        Leave Management
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('uncst-appraisals.index') || request()->routeIs('uncst-appraisals.edit')) bg-secondary @endif"
                    href="{{ route('uncst-appraisals.index') }}">
                    <i class="bi bi-arrow-bar-up"></i>
                    {{ auth()->user()->isAdminOrSecretary ? 'Appraisals' : 'My Appraisals' }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('attendances.index')) bg-secondary @endif"
                    href="{{ route('attendances.index') }}">
                    <i class="bi bi-check2-all"></i>
                    {{ auth()->user()->isAdminOrSecretary ? 'Attendances' : 'My Attendance History' }} </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('salary-advances.index')) bg-secondary @endif"
                    href="{{ route('salary-advances.index') }}">
                    <i class="bi bi-currency-dollar"></i>
                    {{ auth()->user()->isAdminOrSecretary ? 'Salary Advances' : 'My Salary Advances' }} </a>
            </li>


            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('trainings.index') ||
                        request()->routeIs('trainings.show') ||
                        request()->routeIs('trainings.edit') ||
                        request()->routeIs('trainings.create') ||
                        request()->routeIs('out-of-station-trainings.index') ||
                        request()->routeIs('out-of-station-trainings.create') ||
                        request()->routeIs('out-of-station-trainings.edit') ||
                        request()->routeIs('out-of-station-trainings.show') ||
                        request()->routeIs('apply')) bg-secondary @endif"
                    href="{{ route('trainings.index') }}">
                    <i class="bi bi-eyedropper"></i>
                    Trainings/Travels
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('events.index') || request()->routeIs('events.show')) bg-secondary @endif"
                    href="{{ route('events.index') }}">
                    <i class="bi bi-calendar-check"></i>
                    Events
                </a>
            </li>

            <li class="nav-item">
                @php
                    $currentUrl = auth()->user()->isAdminOrSecretary ? 'employees.index' : 'employees.show';
                @endphp
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs($currentUrl)) bg-secondary @endif"
                    href="{{ auth()->user()->isAdminOrSecretary ? route('employees.index') : route('employees.show', auth()->user()->employee->employee_id) }}">
                    <i class="bi bi-database-down"></i>
                    {{ auth()->user()->isAdminOrSecretary ? 'Employees' : 'About Me' }}
                </a>
            </li>
            @if (auth()->user()->hasRole('HR'))
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('uncst-job-applications.index')) bg-secondary @endif"
                        href="{{ route('uncst-job-applications.index') }}">
                        <i class="bi bi-activity"></i>
                        Applications
                    </a>
                </li>
            @endif
            @if (auth()->user()->isAdminOrSecretary)
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('recruitments.index') ||
                            request()->routeIs('recruitments.show') ||
                            request()->routeIs('recruitments.edit') ||
                            request()->routeIs('recruitments.create')) bg-secondary @endif"
                        href="{{ route('recruitments.index') }}">
                        <i class="bi bi-bank2"></i>
                        Staff Recruitment
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasRole('Staff'))
                <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('whistleblower.create')) bg-secondary @endif"
                    href="{{ route('whistleblower.create') }}">
                    <i class="fas fa-exclamation-triangle"></i>
                    Make Complaint
                </a>
            </li>
            @endif

            @if (auth()->user()->hasRole('HR'))
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('whistleblower.index') || request()->routeIs('whistleblower.show')) bg-secondary @endif"
                        href="{{ route('whistleblower.index') }}">
                        <i class="fas fa-user-secret text-white-600"></i>
                        WhistleBlower
                    </a>
                </li>
            @endif

                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('workfromhome.index') || request()->routeIs('workfromhome.show')) bg-secondary @endif"
                        href="{{ route('workfromhome.index') }}">
                        <i class="bi bi-house-check"></i>
                        Work From Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('offdesk.index') || request()->routeIs('offdesk.show')) bg-secondary @endif"
                        href="{{ route('offdesk.index') }}">
                        <i class="bi bi-briefcase"></i>
                        Off Desk
                    </a>
                </li>
        </ul>
        @if (auth()->user()->hasRole('HR'))
            <h6
                class="text-uppercase px-3 text-body-secondary text-light d-flex justify-content-between align-items-center my-3">
                <span class="text-light">Settings</span>
                <i class="bi bi-gear text-light"></i>
            </h6>
            <ul class="nav flex-column mb-auto">
                @foreach ([['route' => 'leave-types.index', 'icon' => 'bi-gear', 'label' => 'Leave Types'], ['route' => 'public_holidays.index', 'icon' => 'bi-gear', 'label' => 'Public Holidays'], ['route' => 'company-jobs.index', 'icon' => 'bi-gear', 'label' => 'Company Jobs'], ['route' => 'positions.index', 'icon' => 'bi-gear', 'label' => 'Positions'], ['route' => 'roles.index', 'icon' => 'bi-gear', 'label' => 'Roles', 'sub' => [['route' => 'permissions.index', 'icon' => 'bi-gear', 'label' => 'Permissions'], ['route' => 'users.index', 'icon' => 'bi-gear', 'label' => 'User Management']]], ['route' => 'departments.index', 'icon' => 'bi-gear', 'label' => 'Departments']] as $item)
                    <li class="nav-item">
                        <a class="nav-link text-white d-flex align-items-center gap-2 fs-6 fw-bold @if (request()->routeIs($item['route'])) bg-secondary @endif"
                            href="{{ route($item['route']) }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                            {{ $item['label'] }}
                        </a>
                        @if (!empty($item['sub']))
                            <ul class="nav flex-column ms-4">
                                @foreach ($item['sub'] as $subItem)
                                    <li class="nav-item">
                                        <a class="nav-link text-white d-flex align-items-center gap-2 fs-6 fw-bold @if (request()->routeIs($subItem['route'])) bg-secondary @endif"
                                            href="{{ route($subItem['route']) }}">
                                            <i class="bi {{ $subItem['icon'] }}"></i>
                                            {{ $subItem['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <h6
            class="text-uppercase px-3 text-body-secondary text-light d-flex justify-content-between align-items-center my-3">
            <span class="text-light">DOCUMENTATION</span>
            <i class="bi bi-gear bi-book"></i>
        </h6>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-6 fw-bold @if (request()->routeIs('docs')) bg-secondary @endif"
                    href="/docs">
                    <i class="bi bi-book"></i>
                    User Manual
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-6 fw-bold @if (request()->routeIs('uncst-matrix')) bg-secondary @endif"
                    href="{{ route('uncst-matrix') }}">
                    <i class="bi bi-book"></i>
                    Requirements Matrix
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-6 fw-bold"
                    href="https://drive.google.com/drive/folders/1Wj9XKIKxhxGoNwrPDN3I8FiHRdNEPP1X?usp=drive_link"
                    target="_blank" rel="noopener">
                    <i class="bi bi-book"></i>
                    Video tutorials
                </a>
            </li>

            @if (auth()->user()->hasRole('HR'))
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-6 fw-bold"
                        href="https://drive.google.com/drive/folders/1-2FfImKXDZMAjhO0hNe9N7s1hjOF045Q?usp=drive_link"
                        target="_blank" rel="noopener">
                        <i class="bi bi-book"></i>
                        Configuration Video tutorials
                    </a>
                </li>
            @endif
        </ul>

        </h6>
    </div>
</div>
