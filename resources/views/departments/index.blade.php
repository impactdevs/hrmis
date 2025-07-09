<x-app-layout>
    <div class="mt-3" id="departments">
        <h5 class="text-center mt-5">Departments</h5>
        <div class="mt-3">
            <a href="{{ route('departments.create') }}" class="btn btn-primary">Add Department</a>
        </div>

        <div class="mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Department Head</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td>{!! $department->department_name !!}</td>
                            <td>{{ optional($department->user)->name }}</td>
                            <td>
                                <a href="{{ route('departments.edit', $department->department_id) }}"
                                    class="btn btn-primary">Edit</a>
                                <form method="POST"
                                    action="{{ route('departments.destroy', $department->department_id) }}"
                                    accept-charset="UTF-8" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm(&quot;Are you sure?&quot;)">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
</x-app-layout>
