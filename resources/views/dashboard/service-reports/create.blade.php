@extends('layouts.dashboard')

@section('title', 'Create Service Report')

@section('content')
<div class="max-w-5xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Create Service Report</h1>
        <p class="text-slate-600 mt-1">Link the report to customer portal data and generate a PDF.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('dashboard.service-reports.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold mb-2">Customer</label>
                <select name="customer_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">Select customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Generator</label>
                <select name="generator_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">Select generator</option>
                    @foreach ($generators as $generator)
                        <option value="{{ $generator->id }}">{{ $generator->serial_number }} — {{ $generator->model }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Related Rental</label>
                <select name="rental_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">None</option>
                    @foreach ($rentals as $rental)
                        <option value="{{ $rental->id }}">{{ $rental->ref_number }} — {{ $rental->customer?->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Related Contract</label>
                <select name="maintenance_contract_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">None</option>
                    @foreach ($contracts as $contract)
                        <option value="{{ $contract->id }}">{{ $contract->ref_number }} — {{ $contract->customer?->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Report Type</label>
                <select name="report_type" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    @foreach (['maintenance', 'rental', 'emergency', 'inspection', 'support'] as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Service Date</label>
                <input type="date" name="service_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Technician Name</label>
                <input type="text" name="technician_name" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div class="flex items-center gap-3 pt-8">
                <input type="checkbox" name="customer_visible" value="1" class="rounded">
                <span class="text-sm font-bold text-slate-700">Visible to customer portal after submit/approval</span>
            </div>
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
            <div>
                <label class="block text-sm font-bold mb-2">{{ $label }}</label>
                <textarea name="{{ $field }}" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3">{{ old($field) }}</textarea>
            </div>
        @endforeach

        <div class="flex gap-3">
            <button class="rounded-xl bg-emerald-600 text-white font-bold px-5 py-3">Create Report</button>
            <a href="{{ route('dashboard.service-reports.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">Cancel</a>
        </div>
    </form>
</div>
@endsection