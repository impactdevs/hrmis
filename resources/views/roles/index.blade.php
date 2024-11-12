<x-app-layout>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Roles</h1>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Role
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Roles List</h5>
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
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmDelete('{{ route('roles.destroy', $role) }}')">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = '@csrf @method('DELETE')';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
