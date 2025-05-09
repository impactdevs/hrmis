<x-app-layout>
    <div class="py-12">
        <!-- Page Heading -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Recruitment Requests</h3>
            @cannot('can approve recruitment')
                <a href="{{ route('recruitments.create') }}" class="btn btn-primary">
                    <i class="bi bi-database-add"></i> Apply for Recruitment
                </a>
            @endcannot
        </div>

        <!-- Recruitment Requests Table -->
        <div class="shadow-sm card">
            <div class="card-body">
                @if ($rectrutmentRequests->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        No recruitment requests found.
                    </div>
                @else
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Position</th>
                                <th scope="col">Department</th>
                                <th scope="col">Approval Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rectrutmentRequests as $recruitment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $recruitment->position }}</td>
                                    <td>{{ $recruitment->department->department_name }}</td>
                                    <td class="align-middle">
                                        @if (!is_null($recruitment->approval_status))
                                            @foreach ($recruitment->approval_status as $key => $status)
                                                <span>{{ $key . '-' . ucfirst($status) }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('recruitments.edit', $recruitment->staff_recruitment_id) }}">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                </li>

                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('recruitments.show', $recruitment->staff_recruitment_id) }}">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </li>

                                                <li>
                                                    <form
                                                        action="{{ route('recruitments.destroy', $recruitment->staff_recruitment_id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this recruitment request?')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <div class="mt-3">
                        {{ $rectrutmentRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
