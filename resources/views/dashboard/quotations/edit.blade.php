@extends('layouts.dashboard')

@section('title', 'تعديل عرض السعر - CADY EST')
@section('page_title', 'تعديل عرض السعر')

@section('content')
@php
    $typeValue = $quotation->type->value ?? $quotation->type ?? 'sale';
    $taxRate = $quotation->tax_rate ?? 16;
    $currency = $quotation->currency ?? 'JOD';
@endphp

<form method="POST" action="{{ route('dashboard.quotations.update', $quotation) }}" class="space-y-6">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-4 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بيانات العميل</h3>

        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اختر العميل</label>
        <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $quotation->customer_id) == $customer->id)>
                    {{ $customer->company_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بيانات العرض</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">نوع العرض</label>
                <select name="type" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    <option value="sale" @selected(old('type', $typeValue) === 'sale')>بيع مولد</option>
                    <option value="rental" @selected(old('type', $typeValue) === 'rental')>تأجير مولد</option>
                    <option value="maintenance_contract" @selected(old('type', $typeValue) === 'maintenance_contract')>عقد صيانة</option>
                    <option value="spare_parts" @selected(old('type', $typeValue) === 'spare_parts')>قطع غيار</option>
                    <option value="other" @selected(old('type', $typeValue) === 'other')>أخرى</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">المشروع / الوصف</label>
                <input type="text" name="project" value="{{ old('project', $quotation->project ?? $quotation->subject) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ العرض</label>
                <input type="date" name="quotation_date" value="{{ old('quotation_date', $quotation->quotation_date ?? $quotation->date) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">صالح حتى</label>
                <input type="date" name="valid_until" value="{{ old('valid_until', $quotation->valid_until) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بنود عرض السعر</h3>

        <div class="space-y-3">
            @forelse($quotation->items as $index => $item)
                @php
                    $quantity = $item->quantity ?? $item->qty ?? 1;
                    $lineTotal = $item->total ?? $item->total_price ?? ($quantity * ($item->unit_price ?? 0));
                @endphp

                <div class="grid grid-cols-12 gap-2 items-end bg-gray-50 rounded-xl p-3">
                    <div class="col-span-12 md:col-span-5">
                        <label class="text-xs text-gray-500 mb-1 block">البند / الوصف</label>
                        <input type="text" name="items[{{ $index }}][description]" value="{{ old("items.$index.description", $item->description) }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>

                    <div class="col-span-4 md:col-span-2">
                        <label class="text-xs text-gray-500 mb-1 block">الكمية</label>
                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ old("items.$index.quantity", $quantity) }}" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>

                    <div class="col-span-4 md:col-span-2">
                        <label class="text-xs text-gray-500 mb-1 block">سعر الوحدة</label>
                        <input type="number" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", $item->unit_price ?? 0) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>

                    <div class="col-span-4 md:col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">الإجمالي الحالي</label>
                        <input type="text" value="{{ number_format($lineTotal, 2) }} {{ $currency }}" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-semibold">
                    </div>
                </div>
            @empty
                <div class="grid grid-cols-12 gap-2 items-end bg-gray-50 rounded-xl p-3">
                    <div class="col-span-12 md:col-span-6">
                        <label class="text-xs text-gray-500 mb-1 block">البند / الوصف</label>
                        <input type="text" name="items[0][description]" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div class="col-span-6 md:col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">الكمية</label>
                        <input type="number" name="items[0][quantity]" value="1" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div class="col-span-6 md:col-span-3">
                        <label class="text-xs text-gray-500 mb-1 block">سعر الوحدة</label>
                        <input type="number" name="items[0][unit_price]" value="0" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملخص العرض</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">الخصم</label>
                <input type="number" name="discount" value="{{ old('discount', $quotation->discount ?? 0) }}" min="0" step="0.01" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">نسبة الضريبة</label>
                <select name="tax_rate" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
                    <option value="16" @selected(old('tax_rate', $taxRate) == 16)>16%</option>
                    <option value="8" @selected(old('tax_rate', $taxRate) == 8)>8%</option>
                    <option value="0" @selected(old('tax_rate', $taxRate) == 0)>0%</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">العملة</label>
                <select name="currency" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
                    <option value="JOD" @selected(old('currency', $currency) === 'JOD')>JOD - دينار أردني</option>
                    <option value="USD" @selected(old('currency', $currency) === 'USD')>USD - دولار أمريكي</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ملاحظات</label>
        <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">{{ old('notes', $quotation->notes) }}</textarea>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="bg-[#00d26a] hover:bg-green-500 text-white px-6 py-3 rounded-xl font-bold text-sm">
            حفظ التعديلات
        </button>

        <a href="{{ route('dashboard.quotations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-bold text-sm">
            إلغاء
        </a>
    </div>
</form>
@endsection
