<x-app-layout>
    <div class="container">
        <h1>Create Role</h1>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Role Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Role</button>
        </form>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-2">Back to Roles</a>
    </div>
</x-app-layout>
