<x-app-layout>
    <div class="mt-3">
        <div class="mt-3">
            <a href="{{ route('company-jobs.create') }}" class="btn btn-primary">Add Job</a>
        </div>

        <div class="mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Job Code</th>
                        <th>Role</th>
                        <th>Will Become Active On</th>
                        <th>Will Become Inactive On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companyJobs as $companyJob)
                        <tr>
                            <th>{{ $companyJob->job_code }}</th>
                            <td>{{ $companyJob->job_title }}</td>
                            <td>
                                {{ $companyJob->will_become_active_at ? $companyJob->will_become_active_at->format('Y-m-d H:i') : 'N/A' }}
                            </td>
                            <td>
                                {{ $companyJob->will_become_inactive_at ? $companyJob->will_become_inactive_at->format('Y-m-d H:i') : 'N/A' }}
                            </td>
                            <td>
                               {{-- use a switch statement to display status depending on will_become_active_at and  will_become_inactive_at, expect pending, running, expired--}}
                                @if ($companyJob->will_become_active_at && $companyJob->will_become_inactive_at)
                                    @if (now()->isBefore($companyJob->will_become_active_at))
                                        Pending
                                    @elseif (now()->isBetween($companyJob->will_become_active_at, $companyJob->will_become_inactive_at))
                                        Running
                                    @else
                                        Expired
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('company-jobs.edit', $companyJob->company_job_id) }}"
                                    class="btn btn-primary">Edit</a>
                                <form method="POST" action="{{ route('company-jobs.destroy', $companyJob->company_job_id) }}"
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
    </div>
</x-app-layout>
