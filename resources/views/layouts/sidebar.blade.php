<div class="sidebar bg-primary border-start border-start-5 shadow rounded-start-5 border-dashed"
    style="position: sticky; top: 0; height: 100vh; overflow-y: auto;">
    <div class="border-bottom border-bottom-5 h-25 d-flex align-items-center justify-content-center">
        <img src="{{ asset('assets/img/logo.png') }}" alt="company logo"
            class="object-fit-contain border rounded img-fluid" style="max-width: 100%; height: auto;">
    </div>
    <div class="d-md-flex flex-column p-0 pt-lg-3">
        <ul class="nav flex-column">
            @foreach ([['route' => 'dashboard', 'icon' => 'bi-bar-chart', 'label' => 'Dashboard'], ['route' => 'employees.index', 'icon' => 'bi-list-check', 'label' => 'Employees'], ['route' => 'events.index', 'icon' => 'bi-calendar-check', 'label' => 'Events'], ['route' => 'trainings.index', 'icon' => 'bi-book', 'label' => 'Trainings'], ['route' => 'appraisals.index', 'icon' => 'bi-search', 'label' => 'Appraisals'], ['route' => 'applications.index', 'icon' => 'bi-send', 'label' => 'Applications', ['route' => 'entries.index', 'icon' => 'bi-journal-text', 'label' => 'Applicants']], ['route' => 'attendances.index', 'icon' => 'bi-vector-pen', 'label' => 'Attendances'], ['route' => 'leaves.index', 'icon' => 'bi-bus-front', 'label' => 'Leave']] as $item)
                <li class="nav-item">
                    <a class="nav-link text-white d-flex align-items-center gap-2 fs-5 fw-bold @if (request()->routeIs($item['route'])) bg-secondary @endif"
                        href="{{ route($item['route']) }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        {{ $item['label'] }}
                    </a>
                    @if (!empty($item['sub']))
                        <ul class="nav flex-column ms-4">
                            @foreach ($item['sub'] as $subItem)
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center gap-2 fw-bold @if (request()->routeIs($subItem['route'])) bg-secondary @endif"
                                        href="{{ route($subItem['route']) }}" data-transition>
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
