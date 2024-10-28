<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4">
            <i class="fas fa-calendar-alt"></i> Event Details
        </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="event_name">
                        <i class="fas fa-tag"></i> Event Name
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $event->event_title }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="event_start_date">
                        <i class="fas fa-clock"></i> Event Start Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $event->event_start_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="event_end_date">
                        <i class="fas fa-clock"></i> Event End Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $event->event_end_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="event_description">
                        <i class="fas fa-info-circle"></i> Event Description
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $event->event_description }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="category">
                        <i class="fas fa-tags"></i> Event Categories
                    </label>
                    <ul class="list-group">
                        @foreach ($event->category as $category)
                            <li class="list-group-item">{{ $category }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
