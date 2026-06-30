<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->report_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        h2 { font-size: 15px; margin-top: 22px; border-bottom: 1px solid #ddd; padding-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td, th { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        .muted { color: #6b7280; }
        .box { border: 1px solid #ddd; padding: 10px; min-height: 45px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>CADY EST Service Report</h1>
    <div class="muted">Report Number: {{ $report->report_number }}</div>

    <h2>Report Information</h2>
    <table>
        <tr>
            <th>Customer</th>
            <td>{{ $report->customer?->company_name ?? '-' }}</td>
            <th>Generator</th>
            <td>{{ $report->generator?->serial_number ?? '-' }}</td>
        </tr>
        <tr>
            <th>Service Date</th>
            <td>{{ $report->service_date?->format('Y-m-d') }}</td>
            <th>Technician</th>
            <td>{{ $report->technician_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Type</th>
            <td>{{ ucfirst($report->report_type) }}</td>
            <th>Status</th>
            <td>{{ ucfirst($report->status) }}</td>
        </tr>
        <tr>
            <th>Rental</th>
            <td>{{ $report->rental?->ref_number ?? '-' }}</td>
            <th>Contract</th>
            <td>{{ $report->maintenanceContract?->ref_number ?? '-' }}</td>
        </tr>
    </table>

    @foreach ([
        'fault_description' => 'Fault Description',
        'diagnosis' => 'Diagnosis',
        'mechanical_work' => 'Mechanical Work',
        'electrical_work' => 'Electrical Work',
        'spare_parts' => 'Spare Parts',
        'technician_notes' => 'Technician Notes',
        'recommended_follow_up' => 'Recommended Follow Up'
    ] as $field => $label)
        <h2>{{ $label }}</h2>
        <div class="box">{{ $report->{$field} ?: '-' }}</div>
    @endforeach
<script src="{{ asset('js/cady-i18n.js') }}?v=20260701"></script>
</body>
</html>