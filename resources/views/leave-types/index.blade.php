<x-app-layout>
    <div class="mt-3">
        <h5 class="text-center mt-5">Leave Types</h5>
        <div class="mt-3">
            <a href="{{ route('leave-types.create') }}" class="btn btn-primary">Add Leave Type</a>
        </div>

        <div class="mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaveTypes as $leaveType)
                        <tr>
                            <td>{{ $leaveType->leave_type_name }}</td>
                            <td>
                                <a href="{{ route('leave-types.edit', $leaveType->leave_type_id) }}"
                                    class="btn btn-primary">Edit</a>
                                <form method="POST"
                                    action="{{ route('leave-types.destroy', $leaveType->leave_type_id) }}"
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
