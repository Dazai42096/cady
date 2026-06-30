@extends('layouts.portal')

@section('title', 'My Rentals - CADY EST')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-[#00d26a] tracking-widest uppercase">Customer Portal</span>
            <h1 class="text-3xl font-extrabold text-[#0b192c] mt-1">My Rentals</h1>
            <p class="text-gray-500 text-sm mt-1">View your generator rental records, status, period, total amount, and daily billing breakdown.</p>
        </div>

        <a href="{{ route('portal.index') }}" class="rounded-xl bg-gray-100 text-gray-700 font-bold px-5 py-3">
            Back to Portal
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-700">
                <thead class="bg-gray-50 text-gray-500 font-bold">
                    <tr>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Generator</th>
                        <th class="px-4 py-3">Period</th>
                        <th class="px-4 py-3">Days</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($rentals as $rental)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-gray-900">
                                {{ $rental->ref_number }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="font-bold">{{ $rental->generator?->serial_number ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $rental->generator?->brand }} {{ $rental->generator?->model }}</div>
                            </td>

                            <td class="px-4 py-3">
                                {{ $rental->start_date?->format('Y-m-d') }} → {{ $rental->end_date?->format('Y-m-d') }}
                            </td>

                            <td class="px-4 py-3 font-bold">
                                {{ $rental->calculated_days }}
                            </td>

                            <td class="px-4 py-3 font-bold">
                                {{ number_format((float) $rental->total_amount, 3) }} {{ $rental->currency }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold
                                    @class([
                                        'bg-gray-100 text-gray-700' => $rental->status === 'draft',
                                        'bg-green-100 text-green-700' => $rental->status === 'active',
                                        'bg-blue-100 text-blue-700' => $rental->status === 'completed',
                                        'bg-red-100 text-red-700' => $rental->status === 'cancelled',
                                    ])">
                                    {{ ucfirst($rental->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('portal.rentals.show', $rental) }}" class="rounded-lg bg-[#0b192c] text-white text-xs font-bold px-3 py-2">
                                        View
                                    </a>

                                    <a href="{{ route('portal.rentals.export', $rental) }}" class="rounded-lg bg-[#00d26a] text-white text-xs font-bold px-3 py-2">
                                        CSV
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                No rental records available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-100">
            {{ $rentals->links() }}
        </div>
    </div>
</div>
@endsection