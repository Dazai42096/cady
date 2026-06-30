@extends('layouts.dashboard')

@section('title', 'Service Report Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Service Report {{ $report->report_number }}</h1>
            <p class="text-slate-600 mt-1">{{ $report->customer?->company_name }} — {{ $report->generator?->serial_number }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('dashboard.service-reports.pdf', $report) }}" class="rounded-xl bg-blue-600 text-white font-bold px-5 py-3">Download PDF</a>
            <a href="{{ route('dashboard.service-reports.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">Back</a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <div class="grid md:grid-cols-3 gap-4">
            <div><div class="text-sm text-slate-500">Status</div><div class="font-bold">{{ ucfirst($report->status) }}</div></div>
            <div><div class="text-sm text-slate-500">Type</div><div class="font-bold">{{ ucfirst($report->report_type) }}</div></div>
            <div><div class="text-sm text-slate-500">Service Date</div><div class="font-bold">{{ $report->service_date?->format('Y-m-d') }}</div></div>
            <div><div class="text-sm text-slate-500">Customer</div><div class="font-bold">{{ $report->customer?->company_name ?? '-' }}</div></div>
            <div><div class="text-sm text-slate-500">Generator</div><div class="font-bold">{{ $report->generator?->serial_number ?? '-' }}</div></div>
            <div><div class="text-sm text-slate-500">Technician</div><div class="font-bold">{{ $report->technician_name ?? '-' }}</div></div>
        </div>

        @foreach ([
            'fault_description' => 'Fault Description',
            'diagnosis' => 'Diagnosis',
            'mechanical_work' => 'Mechanical Work',
            'electrical_work' => 'Electrical Work',
            'spare_parts' => 'Spare Parts',
            'technician_notes' => 'Technician Notes',
            'recommended_follow_up' => 'Recommended Follow Up'
        ] as $field => $label)
            @if ($report->{$field})
                <div>
                    <div class="text-sm font-bold text-slate-500">{{ $label }}</div>
                    <div class="bg-slate-50 rounded-xl p-4 whitespace-pre-wrap">{{ $report->{$field} }}</div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-wrap gap-3">
        @if ($report->status === 'draft')
            <form method="POST" action="{{ route('dashboard.service-reports.submit', $report) }}">
                @csrf
                <button class="rounded-xl bg-emerald-600 text-white font-bold px-5 py-3">Submit + Show in Portal</button>
            </form>
        @endif

        @if ($report->status !== 'approved')
            <form method="POST" action="{{ route('dashboard.service-reports.approve', $report) }}">
                @csrf
                <button class="rounded-xl bg-slate-900 text-white font-bold px-5 py-3">Approve</button>
            </form>
        @endif
    </div>
</div>
@endsection