@extends('layouts.dashboard')
@section('title', 'تعديل عرض السعر - CADY EST')
@section('page_title', 'تعديل عرض السعر')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#0b192c]">تعديل: <span class="font-mono">{{ $quotation->ref_number }}</span></h2>
    <a href="{{ route('dashboard.quotations.show', $quotation) }}" class="text-sm text-gray-500 hover:text-[#0b192c] transition">→ العودة للعرض</a>
</div>

<form action="{{ route('dashboard.quotations.update', $quotation) }}" method="POST" x-data="quotationBuilder()">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-5 pb-3 border-b border-gray-100">بيانات العرض</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العميل <span class="text-red-500">*</span></label>
                        <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $quotation->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">نوع العرض <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            <option value="parts" {{ old('type', $quotation->type?->value) == 'parts' ? 'selected' : '' }}>قطع غيار</option>
                            <option value="maintenance" {{ old('type', $quotation->type?->value) == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                            <option value="installation" {{ old('type', $quotation->type?->value) == 'installation' ? 'selected' : '' }}>تركيب</option>
                            <option value="other" {{ old('type', $quotation->type?->value) == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">المشروع</label>
                        <input type="text" name="project" value="{{ old('project', $quotation->project) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ الإصدار <span class="text-red-500">*</span></label>
                        <input type="date" name="quotation_date" value="{{ old('quotation_date', $quotation->quotation_date?->toDateString()) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm dir-ltr">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">صالح حتى <span class="text-red-500">*</span></label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', $quotation->valid_until?->toDateString()) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm dir-ltr">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-5 pb-3 border-b border-gray-100">بنود العرض</h3>
                <div class="space-y-3 mb-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-start p-3 bg-gray-50 rounded-xl">
                            <div class="col-span-5">
                                <label class="text-xs text-gray-500 mb-1 block">الوصف</label>
                                <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-500 mb-1 block">الكمية</label>
                                <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty"
                                       @input="calcItem(index)"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#00d26a] outline-none"
                                       min="0.01" step="0.01">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-500 mb-1 block">السعر</label>
                                <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price"
                                       @input="calcItem(index)"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#00d26a] outline-none"
                                       min="0" step="0.01">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-500 mb-1 block">الإجمالي</label>
                                <input type="text" :value="item.total.toFixed(2)" readonly
                                       class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-600 font-semibold">
                            </div>
                            <div class="col-span-1 flex items-end justify-center pb-1.5">
                                <button type="button" @click="removeItem(index)"
                                        class="text-red-400 hover:text-red-600 transition text-lg cursor-pointer leading-none">✕</button>
                            </div>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addItem()"
                        class="w-full border-2 border-dashed border-gray-200 hover:border-[#00d26a] text-gray-400 hover:text-[#00d26a] py-3 rounded-xl text-sm font-semibold transition cursor-pointer">
                    + إضافة بند جديد
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملاحظات</h3>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">{{ old('notes', $quotation->notes) }}</textarea>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">الإجماليات</h3>
                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">المجموع</span>
                        <span class="font-semibold" x-text="subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-gray-500">خصم</label>
                        <input type="number" name="discount" x-model.number="discount" @input="calcTotals()"
                               class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs text-left focus:ring-1 focus:ring-[#00d26a] outline-none"
                               min="0" step="0.01">
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-gray-500">ضريبة (%)</label>
                        <input type="number" name="tax_rate" x-model.number="taxRate" @input="calcTotals()"
                               class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs text-left focus:ring-1 focus:ring-[#00d26a] outline-none"
                               min="0" max="100" step="0.01">
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-3">
                        <span class="text-[#0b192c]">الإجمالي</span>
                        <span class="text-[#00d26a]" x-text="total.toFixed(2)"></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">العملة</label>
                    <select name="currency" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
                        <option value="SAR" {{ old('currency', $quotation->currency) == 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                        <option value="USD" {{ old('currency', $quotation->currency) == 'USD' ? 'selected' : '' }}>USD - دولار أمريكي</option>
                        <option value="AED" {{ old('currency', $quotation->currency) == 'AED' ? 'selected' : '' }}>AED - درهم إماراتي</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm cursor-pointer">
                    ✓ حفظ التعديلات
                </button>
                <a href="{{ route('dashboard.quotations.show', $quotation) }}"
                   class="block text-center w-full mt-3 text-gray-500 hover:text-gray-700 text-sm transition">إلغاء</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function quotationBuilder() {
    return {
        items: @json($quotation->items->map(fn($i) => ['description' => $i->description, 'qty' => (float)$i->qty, 'unit_price' => (float)$i->unit_price, 'total' => (float)$i->total])),
        discount: {{ $quotation->discount ?? 0 }},
        taxRate: {{ $quotation->tax_rate ?? 15 }},
        subtotal: {{ $quotation->subtotal ?? 0 }},
        taxAmount: {{ $quotation->tax_amount ?? 0 }},
        total: {{ $quotation->total ?? 0 }},
        addItem() { this.items.push({ description: '', qty: 1, unit_price: 0, total: 0 }); },
        removeItem(index) { if (this.items.length > 1) { this.items.splice(index, 1); this.calcTotals(); } },
        calcItem(index) {
            const item = this.items[index];
            item.total = parseFloat(item.qty || 0) * parseFloat(item.unit_price || 0);
            this.calcTotals();
        },
        calcTotals() {
            this.subtotal = this.items.reduce((sum, i) => sum + (i.total || 0), 0);
            const taxable = Math.max(0, this.subtotal - (this.discount || 0));
            this.taxAmount = taxable * ((this.taxRate || 0) / 100);
            this.total = taxable + this.taxAmount;
        }
    }
}
</script>
@endpush
@endsection
