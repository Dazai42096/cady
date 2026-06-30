@extends('layouts.portal')

@section('title', 'Service Reports - CADY EST')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <h1 class="text-3xl font-extrabold text-[#0b192c]">My Service Reports</h1>
        <p class="text-gray-500 text-sm mt-1">View and download service reports linked to your company account.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-gray-50 text-gray-500 font-bold">
                <tr>
                    <th class="px-4 py-3">Report</th>
                    <th class="px-4 py-3">Generator</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($reports as $report)
                    <tr>
                        <td class="px-4 py-3 font-mono font-bold">{{ $report->report_number }}</td>
                        <td class="px-4 py-3">{{ $report->generator?->serial_number ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $report->service_date?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ ucfirst($report->status) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('portal.service-reports.show', $report) }}" class="text-blue-600 font-bold">View</a>
                            |
                            <a href="{{ route('portal.service-reports.pdf', $report) }}" class="text-emerald-600 font-bold">PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No service reports available.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-gray-100">{{ $reports->links() }}</div>
    </div>
</div>
@endsection