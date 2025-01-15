<x-app-layout>
    <div class="mt-3" id="departments">
        <h5 class="text-center mt-5">Public Holidays</h5>
        <div class="mt-3">
            <a href="{{ route('public_holidays.create') }}" class="btn btn-primary">Add Public Holiday</a>
        </div>

        <div class="mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Holiday Name</th>
                        <th>Holiday Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($holidays as $holiday)
                        <tr>
                            <td>{{ $holiday->id }}</td>
                            <td>{{ $holiday->holiday_name }}</td>
                            <td>{{ $holiday->holiday_date }}</td>
                            <td>
                                <a href="{{ route('public_holidays.edit', $holiday->id) }}"
                                    class="btn btn-primary">Edit</a>
                                <form method="POST"
                                    action="{{ route('public_holidays.destroy', $holiday->id) }}"
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
