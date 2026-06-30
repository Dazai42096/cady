@extends('layouts.portal')

@section('title', 'Service Report Details - CADY EST')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#0b192c]">{{ $report->report_number }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $report->service_date?->format('Y-m-d') }} — {{ $report->generator?->serial_number }}</p>
        </div>

        <a href="{{ route('portal.service-reports.pdf', $report) }}" class="rounded-xl bg-[#00d26a] text-white font-bold px-5 py-3">Download PDF</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
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
                    <div class="text-sm font-bold text-gray-500">{{ $label }}</div>
                    <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-wrap">{{ $report->{$field} }}</div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection