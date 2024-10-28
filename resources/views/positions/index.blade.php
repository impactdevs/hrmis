<x-app-layout>
    <div class="mt-3">
        <h5 class="text-center mt-5">Positions</h5>
        <div class="mt-3">
            <a href="{{ route('positions.create') }}" class="btn btn-primary">Add Position</a>
        </div>

        <div class="mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($positions as $position)
                        <tr>
                            <td>{{ $position->position_name }}</td>
                            <td>
                                <a href="{{ route('positions.edit', $position->position_id) }}"
                                    class="btn btn-primary">Edit</a>
                                <form method="POST"
                                    action="{{ route('positions.destroy', $position->position_id) }}"
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
