<x-app-layout>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Permissions</h1>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary mb-2">
                <i class="fas fa-plus"></i> Create Permission
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Permissions List</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
