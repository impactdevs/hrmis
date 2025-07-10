<x-app-layout>
<div class="w-full px-4 mx-auto py-10">
    <div class="bg-white shadow rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Whistleblowing Reports</h2>
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if($reports->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submission Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Submitted</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-mono">{{ $report->tracking_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->submission_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $report->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('whistleblowing.show', $report->id) }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-xs font-semibold">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $reports->links() }}</div>
        @else
            <div class="text-center text-gray-500 py-12">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h3m4 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V6a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                <p class="mt-4">No whistleblowing reports found.</p>
            </div>
        @endif
    </div>
</div>
</x-app-layout>