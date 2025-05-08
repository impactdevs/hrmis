<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4">
            <i class="fas fa-calendar-alt"></i> Event Details
        </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3 form-group">
                    <label for="event_name">
                        <i class="fas fa-tag"></i> Event Name
                    </label>
                    <p class="p-2 border rounded bg-light">{{ $event->event_title }}</p>
                </div>
                <div class="mb-3 form-group">
                    <label for="event_start_date">
                        <i class="fas fa-clock"></i> Event Start Date
                    </label>
                    <p class="p-2 border rounded bg-light">{{ $event->event_start_date->toDateString() }}</p>
                </div>
                <div class="mb-3 form-group">
                    <label for="event_end_date">
                        <i class="fas fa-clock"></i> Event End Date
                    </label>
                    <p class="p-2 border rounded bg-light">{{ $event->event_end_date->toDateString() }}</p>
                </div>
                <div class="mb-3 form-group">
                    <label for="event_description">
                        <i class="fas fa-info-circle"></i> Event Description
                    </label>
                    <p class="p-2 border rounded bg-light">{{ $event->event_description }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3 form-group">
                    <label for="category">
                        <i class="fas fa-tags"></i> Event Categories
                    </label>
                    <ul class="list-group">

                        @php
                            // Assume training_category contains comma-separated IDs for each category
                            $userIds = explode(',', $event->category['users'] ?? '');
                            $departmentIds = explode(',', $event->category['departments'] ?? '');
                            $positionIds = explode(',', $event->category['positions'] ?? '');

                        @endphp
                        @foreach ($userIds as $id)
                            @if ($id === 'All')
                                <span class="badge bg-primary">All Users</span>
                            @elseif (!empty($id) && isset($options['users'][$id]))
                                <span class="badge bg-primary">{{ $options['users'][$id] ?? 'Unknown User' }}</span>
                            @endif
                        @endforeach



                        @foreach ($departmentIds as $id)
                            @if (filled($id))
                                <span
                                    class="badge bg-success">{{ $options['departments'][$id] == '' ? '' : $options['departments'][$id] ?? 'Unknown Department' }}</span>
                            @endif
                        @endforeach

                        @foreach ($positionIds as $id)
                            @if (filled($id))
                                <span
                                    class="badge bg-info">{{ $options['positions'][$id] == '' ? '' : $options['positions'][$id] ?? 'Unknown Position' }}</span>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
