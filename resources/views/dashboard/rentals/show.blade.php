@extends('layouts.dashboard')

@section('title', 'Rental Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Rental {{ $rental->ref_number }}</h1>
            <p class="text-slate-600 mt-1">{{ $rental->customer?->company_name }} — {{ $rental->generator?->serial_number }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('dashboard.rentals.export', $rental) }}" class="rounded-xl bg-indigo-600 text-white font-bold px-4 py-2">
                Export CSV
            </a>

            <a href="{{ route('dashboard.rentals.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-4 py-2">
                Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Status</div>
            <div class="text-xl font-bold text-slate-900">{{ ucfirst($rental->status) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Rental Days</div>
            <div class="text-xl font-bold text-slate-900">{{ $rental->calculated_days }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Monthly Rate</div>
            <div class="text-xl font-bold text-slate-900">{{ number_format((float) $rental->monthly_rate, 3) }} {{ $rental->currency }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Total Amount</div>
            <div class="text-xl font-bold text-slate-900">{{ number_format((float) $rental->total_amount, 3) }} {{ $rental->currency }}</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            <h2 class="text-lg font-bold text-slate-900">Rental Information</h2>

            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-slate-500">Customer</div>
                    <div class="font-bold text-slate-900">{{ $rental->customer?->company_name ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-slate-500">Generator</div>
                    <div class="font-bold text-slate-900">{{ $rental->generator?->serial_number ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-slate-500">Start Date</div>
                    <div class="font-bold text-slate-900">{{ $rental->start_date?->format('Y-m-d') }}</div>
                </div>

                <div>
                    <div class="text-slate-500">End Date</div>
                    <div class="font-bold text-slate-900">{{ $rental->end_date?->format('Y-m-d') }}</div>
                </div>

                <div>
                    <div class="text-slate-500">Initial Hour Meter</div>
                    <div class="font-bold text-slate-900">{{ $rental->initial_hour_meter }}</div>
                </div>

                <div>
                    <div class="text-slate-500">Final Hour Meter</div>
                    <div class="font-bold text-slate-900">{{ $rental->final_hour_meter ?? '-' }}</div>
                </div>
            </div>

            @if ($rental->notes)
                <div>
                    <div class="text-slate-500 text-sm">Notes</div>
                    <div class="text-slate-900">{{ $rental->notes }}</div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            <h2 class="text-lg font-bold text-slate-900">Actions</h2>

            @if (auth()->user()->hasRole(['admin', 'sales']))
                @if ($rental->status === 'draft')
                    <form method="POST" action="{{ route('dashboard.rentals.activate', $rental) }}">
                        @csrf
                        <button class="w-full rounded-xl bg-emerald-600 text-white font-bold px-4 py-3">
                            Activate Rental
                        </button>
                    </form>
                @endif

                @if (in_array($rental->status, ['draft', 'active'], true))
                    <a href="{{ route('dashboard.rentals.edit', $rental) }}" class="block text-center w-full rounded-xl bg-amber-500 text-white font-bold px-4 py-3">
                        Edit Rental
                    </a>

                    <form method="POST" action="{{ route('dashboard.rentals.extend', $rental) }}" class="space-y-2">
                        @csrf
                        <label class="block text-sm font-bold text-slate-700">Extend End Date</label>
                        <input type="date" name="end_date" value="{{ $rental->end_date?->format('Y-m-d') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2">
                        <button class="w-full rounded-xl bg-blue-600 text-white font-bold px-4 py-2">
                            Extend
                        </button>
                    </form>
                @endif

                @if ($rental->status === 'active')
                    <form method="POST" action="{{ route('dashboard.rentals.complete', $rental) }}" class="space-y-2">
                        @csrf
                        <label class="block text-sm font-bold text-slate-700">Final Hour Meter</label>
                        <input type="number" name="final_hour_meter" min="{{ $rental->initial_hour_meter }}" value="{{ $rental->final_hour_meter }}" class="w-full rounded-xl border border-slate-300 px-3 py-2">
                        <button class="w-full rounded-xl bg-slate-900 text-white font-bold px-4 py-2">
                            Complete Rental
                        </button>
                    </form>
                @endif

                @if (in_array($rental->status, ['draft', 'active'], true))
                    <form method="POST" action="{{ route('dashboard.rentals.cancel', $rental) }}">
                        @csrf
                        <button class="w-full rounded-xl bg-red-600 text-white font-bold px-4 py-3">
                            Cancel Rental
                        </button>
                    </form>
                @endif
            @endif

            @if (auth()->user()->hasRole(['admin', 'sales', 'support']))
                <form method="POST" action="{{ route('dashboard.rentals.hour-meter', $rental) }}" class="space-y-2 border-t border-slate-200 pt-4">
                    @csrf
                    <label class="block text-sm font-bold text-slate-700">Update Final Hour Meter</label>
                    <input type="number" name="final_hour_meter" min="{{ $rental->initial_hour_meter }}" value="{{ $rental->final_hour_meter }}" class="w-full rounded-xl border border-slate-300 px-3 py-2">
                    <button class="w-full rounded-xl bg-slate-100 text-slate-700 font-bold px-4 py-2">
                        Save Meter
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">Daily Calculation Breakdown</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Day</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Month Days</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Daily Rate</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Charge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach (($rental->calculation_breakdown['days'] ?? []) as $day)
                        <tr>
                            <td class="px-4 py-3">{{ $day['date'] }}</td>
                            <td class="px-4 py-3">{{ $day['day_name'] }}</td>
                            <td class="px-4 py-3">{{ $day['month_days'] }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $day['daily_rate'], 3) }}</td>
                            <td class="px-4 py-3 font-bold">{{ number_format((float) $day['daily_charge'], 3) }} {{ $rental->currency }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection