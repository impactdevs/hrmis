<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Impact Outsourcing">
    <meta name="generator" content="Human Resource Management System">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/font-awesome-animation@1.1.1/css/font-awesome-animation.min.css">
    <link rel="stylesheet" href="{{ asset('assets/js/jquery-ui-1.14.0/jquery-ui.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/js/sliptree-bootstrap-tokenfield-ff5b929/dist/css/bootstrap-tokenfield.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/js/sliptree-bootstrap-tokenfield-ff5b929/dist/css/tokenfield-typeahead.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/simple-datatables/style.css') }}">
    <!-- Custom styles for this template -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/custom-js.js'])
</head>

<body>
    <div class="">
        <div class="d-flex flex-row flex-1">
            @include('layouts.sidebar')

            <main class="d-flex flex-column flex-1" id="main">
                @include('layouts.header')
                {{ $slot }}
            </main>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/2.0.1/jquery.pjax.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="{{ asset('assets/js/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/js/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui-1.14.0/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/sliptree-bootstrap-tokenfield-ff5b929/dist/bootstrap-tokenfield.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Vendor JS Files -->

    @if (session()->has('success'))
        <script type="module">
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                destination: "",
                newWindow: true,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "right", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: "linear-gradient(to right, #00b09b, #96c93d)",
                },
                onClick: function() {} // Callback after click
            }).showToast();
        </script>
    @endif

    @if (session()->has('error'))
        <script type="module">
            Toastify({
                text: "{{ session('error') }}",
                duration: 3000,
                destination: "",
                newWindow: true,
                close: true,
                gravity: "top", // `top` or `bottom`
                position: "right", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                style: {
                    background: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%);",
                },
                onClick: function() {} // Callback after click
            }).showToast();
        </script>
    @endif

    <script>
        $(document).ready(function() {
            let userId = {{ auth()->user()->id }};

            // Listen for notifications
            Echo.private('App.Models.User.' + userId)
                .notification((notification) => {
                    console.log(notification);

                    var notificationItem;
                    if (notification.leave_id) {
                        // Create a new notification item
                        notificationItem = `
                                <li>
                                <a class="dropdown-item" href="/leaves/${notification.leave_id}">
                              ${notification.message}
                                </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                `;
                    }

                    if (notification.training_id) {
                        // Create a new notification item
                        notificationItem = `
                                <li>
                                <a class="dropdown-item" href="/trainings/${notification.training_id}">
                                ${notification.message}
                                </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                `;
                    }

                    if (notification.event_id) {
                        // Create a new notification item
                        notificationItem = `
                                <li>
                                <a class="dropdown-item" href="/events/${notification.event_id}">
                                ${notification.message}
                                </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                `;
                    }

                    // Append the notification to the dropdown
                    $('.notifications').prepend(notificationItem);

                    // Update badge count
                    let badgeCount = parseInt($('#notification-badge').text());
                    $('#notification-badge').text(badgeCount + 1);

                    // Update dropdown header
                    const currentHeader = $('.dropdown-header span.badge');
                    const newCount = badgeCount + 1;
                    currentHeader.text(newCount + ' new notifications');
                });
        });
    </script>
    @stack('scripts')
</body>

</html>
