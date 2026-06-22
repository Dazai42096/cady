@extends('layouts.dashboard')
@section('title', 'تفاصيل عقد الصيانة - CADY EST')
@section('page_title', 'تفاصيل عقد الصيانة')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c] font-mono">{{ $contract->ref_number }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $contract->to_name }} — {{ $contract->project }}</p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('dashboard.contracts.pdf', $contract) }}"
           class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition">📄 PDF</a>
        @can('update', $contract)
        <a href="{{ route('dashboard.contracts.edit', $contract) }}"
           class="bg-amber-500 hover:bg-amber-400 text-white px-4 py-2 rounded-xl text-sm font-bold transition">تعديل</a>
        @endcan
        <a href="{{ route('dashboard.contracts.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold transition">→ القائمة</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-[#0b192c]">تفاصيل العقد</h3>
                <x-badge :status="$contract->status" />
            </div>
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-5 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs mb-1">العميل</dt>
                    <dd class="font-semibold">
                        <a href="{{ route('dashboard.customers.show', $contract->customer) }}"
                           class="text-blue-600 hover:text-blue-800 transition">{{ $contract->customer?->company_name }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-1">المولد</dt>
                    <dd class="font-mono font-semibold text-xs">
                        <a href="{{ route('dashboard.generators.show', $contract->generator) }}"
                           class="text-[#0b192c] hover:text-[#00d26a] transition">{{ $contract->generator?->serial_number }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-1">عدد الزيارات</dt>
                    <dd class="font-bold text-2xl text-[#0b192c]">{{ $contract->visit_count }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-1">تاريخ البداية</dt>
                    <dd class="font-semibold">{{ $contract->contract_start_date?->format('Y/m/d') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-1">تاريخ النهاية</dt>
                    <dd class="font-semibold">{{ $contract->contract_end_date?->format('Y/m/d') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-1">طريقة الدفع</dt>
                    <dd class="font-semibold">{{ $contract->payment_method }}</dd>
                </div>
                <div class="col-span-full border-t border-gray-100 pt-4">
                    <div class="flex justify-end gap-8 text-sm">
                        <div class="text-center"><span class="text-gray-400 text-xs block">قيمة العقد</span><span class="font-semibold">{{ number_format($contract->subtotal, 2) }}</span></div>
                        <div class="text-center"><span class="text-gray-400 text-xs block">ضريبة {{ $contract->tax_rate }}%</span><span class="font-semibold">{{ number_format($contract->tax_amount, 2) }}</span></div>
                        <div class="text-center"><span class="text-gray-400 text-xs block">الإجمالي</span><span class="font-bold text-xl text-[#00d26a]">{{ number_format($contract->total_value, 2) }} {{ $contract->currency }}</span></div>
                    </div>
                </div>
            </dl>
        </div>

        {{-- Visits Table --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">
                الزيارات الميدانية ({{ $contract->visits->count() }})
            </h3>
            @if($contract->visits->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">
                    @if($contract->status->value === 'draft')
                        الزيارات ستُجدَّل تلقائياً عند تفعيل العقد
                    @else
                        لا توجد زيارات مجدولة
                    @endif
                </p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="text-xs text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="pb-2">#</th>
                            <th class="pb-2">التاريخ المخطط</th>
                            <th class="pb-2">الفني</th>
                            <th class="pb-2">الحالة</th>
                            <th class="pb-2">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($contract->visits as $visit)
                        <tr>
                            <td class="py-2 text-gray-400 text-xs">{{ $visit->visit_number }}</td>
                            <td class="py-2 font-semibold">{{ $visit->planned_date?->format('Y/m/d') }}</td>
                            <td class="py-2 text-gray-500 text-xs">{{ $visit->technician?->name ?? '—' }}</td>
                            <td class="py-2"><x-badge :status="$visit->status" /></td>
                            <td class="py-2">
                                <a href="{{ route('dashboard.visits.show', $visit) }}"
                                   class="text-xs text-blue-600 hover:underline">تفاصيل</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        @if($contract->terms || $contract->notes)
        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
            @if($contract->terms)
            <div>
                <h3 class="text-base font-bold text-[#0b192c] mb-2">الشروط والأحكام</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $contract->terms }}</p>
            </div>
            @endif
            @if($contract->notes)
            <div>
                <h3 class="text-base font-bold text-[#0b192c] mb-2">ملاحظات</h3>
                <p class="text-sm text-gray-600">{{ $contract->notes }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <div class="space-y-6">
        @can('activate', $contract)
        @if($contract->status->value === 'draft')
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">تفعيل العقد</h3>
            <p class="text-xs text-gray-500 mb-4">عند التفعيل سيتم جدولة {{ $contract->visit_count }} زيارة تلقائياً.</p>
            <form action="{{ route('dashboard.contracts.activate', $contract) }}" method="POST"
                  onsubmit="return confirm('هل تريد تفعيل العقد وجدولة الزيارات؟')">
                @csrf
                <button type="submit"
                        class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition cursor-pointer">
                    ▶ تفعيل العقد
                </button>
            </form>
        </div>
        @endif
        @endcan

        @can('terminate', $contract)
        @if($contract->status->value === 'active')
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-red-600 mb-4 pb-3 border-b border-gray-100">إنهاء العقد</h3>
            <form action="{{ route('dashboard.contracts.terminate', $contract) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من إنهاء العقد؟ سيتم إلغاء جميع الزيارات المعلقة.')">
                @csrf
                <button type="submit"
                        class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                    ■ إنهاء العقد
                </button>
            </form>
        </div>
        @endif
        @endcan

        <div class="bg-white rounded-2xl shadow-sm p-4 text-xs text-gray-400 space-y-1">
            <p>بواسطة: {{ $contract->creator?->name }}</p>
            <p>أُنشئ: {{ $contract->created_at->format('Y/m/d') }}</p>
        </div>
    </div>
</div>
@endsection
