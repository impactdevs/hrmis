<x-app-layout>
    <section class="section dashboard m-2">
        @if (auth()->user()->isAdminOrSecretary())
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-8">
                    <div class="row">

                        <!-- Sales Card -->
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
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card revenue-card">

                                <div class="card-body">
                                    <h5 class="card-title">Attendees <span>| Today</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div class="ps-3">
                                            <h6>{{ $attendances }}</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Revenue Card -->

                        <!-- Customers Card -->
                        <div class="col-xxl-3 col-xl-12">

                            <div class="card info-card customers-card">

                                <div class="card-body">
                                    <h5 class="card-title">Leave <span>| Currently</span></h5>

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

                        </div><!-- End Customers Card -->

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

                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
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
                                        <tbody>
                                            @foreach ($entries as $entry)
                                                @php
                                                    // Assuming $response contains the JSON string
                                                    $data = json_decode($entry->entry->responses, true); // true for associative array
                                                @endphp
                                                <tr>
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
                        </div><!-- End Recent Sales -->

                        <!-- Ongoing Appraisals -->
                        <div class="col-12">
                            <div class="card top-selling overflow-auto">

                                <div class="card-body pb-0">
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
                        </div><!-- End Top Selling -->

                        <div class="col-12">
                            <div class="card">


                                <div id="svg-tree">

                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- End Left side columns -->

                <!-- Right side columns -->
                <div class="col-lg-4">

                    <!-- Budget Report -->
                    <div class="card">
                        <div class="card-body pb-0">
                            <h5 class="card-title">Allocated Leave Analysis <span>| This Month</span></h5>

                            <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                        </div>
                    </div><!-- End Budget Report -->

                    <!-- Website Traffic -->
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

                        <div class="card-body pb-0">
                            <h5 class="card-title">Employee Distribution</h5>

                            <div id="trafficChart" style="min-height: 400px;" class="echart"></div>


                        </div>
                    </div><!-- End Website Traffic -->

                    <!-- Events -->
                    <div class="card">
                        <div class="card-body pb-0">
                            <h5 class="card-title">Events &amp; Trainings <span>| Ongoing, Due Today & Tomorrow</span>
                            </h5>

                            <div class="news">
                                @foreach ($events as $event)
                                    <div class="post-item clearfix">
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
                                    <div class="post-item clearfix">
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
                                    <h5 class="card-title">Leaves</h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $totalLeaves }}</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">

                                <div class="card-body">
                                    <h5 class="card-title">Leave Days Used <span>| This Year</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $totalDays }}</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Revenue Card -->

                        <!-- Customers Card -->
                        <div class="col-xxl-4 col-xl-12">

                            <div class="card info-card customers-card">

                                <div class="card-body">
                                    <h5 class="card-title">Ongoing Appraisals</h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ $ongoingAppraisals }}</h6>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div><!-- End Customers Card -->
                        @if (count($leaveApprovalData) > 0)
                            <!-- Leave Approval Progress -->
                            <div class="col-xxl-12 col-md-12">
                                <div class="card info-card leave-approval-card border border-5 border-primary">
                                    <div class="card-body">
                                        @foreach ($leaveApprovalData as $leaveData)
                                            <div class="leave-approval-item">
                                                <h6>Leave Requests</h6>

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
                        @if ($daysUntilExpiry <= 90)
                            <!-- Revenue Card -->
                            <div class="col-xxl-12 col-md-12">
                                @if ($daysUntilExpiry >= 0 && $daysUntilExpiry != null)
                                    <div class="alert alert-warning mt-3">
                                        <h5>Contract Expiry Notification</h5>
                                        <p>Your contract is expiring in <strong>{{ $daysUntilExpiry }}
                                                days</strong>.</p>
                                        <div class="countdown"
                                            data-expiry="{{ auth()->user()->employee->contract_expiry_date }}">
                                        </div>
                                    </div>
                                @endif
                                @if ($daysUntilExpiry < 0)
                                    <div class="alert alert-danger">
                                        <h5>Contract Expired</h5>
                                        <p>Your contract expired <strong>{{ abs($daysUntilExpiry) }} days
                                                ago</strong>.</p>
                                    </div>
                                @endif

                            </div><!-- End Revenue Card -->
                        @endif

                    </div>
                </div>

                <!-- Right side columns -->
                <div class="col-lg-4">
                    <!-- Budget Report -->
                    <div class="card">
                        <div class="card-body pb-0">
                            <h5 class="card-title">Allocated Leave Analysis <span>| This Month</span></h5>

                            <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

                        </div>
                    </div><!-- End Budget Report -->
                    <!-- Events -->
                    <div class="card">
                        <div class="card-body pb-0">
                            <h5 class="card-title">Events &amp; Trainings <span>| Ongoing, Due Today & Tomorrow</span>
                            </h5>

                            <div class="news">
                                @foreach ($events as $event)
                                    <div class="post-item clearfix">
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
                                    <div class="post-item clearfix">
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




                </div><!-- End Right side columns -->

            </div>
        @endif
    </section>


    @push('scripts')
        @vite(['resources/js/custom-dashboard.js'])

        <script type="module">
            $(document).ready(function() {
                var leaveTypes = {!! $leaveTypesJson !!}; // Leave type names
                var allocatedDays = {!! $chartDataJson !!}; // Allocated leave days
                var employeeData = {!! $chartEmployeeDataJson !!};

                console.log(employeeData)

                console.log(leaveTypes, allocatedDays)

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

                echarts.init(document.querySelector("#trafficChart")).setOption({
                    title: {
                        text: 'Number of Employees per Department',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        top: '5%',
                        left: 'center'
                    },
                    series: [{
                        name: 'Employees by Department',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: true,
                            position: 'outside'
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '18',
                                fontWeight: 'bold'
                            }
                        },
                        labelLine: {
                            show: true
                        },
                        data: employeeData
                    }]
                });
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

                //hierachy chart
                const data = {
                    id: 'ms',
                    data: {
                        imageURL: 'https://i.pravatar.cc/300?img=68',
                        name: 'Margret Swanson',
                    },
                    options: {
                        nodeBGColor: '#cdb4db',
                        nodeBGColorHover: '#cdb4db',
                    },
                    children: [{
                            id: 'mh',
                            data: {
                                imageURL: 'https://i.pravatar.cc/300?img=69',
                                name: 'Mark Hudson',
                            },
                            options: {
                                nodeBGColor: '#ffafcc',
                                nodeBGColorHover: '#ffafcc',
                            },
                            children: [{
                                    id: 'kb',
                                    data: {
                                        imageURL: 'https://i.pravatar.cc/300?img=65',
                                        name: 'Karyn Borbas',
                                    },
                                    options: {
                                        nodeBGColor: '#f8ad9d',
                                        nodeBGColorHover: '#f8ad9d',
                                    },
                                },
                                {
                                    id: 'cr',
                                    data: {
                                        imageURL: 'https://i.pravatar.cc/300?img=60',
                                        name: 'Chris Rup',
                                    },
                                    options: {
                                        nodeBGColor: '#c9cba3',
                                        nodeBGColorHover: '#c9cba3',
                                    },
                                },
                            ],
                        },
                        {
                            id: 'cs',
                            data: {
                                imageURL: 'https://i.pravatar.cc/300?img=59',
                                name: 'Chris Lysek',
                            },
                            options: {
                                nodeBGColor: '#00afb9',
                                nodeBGColorHover: '#00afb9',
                            },
                            children: [{
                                    id: 'Noah_Chandler',
                                    data: {
                                        imageURL: 'https://i.pravatar.cc/300?img=57',
                                        name: 'Noah Chandler',
                                    },
                                    options: {
                                        nodeBGColor: '#84a59d',
                                        nodeBGColorHover: '#84a59d',
                                    },
                                },
                                {
                                    id: 'Felix_Wagner',
                                    data: {
                                        imageURL: 'https://i.pravatar.cc/300?img=52',
                                        name: 'Felix Wagner',
                                    },
                                    options: {
                                        nodeBGColor: '#0081a7',
                                        nodeBGColorHover: '#0081a7',
                                    },
                                },
                            ],
                        },
                    ],
                };
                const options = {
                    contentKey: 'data',
                    width: 800,
                    height: 600,
                    nodeWidth: 150,
                    nodeHeight: 100,
                    fontColor: '#fff',
                    borderColor: '#333',
                    childrenSpacing: 50,
                    siblingSpacing: 20,
                    direction: 'top',
                    nodeTemplate: (content) =>
                        `<div style='display: flex;flex-direction: column;gap: 10px;justify-content: center;align-items: center;height: 100%;'>
          <img style='width: 50px;height: 50px;border-radius: 50%;' src='${content.imageURL}' alt='' />
          <div style="font-weight: bold; font-family: Arial; font-size: 14px">${content.name}</div>
         </div>`,
                    enableToolbar: false,
                };
                const tree = new ApexTree(document.getElementById('svg-tree'), options);
                tree.render(data);

            });

            //real notifications
        </script>
    @endpush
</x-app-layout>
