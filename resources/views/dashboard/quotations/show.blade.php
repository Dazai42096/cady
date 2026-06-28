@extends('layouts.dashboard')

@section('title', 'تفاصيل عرض السعر - CADY EST')
@section('page_title', 'تفاصيل عرض السعر')

@section('content')
@php
    $statusLabel = is_object($quotation->status) && method_exists($quotation->status, 'label')
        ? $quotation->status->label()
        : (string) ($quotation->status->value ?? $quotation->status ?? 'غير محدد');

    $typeLabel = is_object($quotation->type) && method_exists($quotation->type, 'label')
        ? $quotation->type->label()
        : (string) ($quotation->type->value ?? $quotation->type ?? 'غير محدد');

    $date = $quotation->quotation_date ?? $quotation->date ?? optional($quotation->created_at)->toDateString();
    $subtotal = $quotation->subtotal ?? 0;
    $discount = $quotation->discount ?? 0;
    $taxAmount = $quotation->tax_amount ?? $quotation->vat_amount ?? 0;
    $total = $quotation->total ?? $quotation->total_amount ?? 0;
    $taxRate = $quotation->tax_rate ?? 16;
    $currency = $quotation->currency ?? 'JOD';
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#0b192c]">عرض سعر رقم {{ $quotation->ref_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">تفاصيل عرض السعر وحالته الحالية</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard.quotations.pdf', $quotation) }}"
               class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm font-bold hover:bg-gray-200">
                تحميل PDF
            </a>

            <a href="{{ route('dashboard.quotations.edit', $quotation) }}"
               class="px-4 py-2 rounded-xl bg-amber-50 text-amber-700 text-sm font-bold hover:bg-amber-100">
                تعديل
            </a>

            <a href="{{ route('dashboard.quotations.index') }}"
               class="px-4 py-2 rounded-xl bg-[#0b192c] text-white text-sm font-bold">
                رجوع
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-[#0b192c] mb-4">بيانات العميل</h3>
            <div class="space-y-2 text-sm">
                <div><span class="text-gray-500">الشركة:</span> <strong>{{ $quotation->customer->company_name ?? 'غير محدد' }}</strong></div>
                <div><span class="text-gray-500">جهة الاتصال:</span> {{ $quotation->customer->contact_person ?? 'غير محدد' }}</div>
                <div><span class="text-gray-500">الهاتف:</span> {{ $quotation->customer->phone ?? 'غير محدد' }}</div>
                <div><span class="text-gray-500">البريد:</span> {{ $quotation->customer->email ?? 'غير محدد' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-[#0b192c] mb-4">بيانات العرض</h3>
            <div class="space-y-2 text-sm">
                <div><span class="text-gray-500">نوع العرض:</span> {{ $typeLabel }}</div>
                <div><span class="text-gray-500">التاريخ:</span> {{ $date }}</div>
                <div><span class="text-gray-500">صالح حتى:</span> {{ $quotation->valid_until }}</div>
                <div><span class="text-gray-500">الحالة:</span> {{ $statusLabel }}</div>
                <div><span class="text-gray-500">المشروع / الوصف:</span> {{ $quotation->project ?? $quotation->subject ?? 'غير محدد' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#0b192c] text-white">
                <tr>
                    <th class="px-4 py-4 text-right">البند / الوصف</th>
                    <th class="px-4 py-4 text-right">الكمية</th>
                    <th class="px-4 py-4 text-right">سعر الوحدة</th>
                    <th class="px-4 py-4 text-right">الإجمالي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($quotation->items as $item)
                    @php
                        $quantity = $item->quantity ?? $item->qty ?? 1;
                        $lineTotal = $item->total ?? $item->total_price ?? ($quantity * ($item->unit_price ?? 0));
                    @endphp
                    <tr>
                        <td class="px-4 py-4">{{ $item->description }}</td>
                        <td class="px-4 py-4">{{ $quantity }}</td>
                        <td class="px-4 py-4">{{ number_format($item->unit_price ?? 0, 2) }} {{ $currency }}</td>
                        <td class="px-4 py-4 font-bold">{{ number_format($lineTotal, 2) }} {{ $currency }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-400">لا توجد بنود في هذا العرض.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 max-w-md mr-auto">
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span>المجموع قبل الخصم</span><strong>{{ number_format($subtotal, 2) }} {{ $currency }}</strong></div>
            <div class="flex justify-between"><span>الخصم</span><strong>{{ number_format($discount, 2) }} {{ $currency }}</strong></div>
            <div class="flex justify-between"><span>الضريبة ({{ $taxRate }}%)</span><strong>{{ number_format($taxAmount, 2) }} {{ $currency }}</strong></div>
            <div class="flex justify-between border-t pt-3 text-lg"><span>الإجمالي النهائي</span><strong>{{ number_format($total, 2) }} {{ $currency }}</strong></div>
        </div>
    </div>

    @if($quotation->notes)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-bold text-[#0b192c] mb-2">ملاحظات</h3>
            <p class="text-sm text-gray-700">{{ $quotation->notes }}</p>
        </div>
    @endif
</div>
@endsection
