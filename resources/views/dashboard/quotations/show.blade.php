@extends('layouts.dashboard')
@section('title', 'تفاصيل عرض السعر - CADY EST')
@section('page_title', 'تفاصيل عرض السعر')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c] font-mono">{{ $quotation->ref_number }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $quotation->type?->label() }} — أُنشئ بواسطة {{ $quotation->creator?->name }}</p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('dashboard.quotations.pdf', $quotation) }}"
           class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-1.5">
            📄 تحميل PDF
        </a>
        @can('update', $quotation)
        <a href="{{ route('dashboard.quotations.edit', $quotation) }}"
           class="bg-amber-500 hover:bg-amber-400 text-white px-4 py-2 rounded-xl text-sm font-bold transition">تعديل</a>
        @endcan
        <a href="{{ route('dashboard.quotations.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold transition">→ القائمة</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Header Info --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-[#0b192c]">معلومات العرض</h3>
                <x-badge :status="$quotation->status" />
            </div>
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs mb-1">العميل</dt>
                    <dd class="font-semibold">
                        <a href="{{ route('dashboard.customers.show', $quotation->customer) }}"
                           class="text-blue-600 hover:text-blue-800 transition">{{ $quotation->customer?->company_name }}</a>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-1">تاريخ الإصدار</dt>
                    <dd class="font-semibold">{{ $quotation->quotation_date?->format('Y/m/d') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs mb-1">صالح حتى</dt>
                    <dd class="font-semibold @if($quotation->valid_until?->isPast() && $quotation->status->value === 'sent') text-red-600 @endif">
                        {{ $quotation->valid_until?->format('Y/m/d') }}
                    </dd>
                </div>
                @if($quotation->project)
                <div class="col-span-full">
                    <dt class="text-gray-400 text-xs mb-1">المشروع</dt>
                    <dd class="font-semibold">{{ $quotation->project }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Line Items --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 pb-0">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بنود العرض</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-gray-50 text-xs text-gray-500">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">الوصف</th>
                            <th class="px-6 py-3 text-center">الكمية</th>
                            <th class="px-6 py-3 text-center">سعر الوحدة</th>
                            <th class="px-6 py-3 text-center">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($quotation->items as $item)
                        <tr>
                            <td class="px-6 py-3 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $item->description }}</td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ number_format($item->qty, 2) }}</td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-3 text-center font-semibold text-[#0b192c]">{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6 border-t border-gray-100 space-y-2 text-sm">
                <div class="flex justify-end gap-16">
                    <span class="text-gray-500">الإجمالي قبل الخصم</span>
                    <span class="font-semibold w-28 text-left">{{ number_format($quotation->subtotal, 2) }} {{ $quotation->currency }}</span>
                </div>
                @if($quotation->discount > 0)
                <div class="flex justify-end gap-16 text-red-600">
                    <span>الخصم</span>
                    <span class="font-semibold w-28 text-left">- {{ number_format($quotation->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-end gap-16 text-gray-500">
                    <span>ضريبة القيمة المضافة ({{ $quotation->tax_rate }}%)</span>
                    <span class="font-semibold w-28 text-left">{{ number_format($quotation->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-end gap-16 text-lg font-bold border-t border-gray-200 pt-3">
                    <span class="text-[#0b192c]">الإجمالي النهائي</span>
                    <span class="text-[#00d26a] w-28 text-left">{{ number_format($quotation->total, 2) }} {{ $quotation->currency }}</span>
                </div>
            </div>
        </div>

        @if($quotation->notes)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-3">ملاحظات</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $quotation->notes }}</p>
        </div>
        @endif
    </div>

    {{-- Sidebar Actions --}}
    <div class="space-y-6">
        @can('update', $quotation)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">إجراءات سير العمل</h3>
            <div class="space-y-2">
                @if($quotation->status->value === 'draft')
                <form action="{{ route('dashboard.quotations.mark_sent', $quotation) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        📤 تحديد كمُرسَل
                    </button>
                </form>
                @endif
                @if($quotation->status->value === 'sent')
                <form action="{{ route('dashboard.quotations.accept', $quotation) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        ✓ تأكيد القبول
                    </button>
                </form>
                <form action="{{ route('dashboard.quotations.reject', $quotation) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        ✗ تسجيل الرفض
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endcan

        @can('delete', $quotation)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-red-600 mb-4 pb-3 border-b border-gray-100">منطقة الخطر</h3>
            <form action="{{ route('dashboard.quotations.destroy', $quotation) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العرض؟')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                    🗑 حذف العرض
                </button>
            </form>
        </div>
        @endcan

        <div class="bg-white rounded-2xl shadow-sm p-4 text-xs text-gray-400 space-y-1">
            <p>أُنشئ: {{ $quotation->created_at->format('Y/m/d H:i') }}</p>
            <p>آخر تحديث: {{ $quotation->updated_at->diffForHumans() }}</p>
        </div>
    </div>
</div>
@endsection
