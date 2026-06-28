$ErrorActionPreference = 'Stop'
$project = "C:\Users\azmih\.gemini\antigravity\scratch\cady-est"
Set-Location $project
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)

$index = @'
@extends('layouts.dashboard')

@section('title', 'عروض الأسعار - CADY EST')
@section('page_title', 'عروض الأسعار')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#0b192c]">إدارة عروض الأسعار</h2>
            <p class="text-sm text-gray-500 mt-1">عرض ومتابعة عروض الأسعار الخاصة بالعملاء</p>
        </div>

        <a href="{{ route('dashboard.quotations.create') }}"
           class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-3 rounded-xl font-bold text-sm transition">
            + إنشاء عرض سعر
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('dashboard.quotations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="ابحث برقم العرض، اسم العميل، أو المشروع..."
                   class="md:col-span-2 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">

            <select name="status"
                    class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                <option value="">كل الحالات</option>
                <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                <option value="sent" @selected(request('status') === 'sent')>تم الإرسال</option>
                <option value="accepted" @selected(request('status') === 'accepted')>مقبول</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
            </select>

            <button type="submit"
                    class="bg-[#0b192c] text-white px-5 py-2.5 rounded-xl font-bold text-sm">
                بحث
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#0b192c] text-white">
                <tr>
                    <th class="px-4 py-4 text-right">رقم العرض</th>
                    <th class="px-4 py-4 text-right">العميل</th>
                    <th class="px-4 py-4 text-right">نوع العرض</th>
                    <th class="px-4 py-4 text-right">التاريخ</th>
                    <th class="px-4 py-4 text-right">الإجمالي</th>
                    <th class="px-4 py-4 text-right">الحالة</th>
                    <th class="px-4 py-4 text-right">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($quotations as $quotation)
                    @php
                        $statusLabel = is_object($quotation->status) && method_exists($quotation->status, 'label')
                            ? $quotation->status->label()
                            : (string) ($quotation->status->value ?? $quotation->status ?? 'غير محدد');

                        $typeLabel = is_object($quotation->type) && method_exists($quotation->type, 'label')
                            ? $quotation->type->label()
                            : (string) ($quotation->type->value ?? $quotation->type ?? 'غير محدد');

                        $date = $quotation->quotation_date ?? $quotation->date ?? optional($quotation->created_at)->toDateString();
                        $total = $quotation->total ?? $quotation->total_amount ?? 0;
                        $currency = $quotation->currency ?? 'JOD';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 font-bold text-[#0b192c]">{{ $quotation->ref_number }}</td>
                        <td class="px-4 py-4">
                            @if($quotation->customer)
                                <a href="{{ route('dashboard.customers.show', $quotation->customer) }}" class="text-blue-600 hover:underline">
                                    {{ $quotation->customer->company_name }}
                                </a>
                            @else
                                <span class="text-gray-400">غير محدد</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-gray-700">{{ $typeLabel }}</td>
                        <td class="px-4 py-4 text-gray-600">{{ $date }}</td>
                        <td class="px-4 py-4 font-bold text-[#0b192c]">{{ number_format($total, 2) }} {{ $currency }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('dashboard.quotations.show', $quotation) }}" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold hover:bg-blue-100">عرض</a>
                                <a href="{{ route('dashboard.quotations.pdf', $quotation) }}" class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-700 text-xs font-bold hover:bg-gray-100">PDF</a>
                                <a href="{{ route('dashboard.quotations.edit', $quotation) }}" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold hover:bg-amber-100">تعديل</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">لا توجد عروض أسعار حتى الآن.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($quotations, 'links'))
            <div class="p-4 border-t border-gray-100">{{ $quotations->links() }}</div>
        @endif
    </div>
</div>
@endsection
'@

