@extends('layouts.dashboard')
@section('title', 'عقود الصيانة - CADY EST')
@section('page_title', 'عقود الصيانة')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">عقود الصيانة</h2>
        <p class="text-sm text-gray-500 mt-1">إجمالي: {{ $contracts->total() }} عقد</p>
    </div>
    @can('create', App\Models\MaintenanceContract::class)
    <a href="{{ route('dashboard.contracts.create') }}"
       class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
        <span>+</span> عقد جديد
    </a>
    @endcan
</div>

<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('dashboard.contracts.index') }}" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="بحث برقم العقد أو اسم العميل..."
               class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
        <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
            <option value="">كل الحالات</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>مسودة</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>نشط</option>
            <option value="expired" {{ request('status')=='expired'?'selected':'' }}>منتهي</option>
            <option value="terminated" {{ request('status')=='terminated'?'selected':'' }}>مُنهى</option>
        </select>
        <button type="submit" class="bg-[#0b192c] text-white px-5 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition">بحث</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('dashboard.contracts.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-200 transition">مسح</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($contracts->isEmpty())
        <x-empty-state message="لا توجد عقود صيانة بعد." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">رقم العقد</th>
                    <th class="px-5 py-4">العميل</th>
                    <th class="px-5 py-4">المولد</th>
                    <th class="px-5 py-4">بداية العقد</th>
                    <th class="px-5 py-4">نهاية العقد</th>
                    <th class="px-5 py-4">الزيارات</th>
                    <th class="px-5 py-4">القيمة</th>
                    <th class="px-5 py-4">الحالة</th>
                    <th class="px-5 py-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($contracts as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <a href="{{ route('dashboard.contracts.show', $c) }}"
                           class="font-mono font-semibold text-[#0b192c] hover:text-[#00d26a] transition text-xs">{{ $c->ref_number }}</a>
                    </td>
                    <td class="px-5 py-4 text-xs">
                        <a href="{{ route('dashboard.customers.show', $c->customer) }}"
                           class="text-blue-600 hover:underline">{{ $c->customer?->company_name }}</a>
                    </td>
                    <td class="px-5 py-4 text-gray-500 font-mono text-xs">{{ $c->generator?->serial_number }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $c->contract_start_date?->format('Y/m/d') }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $c->contract_end_date?->format('Y/m/d') }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-lg text-xs font-bold">{{ $c->visit_count }}</span>
                    </td>
                    <td class="px-5 py-4 font-semibold text-[#0b192c] text-xs">
                        {{ number_format($c->total_value, 2) }} <span class="text-gray-400">{{ $c->currency }}</span>
                    </td>
                    <td class="px-5 py-4"><x-badge :status="$c->status" /></td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('dashboard.contracts.show', $c) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2.5 py-1.5 rounded-lg font-semibold transition">عرض</a>
                            @can('update', $c)
                            <a href="{{ route('dashboard.contracts.edit', $c) }}"
                               class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-2.5 py-1.5 rounded-lg font-semibold transition">تعديل</a>
                            @endcan
                            <a href="{{ route('dashboard.contracts.pdf', $c) }}"
                               class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-700 px-2.5 py-1.5 rounded-lg font-semibold transition">PDF</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $contracts->links() }}</div>
    @endif
</div>
@endsection
