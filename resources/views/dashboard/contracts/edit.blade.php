@extends('layouts.dashboard')
@section('title', 'تعديل عقد الصيانة - CADY EST')
@section('page_title', 'تعديل عقد الصيانة')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#0b192c]">تعديل العقد: <span class="font-mono text-[#00d26a]">{{ $contract->ref_number }}</span></h2>
    <a href="{{ route('dashboard.contracts.show', $contract) }}" class="text-sm text-gray-500 hover:text-[#0b192c] transition">→ العودة للعقد</a>
</div>

@if($errors->any())
<div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 text-sm">
    <ul class="list-disc pr-4 space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('dashboard.contracts.update', $contract) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-5 pb-3 border-b border-gray-100">بيانات العقد</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العميل <span class="text-red-500">*</span></label>
                        <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('customer_id') border-red-400 @enderror">
                            <option value="">-- اختر العميل --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $contract->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">المولد <span class="text-red-500">*</span></label>
                        <select name="generator_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('generator_id') border-red-400 @enderror">
                            <option value="">-- اختر المولد --</option>
                            @foreach($generators as $g)
                            <option value="{{ $g->id }}" {{ old('generator_id', $contract->generator_id) == $g->id ? 'selected' : '' }}>
                                {{ $g->serial_number }} — {{ $g->brand }} ({{ $g->customer?->company_name }})
                            </option>
                            @endforeach
                        </select>
                        @error('generator_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اسم المستلم <span class="text-red-500">*</span></label>
                        <input type="text" name="to_name" value="{{ old('to_name', $contract->to_name) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('to_name') border-red-400 @enderror"
                               placeholder="اسم الشركة أو الشخص المستلم للعقد">
                        @error('to_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">المشروع</label>
                        <input type="text" name="project" value="{{ old('project', $contract->project) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm"
                               placeholder="اسم المشروع (اختياري)">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ البداية <span class="text-red-500">*</span></label>
                        <input type="date" name="contract_start_date" value="{{ old('contract_start_date', $contract->contract_start_date) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm dir-ltr @error('contract_start_date') border-red-400 @enderror">
                        @error('contract_start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ النهاية <span class="text-red-500">*</span></label>
                        <input type="date" name="contract_end_date" value="{{ old('contract_end_date', $contract->contract_end_date) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm dir-ltr @error('contract_end_date') border-red-400 @enderror">
                        @error('contract_end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">عدد الزيارات <span class="text-red-500">*</span></label>
                        <input type="number" name="visit_count" value="{{ old('visit_count', $contract->visit_count) }}" min="1"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('visit_count') border-red-400 @enderror">
                        @error('visit_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">طريقة الدفع <span class="text-red-500">*</span></label>
                        <input type="text" name="payment_method" value="{{ old('payment_method', $contract->payment_method) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('payment_method') border-red-400 @enderror"
                               placeholder="تحويل بنكي، نقداً، شيك...">
                        @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-5 pb-3 border-b border-gray-100">القيمة المالية</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">قيمة العقد <span class="text-red-500">*</span></label>
                        <input type="number" name="subtotal" value="{{ old('subtotal', $contract->subtotal) }}" min="0" step="0.01"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('subtotal') border-red-400 @enderror"
                               placeholder="0.00">
                        @error('subtotal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">نسبة الضريبة (%)</label>
                        <input type="number" name="tax_rate" value="{{ old('tax_rate', $contract->tax_rate) }}" min="0" max="100" step="0.01"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العملة</label>
                        <select name="currency" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            <option value="SAR" {{ old('currency', $contract->currency) === 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                            <option value="USD" {{ old('currency', $contract->currency) === 'USD' ? 'selected' : '' }}>USD - دولار أمريكي</option>
                            <option value="AED" {{ old('currency', $contract->currency) === 'AED' ? 'selected' : '' }}>AED - درهم إماراتي</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">الشروط والملاحظات</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الشروط والأحكام</label>
                        <textarea name="terms" rows="4"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm"
                                  placeholder="شروط وأحكام العقد...">{{ old('terms', $contract->terms) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ملاحظات إضافية</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm"
                                  placeholder="ملاحظات إضافية...">{{ old('notes', $contract->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">معلومات العقد</h3>
                <div class="space-y-3 mb-5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">رقم العقد:</span>
                        <span class="font-mono font-bold text-[#0b192c]">{{ $contract->ref_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">الحالة الحالية:</span>
                        <x-badge :type="$contract->status->value === 'active' ? 'success' : ($contract->status->value === 'draft' ? 'default' : 'danger')">
                            {{ $contract->status->label() }}
                        </x-badge>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">عدد الزيارات المنجزة:</span>
                        <span class="font-bold">{{ $contract->visits()->where('status', 'completed')->count() }}</span>
                    </div>
                </div>
                @if($contract->status->value === 'active')
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4">
                    <p class="text-xs text-amber-700 font-semibold">⚠️ تنبيه</p>
                    <p class="text-xs text-amber-600 mt-1">تعديل العقد النشط لن يعيد جدولة الزيارات المُنشأة مسبقاً.</p>
                </div>
                @endif
                <button type="submit"
                        class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm cursor-pointer">
                    ✓ حفظ التغييرات
                </button>
                <a href="{{ route('dashboard.contracts.show', $contract) }}"
                   class="block text-center w-full mt-3 text-gray-500 hover:text-gray-700 text-sm transition">إلغاء</a>
            </div>
        </div>
    </div>
</form>
@endsection
