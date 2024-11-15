<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4">
            <i class="fas fa-calendar-alt"></i> Leave Details
        </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="leave_name">
                        <i class="fas fa-tag"></i> Leave Name
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->leave_title }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_start_date">
                        <i class="fas fa-clock"></i> Leave Start Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->start_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_end_date">
                        <i class="fas fa-clock"></i> Leave End Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->end_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_description">
                        <i class="fas fa-info-circle"></i> Leave Description
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leave->leave_description }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="category">
                        <i class="fas fa-tags"></i> Leave Categories
                    </label>
                    <ul class="list-group">
                        @foreach ($leave->leaveory as $category)
                            <li class="list-group-item">{{ $category }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