$show = @'
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

    $currency = $quotation->currency ?? 'JOD';
    $subtotal = $quotation->subtotal ?? 0;
    $tax = $quotation->tax_amount ?? $quotation->vat_amount ?? 0;
    $total = $quotation->total ?? $quotation->total_amount ?? 0;
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#0b192c]">عرض السعر {{ $quotation->ref_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">مراجعة تفاصيل العرض والبنود والمبالغ</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard.quotations.pdf', $quotation) }}" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm font-bold hover:bg-gray-200">تحميل PDF</a>
            <a href="{{ route('dashboard.quotations.edit', $quotation) }}" class="px-4 py-2 rounded-xl bg-amber-50 text-amber-700 text-sm font-bold hover:bg-amber-100">تعديل</a>
            <a href="{{ route('dashboard.quotations.index') }}" class="px-4 py-2 rounded-xl bg-[#0b192c] text-white text-sm font-bold">رجوع</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-[#0b192c] mb-4">بيانات العميل</h3>
            <div class="space-y-2 text-sm text-gray-700">
                <div><span class="font-bold">الشركة:</span> {{ $quotation->customer->company_name ?? 'غير محدد' }}</div>
                <div><span class="font-bold">جهة الاتصال:</span> {{ $quotation->customer->contact_person ?? 'غير محدد' }}</div>
                <div><span class="font-bold">الهاتف:</span> {{ $quotation->customer->phone ?? 'غير محدد' }}</div>
                <div><span class="font-bold">البريد:</span> {{ $quotation->customer->email ?? 'غير محدد' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-[#0b192c] mb-4">بيانات العرض</h3>
            <div class="space-y-2 text-sm text-gray-700">
                <div><span class="font-bold">نوع العرض:</span> {{ $typeLabel }}</div>
                <div><span class="font-bold">الحالة:</span> {{ $statusLabel }}</div>
                <div><span class="font-bold">تاريخ العرض:</span> {{ $quotation->quotation_date ?? $quotation->date }}</div>
                <div><span class="font-bold">صالح حتى:</span> {{ $quotation->valid_until }}</div>
                <div><span class="font-bold">العملة:</span> {{ $currency }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-[#0b192c]">بنود عرض السعر</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-[#0b192c] text-white">
                <tr>
                    <th class="px-4 py-3 text-right">الوصف</th>
                    <th class="px-4 py-3 text-right">الكمية</th>
                    <th class="px-4 py-3 text-right">سعر الوحدة</th>
                    <th class="px-4 py-3 text-right">الإجمالي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($quotation->items as $item)
                    @php
                        $qty = $item->quantity ?? $item->qty ?? 1;
                        $lineTotal = $item->total ?? $item->total_price ?? (($item->unit_price ?? 0) * $qty);
                    @endphp
                    <tr>
                        <td class="px-4 py-3">{{ $item->description }}</td>
                        <td class="px-4 py-3">{{ $qty }}</td>
                        <td class="px-4 py-3">{{ number_format($item->unit_price ?? 0, 2) }} {{ $currency }}</td>
                        <td class="px-4 py-3 font-bold">{{ number_format($lineTotal, 2) }} {{ $currency }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 max-w-md mr-auto">
        <div class="flex justify-between py-2 text-sm"><span>المجموع الفرعي</span><span>{{ number_format($subtotal, 2) }} {{ $currency }}</span></div>
        <div class="flex justify-between py-2 text-sm"><span>الضريبة {{ $quotation->tax_rate ?? 16 }}%</span><span>{{ number_format($tax, 2) }} {{ $currency }}</span></div>
        <div class="flex justify-between py-3 border-t border-gray-100 font-bold text-lg"><span>الإجمالي النهائي</span><span>{{ number_format($total, 2) }} {{ $currency }}</span></div>
    </div>
</div>
@endsection
'@

$edit = @'
@extends('layouts.dashboard')

@section('title', 'تعديل عرض السعر - CADY EST')
@section('page_title', 'تعديل عرض السعر')

@section('content')
@php
    $items = $quotation->items->map(function ($item) {
        $qty = $item->quantity ?? $item->qty ?? 1;
        return [
            'description' => $item->description,
            'quantity' => (float) $qty,
            'unit_price' => (float) ($item->unit_price ?? 0),
            'total' => (float) ($item->total ?? $item->total_price ?? (($item->unit_price ?? 0) * $qty)),
        ];
    })->values();
@endphp

<form method="POST" action="{{ route('dashboard.quotations.update', $quotation) }}" x-data="quotationForm()" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="customer_mode" value="existing">

    @if ($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-4 rounded-xl text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">بيانات العميل</h3>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">اختر العميل</label>
                <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $quotation->customer_id) == $customer->id)>
                            {{ $customer->company_name }} - {{ $customer->contact_person }}
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
                            <option value="sale" @selected(old('type', $quotation->type->value ?? $quotation->type) === 'sale')>بيع مولد</option>
                            <option value="rental" @selected(old('type', $quotation->type->value ?? $quotation->type) === 'rental')>تأجير مولد</option>
                            <option value="maintenance_contract" @selected(old('type', $quotation->type->value ?? $quotation->type) === 'maintenance_contract')>عقد صيانة</option>
                            <option value="spare_parts" @selected(old('type', $quotation->type->value ?? $quotation->type) === 'spare_parts')>قطع غيار</option>
                            <option value="other" @selected(old('type', $quotation->type->value ?? $quotation->type) === 'other')>أخرى</option>
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
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                    <h3 class="text-base font-bold text-[#0b192c]">بنود عرض السعر</h3>
                    <button type="button" @click="addItem()" class="bg-[#0b192c] text-white rounded-lg px-3 py-2 text-xs font-bold">+ إضافة بند</button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 items-end bg-gray-50 rounded-xl p-3 mb-3">
                        <div class="col-span-12 md:col-span-5">
                            <label class="text-xs text-gray-500 mb-1 block">البند / الوصف</label>
                            <input type="text" :name="`items[${index}][description]`" x-model="item.description" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">الكمية</label>
                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="calcItem(index)" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">سعر الوحدة</label>
                            <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcItem(index)" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="text-xs text-gray-500 mb-1 block">الإجمالي</label>
                            <input type="text" :value="item.total.toFixed(2)" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm font-semibold">
                        </div>
                        <div class="col-span-12 md:col-span-1">
                            <button type="button" @click="removeItem(index)" class="w-full text-red-500 py-2">✕</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملاحظات</h3>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">{{ old('notes', $quotation->notes) }}</textarea>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sticky top-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملخص العرض</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span>المجموع الفرعي</span><span x-text="money(subtotal)"></span></div>
                    <div class="flex justify-between items-center"><span>الخصم</span><input type="number" name="discount" x-model.number="discount" @input="calcTotals()" min="0" step="0.01" class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs text-left"></div>
                    <div class="flex justify-between items-center"><span>الضريبة</span><select name="tax_rate" x-model.number="taxRate" @change="calcTotals()" class="w-28 px-2 py-1 border border-gray-200 rounded-lg text-xs"><option value="16">16%</option><option value="8">8%</option><option value="0">0%</option></select></div>
                    <div class="flex justify-between text-xs text-gray-500"><span>مبلغ الضريبة</span><span x-text="money(taxAmount)"></span></div>
                    <div class="flex justify-between font-bold text-lg border-t border-gray-100 pt-3"><span>الإجمالي النهائي</span><span class="text-[#00d26a]" x-text="money(total)"></span></div>
                </div>
                <div class="mt-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">العملة</label>
                    <select name="currency" x-model="currency" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm">
                        <option value="JOD">JOD - دينار أردني</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm mt-5">حفظ التعديلات</button>
                <a href="{{ route('dashboard.quotations.index') }}" class="block text-center w-full mt-3 text-gray-500 text-sm">إلغاء</a>
            </div>
        </div>
    </div>
</form>

<script>
function quotationForm() {
    return {
        currency: '{{ old('currency', $quotation->currency ?? 'JOD') }}',
        items: @json($items),
        discount: Number('{{ old('discount', $quotation->discount ?? 0) }}'),
        taxRate: Number('{{ old('tax_rate', $quotation->tax_rate ?? 16) }}'),
        subtotal: 0,
        taxAmount: 0,
        total: 0,
        init() { this.items.forEach((_, i) => this.calcItem(i)); this.calcTotals(); },
        addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0, total: 0 }); },
        removeItem(index) { if (this.items.length > 1) { this.items.splice(index, 1); this.calcTotals(); } },
        calcItem(index) { const item = this.items[index]; item.total = Number(item.quantity || 0) * Number(item.unit_price || 0); this.calcTotals(); },
        calcTotals() { this.subtotal = this.items.reduce((sum, i) => sum + Number(i.total || 0), 0); const taxable = Math.max(0, this.subtotal - Number(this.discount || 0)); this.taxAmount = taxable * (Number(this.taxRate || 0) / 100); this.total = taxable + this.taxAmount; },
        money(value) { return Number(value || 0).toFixed(2) + ' ' + this.currency; }
    }
}
</script>
@endsection
'@

[System.IO.File]::WriteAllText("resources\views\dashboard\quotations\index.blade.php", $index, $utf8NoBom)
[System.IO.File]::WriteAllText("resources\views\dashboard\quotations\show.blade.php", $show, $utf8NoBom)
[System.IO.File]::WriteAllText("resources\views\dashboard\quotations\edit.blade.php", $edit, $utf8NoBom)

Write-Host "Quotation Arabic views replaced successfully."
Write-Host "Now run: php artisan view:clear; php artisan optimize:clear; npm run build; php artisan serve"
