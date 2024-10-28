<x-app-layout>
    <div class="container">
        <h1>Edit Role</h1>
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Role Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $role->name }}"
                    required>
            </div>

            <h5 class="mt-4">Manage Permissions:</h5>
            <div class="form-group">
                @foreach ($allPermissions as $permission)
                    <div class="form-check">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            class="form-check-input" id="permission-{{ $permission->id }}"
                            @if ($role->hasPermissionTo($permission)) checked @endif>
                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Update Role</button>
        </form>

        <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-2">Back to Roles</a>
    </div>
</x-app-layout>
