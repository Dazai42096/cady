@extends('layouts.dashboard')

@section('title', 'Create Rental')

@section('content')
<div class="max-w-5xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Create Rental</h1>
        <p class="text-slate-600 mt-1">Create a generator rental linked to a real customer and generator record.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.rentals.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold mb-2">Customer Name</label>
                <select name="customer_id" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">Select customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                            {{ $customer->company_name ?? $customer->name ?? $customer->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Generator</label>
                <select name="generator_id" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">Select generator</option>
                    @foreach ($generators as $generator)
                        <option value="{{ $generator->id }}" @selected(old('generator_id') == $generator->id)>
                            {{ $generator->serial_number ?? 'Generator' }}
                            @if (!empty($generator->model))
                                — {{ $generator->model }}
                            @endif
                            @if (!empty($generator->capacity_kva))
                                — {{ $generator->capacity_kva }} KVA
                            @endif
                            @if (!empty($generator->status))
                                — {{ ucfirst($generator->status) }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Start Date</label>
                <input type="date" name="start_date" required value="{{ old('start_date', date('Y-m-d')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">End Date</label>
                <input type="date" name="end_date" required value="{{ old('end_date') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Monthly Rate</label>
                <input type="number" step="0.001" min="0" name="monthly_rate" required value="{{ old('monthly_rate') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Currency</label>
                <select name="currency" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="JOD" @selected(old('currency') === 'JOD')>JOD</option>
                    <option value="USD" @selected(old('currency') === 'USD')>USD</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Initial Hour Meter</label>
                <input type="number" step="0.1" min="0" name="initial_hour_meter" value="{{ old('initial_hour_meter') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Notes</label>
            <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3">
            <button class="rounded-xl bg-emerald-600 text-white font-bold px-5 py-3">Create Rental</button>
            <a href="{{ route('dashboard.rentals.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">Cancel</a>
        </div>
    </form>
</div>
@endsection