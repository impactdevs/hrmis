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
            <table class="table table-striped" data-toggle="table" data-search="true" data-show-columns="true"
                data-show-export="true" data-show-pagination-switch="true"
                data-page-list="[20, 25, 50, 100, 500, 1000, 2000, 10000, all]" data-pagination="true">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Employee</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Repayment Start</th>
                        <th class="text-center">Repayment End</th>
                        <th class="text-center">Contract Expiry</th>
                        <th class="text-center">Net Monthly Pay</th>
                        <th class="text-center">Outstanding Loan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
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
                            <td>{{ number_format($advance->amount_applied_for) }}</td>
                            <td>{{ $advance->repayment_start_date ? \Carbon\Carbon::parse($advance->repayment_start_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ $advance->repayment_end_date ? \Carbon\Carbon::parse($advance->repayment_end_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ $advance->date_of_contract_expiry ? \Carbon\Carbon::parse($advance->date_of_contract_expiry)->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ number_format($advance->net_monthly_pay) }}</td>
                            <td>{{ number_format($advance->outstanding_loan) }}</td>
                            <td>
                                @php
                                    $user = Auth::user();
                                    $userRole = $user->roles->pluck('name')[0] ?? '';
                                    if (
                                        $userRole === 'Head of Division' &&
                                        isset($user->employee->department) &&
                                        strtoupper(trim($user->employee->department->department_name)) ===
                                            'FINANCE DEPARTMENT'
                                    ) {
                                        $userRole = 'Finance Department';
                                    }
                                    $statuses = $advance->loan_request_status ?? [];
                                    $approvalOrder = ['HR', 'Finance Department', 'Executive Secretary'];
                                    $currentStep = null;
                                    foreach ($approvalOrder as $role) {
                                        if (empty($statuses[$role]) || $statuses[$role] === 'pending') {
                                            $currentStep = $role;
                                            break;
                                        }
                                    }
                                    $status = $statuses[$userRole] ?? null;
                                @endphp

                                <div class="d-flex align-items-center justify-content-center gap-2 flex-nowrap"
                                    style="min-width: 320px;">
                                    @foreach ($approvalOrder as $idx => $role)
                                        @php
                                            $roleStatus = $statuses[$role] ?? 'pending';
                                            $isCurrent = $currentStep === $role;
                                        @endphp
                                        <div class="d-flex flex-column align-items-center mx-1" style="width: 60px;">
                                            <div class="rounded-circle
                                                @if ($roleStatus === 'approved') bg-success text-white
                                                @elseif($roleStatus === 'rejected') bg-danger text-white
                                                @elseif($isCurrent) bg-warning text-dark
                                                @else bg-light text-secondary @endif
                                                d-flex justify-content-center align-items-center"
                                                style="width: 18px; height: 18px; font-size: 0.85rem; border: 1px solid #ccc;">
                                                @if ($roleStatus === 'approved')
                                                    <i class="bi bi-check-lg" style="font-size:0.8em;"></i>
                                                @elseif($roleStatus === 'rejected')
                                                    <i class="bi bi-x-lg" style="font-size:0.8em;"></i>
                                                @elseif($isCurrent)
                                                    <i class="bi bi-hourglass-split" style="font-size:0.8em;"></i>
                                                @else
                                                    <span style="font-size:0.8em;">{{ $idx + 1 }}</span>
                                                @endif
                                            </div>
                                            <small class="text-center"
                                                style="font-size: 0.75em; width: 60px; white-space: normal;">{{ $role }}</small>
                                        </div>
                                        @if ($idx < count($approvalOrder) - 1)
                                            <div style="width: 32px; height: 2px; background: #ccc;"></div>
                                        @endif
                                    @endforeach
                                </div>
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
