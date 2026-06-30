@extends('layouts.dashboard')

@section('title', 'Rentals')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Rental Control</h1>
            <p class="text-slate-600 mt-1">Manage generator rentals, active periods, extensions, completion, and exports.</p>
        </div>

        @if (auth()->user()->hasRole(['admin', 'sales']))
            <a href="{{ route('dashboard.rentals.create') }}" style="background:#059669;color:white;padding:12px 20px;border-radius:12px;font-weight:700;text-decoration:none;display:inline-block;">
                New Rental
            </a>
        @endif
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

    <form method="GET" action="{{ route('dashboard.rentals.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col md:flex-row gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search reference, customer, generator..."
            class="flex-1 rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400"
        >

        <select name="status" class="rounded-xl border border-slate-300 px-4 py-2">
            <option value="">All statuses</option>
            @foreach (['draft', 'active', 'completed', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <button class="rounded-xl bg-slate-900 text-white font-bold px-5 py-2">
            Filter
        </button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Generator</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($rentals as $rental)
                        <tr>
                            <td class="px-4 py-4 font-bold text-slate-900">
                                {{ $rental->ref_number }}
                            </td>

                            <td class="px-4 py-4">
                                {{ $rental->customer?->company_name ?? '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-medium">{{ $rental->generator?->serial_number ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $rental->generator?->model }}</div>
                            </td>

                            <td class="px-4 py-4 text-sm">
                                <div>{{ $rental->start_date?->format('Y-m-d') }} → {{ $rental->end_date?->format('Y-m-d') }}</div>
                                <div class="text-slate-500">{{ $rental->calculated_days }} days</div>
                            </td>

                            <td class="px-4 py-4 font-bold">
                                {{ number_format((float) $rental->total_amount, 3) }} {{ $rental->currency }}
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold
                                    @class([
                                        'bg-slate-100 text-slate-700' => $rental->status === 'draft',
                                        'bg-emerald-100 text-emerald-700' => $rental->status === 'active',
                                        'bg-blue-100 text-blue-700' => $rental->status === 'completed',
                                        'bg-red-100 text-red-700' => $rental->status === 'cancelled',
                                    ])">
                                    {{ ucfirst($rental->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('dashboard.rentals.show', $rental) }}" class="rounded-lg bg-slate-900 text-white text-xs font-bold px-3 py-2">
                                        View
                                    </a>

                                    <a href="{{ route('dashboard.rentals.export', $rental) }}" class="rounded-lg bg-indigo-600 text-white text-xs font-bold px-3 py-2">
                                        CSV
                                    </a>

                                    @if (auth()->user()->hasRole(['admin', 'sales']) && in_array($rental->status, ['draft', 'active'], true))
                                        <a href="{{ route('dashboard.rentals.edit', $rental) }}" class="rounded-lg bg-amber-500 text-white text-xs font-bold px-3 py-2">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                No rentals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200">
            {{ $rentals->links() }}
        </div>
    </div>
</div>
@endsection