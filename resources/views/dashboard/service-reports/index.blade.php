@extends('layouts.dashboard')

@section('title', 'Service Reports')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Service Reports</h1>
            <p class="text-slate-600 mt-1">Create customer-linked service reports and PDF downloads.</p>
        </div>

        <a href="{{ route('dashboard.service-reports.create') }}" style="background:#059669;color:white;padding:12px 20px;border-radius:12px;font-weight:700;text-decoration:none;display:inline-block;">
            New Service Report
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <form method="GET" action="{{ route('dashboard.service-reports.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col md:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search report, customer, generator..." class="flex-1 rounded-xl border border-slate-300 px-4 py-2">
        <select name="status" class="rounded-xl border border-slate-300 px-4 py-2">
            <option value="">All statuses</option>
            @foreach (['draft', 'submitted', 'approved'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-slate-900 text-white font-bold px-5 py-2">Filter</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Report</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Generator</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        <tr>
                            <td class="px-4 py-4 font-mono font-bold">{{ $report->report_number }}</td>
                            <td class="px-4 py-4">{{ $report->customer?->company_name ?? '-' }}</td>
                            <td class="px-4 py-4">{{ $report->generator?->serial_number ?? '-' }}</td>
                            <td class="px-4 py-4">{{ $report->service_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-bold">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('dashboard.service-reports.show', $report) }}" class="rounded-lg bg-slate-900 text-white text-xs font-bold px-3 py-2">View</a>
                                    <a href="{{ route('dashboard.service-reports.pdf', $report) }}" class="rounded-lg bg-blue-600 text-white text-xs font-bold px-3 py-2">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">No service reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200">{{ $reports->links() }}</div>
    </div>
</div>
@endsection