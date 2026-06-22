@extends('layouts.dashboard')

@section('title', 'إنشاء عرض سعر - CADY EST')
@section('page_title', 'إنشاء عرض سعر جديد')

@section('content')
<form action="{{ route('dashboard.quotations.store') }}" method="POST" x-data="quotationBuilder()" x-init="calcTotals()" class="space-y-6">
    @csrf

    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-4 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بيانات العميل</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <label class="flex items-center gap-2 rounded-xl border border-gray-200 p-3 cursor-pointer">
                        <input type="radio" name="customer_mode" value="existing" x-model="customerMode" class="text-[#00d26a]" checked>
                        <span class="font-semibold text-sm">عميل موجود في النظام</span>
                    </label>
                    <label class="flex items-center gap-2 rounded-xl border border-gray-200 p-3 cursor-pointer">
                        <input type="radio" name="customer_mode" value="new" x-model="customerMode" class="text-[#00d26a]">
                        <span class="font-semibold text-sm">عميل جديد</span>
                    </label>
                </div>

                <div x-show="customerMode === 'existing'">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">اختر العميل</label>
                    <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                        <option value="">اختر العميل...</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->company_name }} - {{ $customer->contact_person }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="customerMode === 'new'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اسم الشركة</label>
                        <input type="text" name="new_customer[company_name]" value="{{ old('new_customer.company_name') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اسم الشخص المسؤول</label>
                        <input type="text" name="new_customer[contact_person]" value="{{ old('new_customer.contact_person') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الهاتف</label>
                        <input type="text" name="new_customer[phone]" value="{{ old('new_customer.phone') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">البريد الإلكتروني</label>
                        <input type="email" name="new_customer[email]" value="{{ old('new_customer.email') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">النشاط</label>
                        <input type="text" name="new_customer[business_activity]" value="{{ old('new_customer.business_activity') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm" placeholder="صناعات / طبي / تجاري">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العنوان</label>
                        <input type="text" name="new_customer[address]" value="{{ old('new_customer.address') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بيانات العرض</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">نوع العرض</label>
                        <select name="type" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            <option value="sale" {{ old('type') == 'sale' ? 'selected' : '' }}>بيع مولد</option>
                            <option value="rental" {{ old('type') == 'rental' ? 'selected' : '' }}>تأجير مولد</option>
                            <option value="maintenance_contract" {{ old('type') == 'maintenance_contract' ? 'selected' : '' }}>عقد صيانة</option>
                            <option value="spare_parts" {{ old('type') == 'spare_parts' ? 'selected' : '' }}>قطع غيار</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">المشروع / الوصف</label>
                        <input type="text" name="project" value="{{ old('project') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ العرض</label>
                        <input type="date" name="quotation_date" value="{{ old('quotation_date', now()->toDateString()) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">صالح حتى</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', now()->addDays(30)->toDateString()) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-[#0b192c]">بنود عرض السعر</h3>
                    <button type="button" @click="addItem()" class="bg-[#0b192c] text-white rounded-lg px-3 py-2 text-xs font-bold">+ إضافة بند</button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 items-end bg-gray-50 rounded-xl p-3 mb-3">
                        <div class="col-span-5">
                            <label class="text-xs text-gray-500 mb-1 block">البند / الوصف</label>
                            <input type="text" :name="`items[${index}][description]`" x-model="item.description" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="مثال: مولد بيركنز 50 KVA">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">الكمية</label>
                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="calcItem(index)" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">سعر الوحدة</label>
                            <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcItem(index)" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">الإجمالي</label>
                            <input type="text" :value="item.total.toFixed(2)" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-semibold">
                        </div>
                        <div class="col-span-1">
                            <button type="button" @click="removeItem(index)" class="w-full text-red-500 py-2">✕</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملاحظات</h3>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملخص العرض</h3>
                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between"><span>قبل الخصم</span><span x-text="money(subtotal)"></span></div>
                    <div class="flex justify-between items-center">
                        <label>الخصم</label>
                        <input type="number" name="discount" x-model.number="discount" @input="calcTotals()" min="0" step="0.01" class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs text-left">
                    </div>
                    <div class="flex justify-between items-center">
                        <label>نسبة الضريبة</label>
                        <select name="tax_rate" x-model.number="taxRate" @change="calcTotals()" class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs">
                            <option value="16">16%</option>
                            <option value="8">8%</option>
                            <option value="0">0%</option>
                        </select>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500"><span>مبلغ الضريبة</span><span x-text="money(taxAmount)"></span></div>
                    <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-3"><span>الإجمالي النهائي</span><span class="text-[#00d26a]" x-text="money(total)"></span></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">العملة</label>
                    <select name="currency" x-model="currency" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="JOD">JOD - دينار أردني</option>
                        <option value="USD">USD - دولار أمريكي</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm">حفظ عرض السعر</button>
                <a href="{{ route('dashboard.quotations.index') }}" class="block text-center w-full mt-3 text-gray-500 text-sm">إلغاء</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function quotationBuilder() {
    return {
        customerMode: '{{ old('customer_mode', 'existing') }}',
        currency: '{{ old('currency', 'JOD') }}',
        items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
        discount: Number('{{ old('discount', 0) }}'),
        taxRate: Number('{{ old('tax_rate', 16) }}'),
        subtotal: 0,
        taxAmount: 0,
        total: 0,
        addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 }); },
        removeItem(index) { if (this.items.length > 1) { this.items.splice(index, 1); this.calcTotals(); } },
        calcItem(index) {
            const item = this.items[index];
            item.total = Number(item.quantity || 0) * Number(item.unit_price || 0);
            this.calcTotals();
        },
        calcTotals() {
            this.subtotal = this.items.reduce((sum, i) => sum + Number(i.total || 0), 0);
            const taxable = Math.max(0, this.subtotal - Number(this.discount || 0));
            this.taxAmount = taxable * (Number(this.taxRate || 0) / 100);
            this.total = taxable + this.taxAmount;
        },
        money(value) { return Number(value || 0).toFixed(2) + ' ' + this.currency; }
    }
}
</script>
@endpush
@endsection