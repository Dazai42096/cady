@extends('layouts.dashboard')

@section('title', 'زيارات الصيانة - CADY EST')
@section('page_title', 'زيارات الصيانة الميدانية')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">زيارات الصيانة</h2>
        <p class="text-sm text-gray-500 mt-1">إجمالي: {{ $visits->total() }} زيارة</p>
    </div>
</div>

{{-- Filters and Search --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('dashboard.visits.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#00d26a]">
                <option value="">كل الحالات</option>
                @foreach(App\Enums\VisitStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">الفني المسؤول</label>
            <select name="technician_id" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#00d26a]">
                <option value="">كل الفنيين</option>
                @foreach($technicians as $tech)
                    <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                        {{ $tech->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-[#0b192c] hover:bg-slate-800 text-white py-2 rounded-xl text-sm font-semibold transition">
                تصفية
            </button>
            @if(request('status') || request('technician_id'))
                <a href="{{ route('dashboard.visits.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-200 transition">
                    إعادة تعيين
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Visits Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($visits->isEmpty())
        <div class="p-8 text-center text-gray-400">
            لا توجد زيارات صيانة مطابقة للخيارات المحددة.
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">التاريخ المخطط</th>
                    <th class="px-5 py-4">العميل</th>
                    <th class="px-5 py-4">المولد</th>
                    <th class="px-5 py-4">الفني المسؤول</th>
                    <th class="px-5 py-4">الحالة</th>
                    <th class="px-5 py-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($visits as $visit)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4 font-semibold text-gray-800 font-mono">
                        {{ $visit->planned_date }}
                    </td>
                    <td class="px-5 py-4">
                        @if($visit->contract && $visit->contract->customer)
                            <a href="{{ route('dashboard.customers.show', $visit->contract->customer) }}" class="text-[#0b192c] hover:text-[#00d26a] font-semibold transition">
                                {{ $visit->contract->customer->company_name }}
                            </a>
                        @else
                            <span class="text-gray-400">غير محدد</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($visit->contract && $visit->contract->generator)
                            <a href="{{ route('dashboard.generators.show', $visit->contract->generator) }}" class="text-gray-600 hover:text-[#00d26a] transition">
                                {{ $visit->contract->generator->brand }} ({{ $visit->contract->generator->model }})
                            </a>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">S/N: {{ $visit->contract->generator->serial_number }}</p>
                        @else
                            <span class="text-gray-400">غير محدد</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600">
                        {{ $visit->technician->name ?? 'غير معين' }}
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $badgeType = match($visit->status->value) {
                                'scheduled' => 'info',
                                'confirmed' => 'warning',
                                'in_progress' => 'navy',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'default',
                            };
                        @endphp
                        <x-badge :type="$badgeType">
                            {{ $visit->status->label() }}
                        </x-badge>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('dashboard.visits.show', $visit) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg font-semibold transition">
                                تفاصيل
                            </a>
                            @can('update', $visit)
                            <a href="{{ route('dashboard.visits.edit', $visit) }}"
                               class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg font-semibold transition">
                                تعديل
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100 font-mono">
        {{ $visits->links() }}
    </div>
    @endif
</div>
@endsection
