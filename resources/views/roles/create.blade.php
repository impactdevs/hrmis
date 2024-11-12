<x-app-layout>
    <div class="container mt-4">
        <h1 class="h2">Create Role</h1>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Role Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group mt-3">
                <label>Tick which Permissions you want to assign to this <strong>Role</strong></label>
                @foreach ($permissions as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="permission{{ $permission->id }}"
                            name="permissions[]" value="{{ $permission->id }}">
                        <label class="form-check-label" for="permission{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-4">Create Role</button>
        </form>
    </div>
</x-app-layout>
