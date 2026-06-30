@extends('layouts.portal')

@section('title', 'Rental Details - CADY EST')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-[#00d26a] tracking-widest uppercase">Rental Details</span>
            <h1 class="text-3xl font-extrabold text-[#0b192c] mt-1">{{ $rental->ref_number }}</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ $rental->generator?->serial_number }} —
                {{ $rental->start_date?->format('Y-m-d') }} to {{ $rental->end_date?->format('Y-m-d') }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('portal.rentals.export', $rental) }}" class="rounded-xl bg-[#00d26a] text-white font-bold px-5 py-3">
                Export CSV
            </a>

            <a href="{{ route('portal.rentals.index') }}" class="rounded-xl bg-gray-100 text-gray-700 font-bold px-5 py-3">
                Back
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="text-sm text-gray-500">Status</div>
            <div class="text-xl font-black text-[#0b192c]">{{ ucfirst($rental->status) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="text-sm text-gray-500">Days</div>
            <div class="text-xl font-black text-[#0b192c]">{{ $rental->calculated_days }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="text-sm text-gray-500">Monthly Rate</div>
            <div class="text-xl font-black text-[#0b192c]">{{ number_format((float) $rental->monthly_rate, 3) }} {{ $rental->currency }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="text-sm text-gray-500">Total</div>
            <div class="text-xl font-black text-[#0b192c]">{{ number_format((float) $rental->total_amount, 3) }} {{ $rental->currency }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-[#0b192c] mb-4">Rental Information</h2>

        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-500">Customer</div>
                <div class="font-bold text-gray-900">{{ $rental->customer?->company_name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-gray-500">Generator</div>
                <div class="font-bold text-gray-900">{{ $rental->generator?->serial_number ?? '-' }}</div>
            </div>

            <div>
                <div class="text-gray-500">Start Date</div>
                <div class="font-bold text-gray-900">{{ $rental->start_date?->format('Y-m-d') }}</div>
            </div>

            <div>
                <div class="text-gray-500">End Date</div>
                <div class="font-bold text-gray-900">{{ $rental->end_date?->format('Y-m-d') }}</div>
            </div>

            <div>
                <div class="text-gray-500">Initial Hour Meter</div>
                <div class="font-bold text-gray-900">{{ $rental->initial_hour_meter }}</div>
            </div>

            <div>
                <div class="text-gray-500">Final Hour Meter</div>
                <div class="font-bold text-gray-900">{{ $rental->final_hour_meter ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-extrabold text-[#0b192c]">Daily Billing Breakdown</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-700">
                <thead class="bg-gray-50 text-gray-500 font-bold">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Day</th>
                        <th class="px-4 py-3">Month Days</th>
                        <th class="px-4 py-3">Daily Rate</th>
                        <th class="px-4 py-3">Charge</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
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