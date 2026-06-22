@extends('layouts.dashboard')
@section('title', 'Ø¹Ø±ÙˆØ¶ Ø§Ù„Ø£Ø³Ø¹Ø§Ø± - CADY EST')
@section('page_title', 'Ø¹Ø±ÙˆØ¶ Ø§Ù„Ø£Ø³Ø¹Ø§Ø±')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">Ø¹Ø±ÙˆØ¶ Ø§Ù„Ø£Ø³Ø¹Ø§Ø±</h2>
        <p class="text-sm text-gray-500 mt-1">Ø¥Ø¬Ù…Ø§Ù„ÙŠ: {{ $quotations->total() }} Ø¹Ø±Ø¶</p>
    </div>
    @can('create', App\Models\Quotation::class)
    <a href="{{ route('dashboard.quotations.create') }}"
       class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
        <span>+</span> Ø¹Ø±Ø¶ Ø³Ø¹Ø± Ø¬Ø¯ÙŠØ¯
    </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('dashboard.quotations.index') }}" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Ø¨Ø­Ø« Ø¨Ø±Ù‚Ù… Ø§Ù„Ø¹Ø±Ø¶ Ø£Ùˆ Ø§Ø³Ù… Ø§Ù„Ø¹Ù…ÙŠÙ„..."
               class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
        <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
            <option value="">ÙƒÙ„ Ø§Ù„Ø­Ø§Ù„Ø§Øª</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Ù…Ø³ÙˆØ¯Ø©</option>
            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Ù…ÙØ±Ø³ÙŽÙ„</option>
            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Ù…Ù‚Ø¨ÙˆÙ„</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ù…Ø±ÙÙˆØ¶</option>
        </select>
        <button type="submit" class="bg-[#0b192c] text-white px-5 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition">Ø¨Ø­Ø«</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('dashboard.quotations.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-200 transition">Ù…Ø³Ø­</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($quotations->isEmpty())
        <x-empty-state message="Ù„Ø§ ØªÙˆØ¬Ø¯ Ø¹Ø±ÙˆØ¶ Ø£Ø³Ø¹Ø§Ø± Ø¨Ø¹Ø¯." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">Ø±Ù‚Ù… Ø§Ù„Ø¹Ø±Ø¶</th>
                    <th class="px-5 py-4">Ø§Ù„Ø¹Ù…ÙŠÙ„</th>
                    <th class="px-5 py-4">Ø§Ù„Ù†ÙˆØ¹</th>
                    <th class="px-5 py-4">Ø§Ù„ØªØ§Ø±ÙŠØ®</th>
                    <th class="px-5 py-4">Ø§Ù„Ø¥Ø¬Ù…Ø§Ù„ÙŠ</th>
                    <th class="px-5 py-4">Ø§Ù„Ø­Ø§Ù„Ø©</th>
                    <th class="px-5 py-4">Ø§Ù„Ø¥Ø¬Ø±Ø§Ø¡Ø§Øª</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($quotations as $q)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <a href="{{ route('dashboard.quotations.show', $q) }}"
                           class="font-mono font-semibold text-[#0b192c] hover:text-[#00d26a] transition text-xs">{{ $q->ref_number }}</a>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('dashboard.customers.show', $q->customer) }}"
                           class="text-blue-600 hover:text-blue-800 transition text-xs">{{ $q->customer?->company_name }}</a>
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $q->type?->label() }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $q->quotation_date?->format('Y/m/d') }}</td>
                    <td class="px-5 py-4 font-semibold text-[#0b192c]">
                        {{ number_format($q->total, 2) }} <span class="text-xs text-gray-400">{{ $q->currency }}</span>
                    </td>
                    <td class="px-5 py-4"><x-badge :status="$q->status" /></td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('dashboard.quotations.show', $q) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-1.5 rounded-lg font-semibold transition">Ø¹Ø±Ø¶</a>
                            @can('update', $q)
                            <a href="{{ route('dashboard.quotations.edit', $q) }}"
                               class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-2.5 py-1.5 rounded-lg font-semibold transition">ØªØ¹Ø¯ÙŠÙ„</a>
                            @endcan
                            <a href="{{ route('dashboard.quotations.pdf', $q) }}"
                               class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-700 px-2.5 py-1.5 rounded-lg font-semibold transition">PDF</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $quotations->links() }}</div>
    @endif
</div>
@endsection
