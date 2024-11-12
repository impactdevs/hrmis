<x-app-layout>
    <div class="container mt-4">
        <h1 class="h2 mb-4">User Management</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Users List</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->getRoleNames() as $role)
                                        <span class="badge text-bg-info">{{ $role }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Edit Roles</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
