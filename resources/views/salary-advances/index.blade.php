<x-app-layout>
    <div class="mt-3">
        <div class="flex-row flex-1 d-flex justify-content-between">
            @can('can add salary advance')
                <div>
                    <a href="{{ route('salary-advances.create') }}" class="btn border-t-neutral-50 btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Salary Advance
                    </a>
                </div>
            @endcan
        </div>

        <div class="table-wrapper mt-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Amount Applied For</th>
                        <th>Reasons</th>
                        <th>Repayment Start</th>
                        <th>Repayment End</th>
                        <th>Contract Expiry</th>
                        <th>Net Monthly Pay</th>
                        <th>Outstanding Loan</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salarydvances as $index => $advance)
                        <tr>
                            <td>{{ $salarydvances->firstItem() + $index }}</td>
                            <td>
                                {{-- Assuming you have a relation or can access employee name --}}
                                {{ $advance->employee->first_name . ' ' . $advance->employee->first_name }}
                            </td>
                            <td>{{ number_format($advance->amount_applied_for, 2) }}</td>
                            <td>{{ $advance->reasons }}</td>
                            <td>{{ $advance->repayment_start_date ? \Carbon\Carbon::parse($advance->repayment_start_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ $advance->repayment_end_date ? \Carbon\Carbon::parse($advance->repayment_end_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ $advance->date_of_contract_expiry ? \Carbon\Carbon::parse($advance->date_of_contract_expiry)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ number_format($advance->net_monthly_pay, 2) }}</td>
                            <td>{{ number_format($advance->outstanding_loan, 2) }}</td>
                            <td>
                                @if (!is_null($advance->loan_request_status))
                                    <div class="status m-2">
                                        @foreach ($advance->loan_request_status as $key => $status)
                                            <span
                                                class="status-{{ $key }}">{{ $key }}-{{ $status }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        {{-- @can('can edit salary advance')
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('salary-advances.edit', $advance->id) }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </li>
                                        @endcan --}}
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('salary-advances.edit', $advance->id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </li>
                                        @can('can delete salary advance')
                                            <li>
                                                <form action="{{ route('salary-advances.destroy', $advance->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-danger">
                                No salary advance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrapper">
                {!! $salarydvances->links() !!}
            </div>
        </div>
    </div>
</x-app-layout>
