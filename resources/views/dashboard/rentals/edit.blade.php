@extends('layouts.dashboard')

@section('title', 'Edit Rental')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Edit Rental {{ $rental->ref_number }}</h1>
        <p class="text-slate-600 mt-1">Update rental details and recalculate the rental total.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.rentals.update', $rental) }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Customer</label>
                <select name="customer_id" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $rental->customer_id) === $customer->id)>
                            {{ $customer->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Generator</label>
                <select name="generator_id" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    @foreach ($generators as $generator)
                        <option value="{{ $generator->id }}" @selected(old('generator_id', $rental->generator_id) === $generator->id)>
                            {{ $generator->serial_number }} — {{ $generator->model }} — {{ $generator->capacity_kva }} KVA
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', $rental->start_date?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date', $rental->end_date?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Monthly Rate</label>
                <input type="number" step="0.001" min="0" name="monthly_rate" value="{{ old('monthly_rate', $rental->monthly_rate) }}" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Currency</label>
                <select name="currency" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    @foreach (['JOD', 'USD', 'EUR'] as $currency)
                        <option value="{{ $currency }}" @selected(old('currency', $rental->currency) === $currency)>{{ $currency }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Initial Hour Meter</label>
                <input type="number" min="0" name="initial_hour_meter" value="{{ old('initial_hour_meter', $rental->initial_hour_meter) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Notes</label>
            <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3">{{ old('notes', $rental->notes) }}</textarea>
        </div>

        <div class="flex gap-3">
            <button class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3">
                Save Changes
            </button>

            <a href="{{ route('dashboard.rentals.show', $rental) }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection