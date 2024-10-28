<x-app-layout>
    <section class="section dashboard m-2">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-12">
                <div class="row">
                    @if ($notifications->isNotEmpty())
                        <ul class="list-group">
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
                                @endphp
                                <li class="list-group-item notification-item m-2 d-flex justify-content-between"
                                    data-url="{{ $url }}" data-id="{{ $notification->id }}"
                                    data-type="{{ $notification->type }}">
                                    <div>
                                        {{ $notification->data['message'] }}
                                        <small
                                            class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>

                                    <button class="btn-close" aria-label="Close"></button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No new notifications.</p>
                    @endif
                </div>
            </div><!-- End Left side columns -->
        </div>
    </section>

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
                                notificationItem.addClass(
                                    'list-group-item-success'); // Optionally add a success class
                                notificationItem.fadeOut(); // Optionally fade out the notification
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
</x-app-layout>
