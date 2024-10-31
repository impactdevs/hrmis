<div class="sidebar bg-primary border-start border-start-5 shadow rounded-start-5 border-dashed"
    style="position: sticky; top: 0; height: 100vh; overflow-y: auto;">
    <div class="border-bottom border-bottom-5 h-25 d-flex align-items-center justify-content-center">
        <img src="{{ asset('assets/img/logo.png') }}" alt="company logo"
            class="object-fit-contain border rounded img-fluid" style="max-width: 100%; height: auto;">
    </div>
    <div class="d-md-flex flex-column p-0 pt-lg-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('dashboard')) bg-secondary @endif"
                    href="{{ route('dashboard') }}">
                    <i class="bi bi-bar-chart"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('leaves.index')) bg-secondary @endif"
                    href="{{ route('leaves.index') }}">
                    <i class="bi bi-arrow-bar-up"></i>
                    {{ auth()->user()->hasRole('Super Admin') ? 'Leaves' : 'My Leaves' }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('appraisals.index')) bg-secondary @endif"
                    href="{{ route('appraisals.index') }}">
                    <i class="bi bi-arrow-bar-up"></i>
                    {{ auth()->user()->hasRole('Super Admin') ? 'Appraisals' : 'My Appraisals' }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('attendances.index')) bg-secondary @endif"
                    href="{{ route('attendances.index') }}">
                    <i class="bi bi-check2-all"></i>
                    {{ auth()->user()->hasRole('Super Admin') ? 'Attendances' : 'My Attendance History' }}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('trainings.index') || request()->routeIs('trainings.show')) bg-secondary @endif"
                    href="{{ route('trainings.index') }}">
                    <i class="bi bi-eyedropper"></i>
                    Trainings
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
                    $currentUrl = auth()->user()->hasRole('Super Admin') ? 'employees.index' : 'employees.show';
                @endphp
                <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs($currentUrl)) bg-secondary @endif"
                    href="{{ auth()->user()->hasRole('Super Admin') ? route('employees.index') : route('employees.show', auth()->user()->employee->employee_id) }}">
                    <i class="bi bi-database-down"></i>
                    {{ auth()->user()->hasRole('Super Admin') ? 'Employees' : 'About Me' }}
                </a>
            </li>
            @if (auth()->user()->hasRole('Super Admin'))
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs('applications.index')) bg-secondary @endif"
                        href="{{ route('applications.index') }}">
                        <i class="bi bi-activity"></i>
                        Applications
                    </a>
                </li>
            @endif
        </ul>
        @if (auth()->user()->hasRole('Super Admin'))
            <h6
                class="text-uppercase px-3 text-body-secondary text-light d-flex justify-content-between align-items-center my-3">
                <span class="text-light">Settings</span>
                <i class="bi bi-gear text-light"></i>
            </h6>
            <ul class="nav flex-column mb-auto">
                @foreach ([['route' => 'leave-types.index', 'icon' => 'bi-gear', 'label' => 'Leave Types'], ['route' => 'company-jobs.index', 'icon' => 'bi-gear', 'label' => 'Company Jobs'], ['route' => 'positions.index', 'icon' => 'bi-gear', 'label' => 'Positions'], ['route' => 'roles.index', 'icon' => 'bi-gear', 'label' => 'Roles', 'sub' => [['route' => 'permissions.index', 'icon' => 'bi-gear', 'label' => 'Permissions'], ['route' => 'users.index', 'icon' => 'bi-gear', 'label' => 'User']]], ['route' => 'departments.index', 'icon' => 'bi-gear', 'label' => 'Departments']] as $item)
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
    </div>
</div>
