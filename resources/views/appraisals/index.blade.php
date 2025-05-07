<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-10xl mx-auto">
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Performance Appraisals</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage employee performance evaluations</p>
                </div>
                @if (!auth()->user()->isAdminOrSecretary)
                    <a href="{{ route('appraisals.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Appraisal
                    </a>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4">
                    @if (auth()->user()->isAdminOrSecretary)
                        <!-- Table Layout for Admins/Secretaries -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Table content remains the same as original -->
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            #ID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Employee</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Position</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">







                                    @if (filled($appraisals))
                                        @foreach ($appraisals as $appraisal)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    #{{ $loop->iteration }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="ml-4">
                                                            <div
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $appraisal->employee->first_name }}
                                                                {{ $appraisal->employee->last_name }}
                                                            </div>
                                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $appraisal->employee->email }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ optional($appraisal->employee->position)->position_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $approvedBy = collect($appraisal->appraisal_request_status)
                                                            ->filter(fn($status) => $status === 'approved')
                                                            ->keys();

                                                        $rejectedBy = collect($appraisal->appraisal_request_status)
                                                            ->filter(fn($status) => $status === 'rejected')
                                                            ->keys();
                                                    @endphp

                                                    <div class="space-y-1">
                                                        @if ($approvedBy->isNotEmpty())
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 mr-1 text-green-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                <span
                                                                    class="text-sm text-green-600 dark:text-green-400">Approved
                                                                    by {{ $approvedBy->join(', ') }}</span>
                                                            </div>
                                                        @endif

                                                        @if ($rejectedBy->isNotEmpty())
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 mr-1 text-red-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                                <span
                                                                    class="text-sm text-red-600 dark:text-red-400">Rejected
                                                                    by {{ $rejectedBy->join(', ') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end space-x-3">

                                                        <a href="{{ route('appraisals.edit', $appraisal->appraisal_id) }}"
                                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-500">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </a>
                                                        <form
                                                            action="{{ route('appraisals.destroy', $appraisal->appraisal_id) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-500"
                                                                onclick="return confirm('Are you sure?')">
                                                                <svg class="w-5 h-5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- Card Layout for Regular Users -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @if (filled($appraisals))
                                @foreach ($appraisals as $appraisal)
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ $appraisal->employee->first_name }}
                                                    {{ $appraisal->employee->last_name }}
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $appraisal->employee->email }}</p>
                                            </div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ optional($appraisal->employee->position)->position_name }}
                                            </span>
                                        </div>

                                        <div class="space-y-2 mb-4">
                                            @php
                                                $approvedBy = collect($appraisal->appraisal_request_status)
                                                    ->filter(fn($status) => $status === 'approved')
                                                    ->keys();
                                                $rejectedBy = collect($appraisal->appraisal_request_status)
                                                    ->filter(fn($status) => $status === 'rejected')
                                                    ->keys();
                                            @endphp

                                            @if ($approvedBy->isNotEmpty())
                                                <div
                                                    class="flex items-center text-sm text-green-600 dark:text-green-400">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Approved by {{ $approvedBy->join(', ') }}
                                                </div>
                                            @endif

                                            @if ($rejectedBy->isNotEmpty())
                                                <div class="flex items-center text-sm text-red-600 dark:text-red-400">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Rejected by {{ $rejectedBy->join(', ') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('appraisals.edit', $appraisal->appraisal_id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-500"
                                                title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            @if (auth()->user()->employee->user_id == $appraisal->employee->user_id)
                                                <form
                                                    action="{{ route('appraisals.destroy', $appraisal->appraisal_id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-500"
                                                        onclick="return confirm('Are you sure?')" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No appraisals found.</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Pagination (for both layouts) -->
                    <div class="mt-4 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        {!! $appraisals->appends(['search' => request()->get('search')])->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
