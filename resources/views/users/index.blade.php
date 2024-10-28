<x-app-layout>
    <h1>Users</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-2">Create User</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach ($user->getRoleNames() as $role)
                            <span class="badge text-bg-info">{{ $role }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Edit Roles</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</x-app-layout>
