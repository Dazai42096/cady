@extends('layouts.dashboard')
@section('title', 'عروض الأسعار - CADY EST')
@section('page_title', 'عروض الأسعار')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">عروض الأسعار</h2>
        <p class="text-sm text-gray-500 mt-1">إجمالي: {{ $quotations->total() }} عرض</p>
    </div>
    @can('create', App\Models\Quotation::class)
    <a href="{{ route('dashboard.quotations.create') }}"
       class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
        <span>+</span> عرض سعر جديد
    </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('dashboard.quotations.index') }}" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="بحث برقم العرض أو اسم العميل..."
               class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
        <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
            <option value="">كل الحالات</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>مُرسَل</option>
            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
        </select>
        <button type="submit" class="bg-[#0b192c] text-white px-5 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition">بحث</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('dashboard.quotations.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-200 transition">مسح</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($quotations->isEmpty())
        <x-empty-state message="لا توجد عروض أسعار بعد." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">رقم العرض</th>
                    <th class="px-5 py-4">العميل</th>
                    <th class="px-5 py-4">النوع</th>
                    <th class="px-5 py-4">التاريخ</th>
                    <th class="px-5 py-4">الإجمالي</th>
                    <th class="px-5 py-4">الحالة</th>
                    <th class="px-5 py-4">الإجراءات</th>
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
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-1.5 rounded-lg font-semibold transition">عرض</a>
                            @can('update', $q)
                            <a href="{{ route('dashboard.quotations.edit', $q) }}"
                               class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-2.5 py-1.5 rounded-lg font-semibold transition">تعديل</a>
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
