<x-app-layout>
<div class="container">
    <h1>Edit Roles for {{ $user->name }}</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="roles">Assign Roles</label><br>
            @foreach($roles as $role)
                <div class="form-check">
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                           id="role_{{ $role->id }}"
                           class="form-check-input"
                           {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Update Roles</button>
    </form>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-2">Back to Users</a>
</div>
</x-app-layout>
