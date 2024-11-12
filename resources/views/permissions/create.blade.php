<x-app-layout>
    <div class="container mt-4">
        <h1 class="h2 mb-4">Create Permission</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Permission Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Permission Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Create Permission</button>
                </form>
            </div>
        </div>

        <a href="{{ route('permissions.index') }}" class="btn btn-secondary mt-3">Back to Permissions</a>
    </div>
</x-app-layout>
