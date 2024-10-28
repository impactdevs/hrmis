<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4">
            <i class="fas fa-calendar-alt"></i> Training Details
        </h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="training_name">
                        <i class="fas fa-tag"></i> Training Name
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_title }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_start_date">
                        <i class="fas fa-clock"></i> Training Start Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_start_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_end_date">
                        <i class="fas fa-clock"></i> Training End Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_end_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="training_description">
                        <i class="fas fa-info-circle"></i> Training Description
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $training->training_description }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="category">
                        <i class="fas fa-tags"></i> Training Categories
                    </label>
                    <ul class="list-group">
                        @foreach ($training->training_category as $category)
                            <li class="list-group-item">{{ $category }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
