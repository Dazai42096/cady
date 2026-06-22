@extends('layouts.dashboard')
@section('title', 'إنشاء عقد صيانة - CADY EST')
@section('page_title', 'إنشاء عقد صيانة جديد')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#0b192c]">عقد صيانة جديد</h2>
    <a href="{{ route('dashboard.contracts.index') }}" class="text-sm text-gray-500 hover:text-[#0b192c] transition">→ العودة</a>
</div>

<form action="{{ route('dashboard.contracts.store') }}" method="POST">
    @csrf
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
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">المولد <span class="text-red-500">*</span></label>
                        <select name="generator_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('generator_id') border-red-400 @enderror">
                            <option value="">-- اختر المولد --</option>
                            @foreach($generators as $g)
                            <option value="{{ $g->id }}" {{ old('generator_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->serial_number }} — {{ $g->brand }} ({{ $g->customer?->company_name }})
                            </option>
                            @endforeach
                        </select>
                        @error('generator_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اسم المستلم <span class="text-red-500">*</span></label>
                        <input type="text" name="to_name" value="{{ old('to_name') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('to_name') border-red-400 @enderror"
                               placeholder="اسم الشركة أو الشخص المستلم للعقد">
                        @error('to_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">المشروع</label>
                        <input type="text" name="project" value="{{ old('project') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm"
                               placeholder="اسم المشروع (اختياري)">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ البداية <span class="text-red-500">*</span></label>
                        <input type="date" name="contract_start_date" value="{{ old('contract_start_date', now()->toDateString()) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm dir-ltr @error('contract_start_date') border-red-400 @enderror">
                        @error('contract_start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">تاريخ النهاية <span class="text-red-500">*</span></label>
                        <input type="date" name="contract_end_date" value="{{ old('contract_end_date', now()->addYear()->toDateString()) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm dir-ltr @error('contract_end_date') border-red-400 @enderror">
                        @error('contract_end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">عدد الزيارات <span class="text-red-500">*</span></label>
                        <input type="number" name="visit_count" value="{{ old('visit_count', 4) }}" min="1"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('visit_count') border-red-400 @enderror">
                        @error('visit_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">طريقة الدفع <span class="text-red-500">*</span></label>
                        <input type="text" name="payment_method" value="{{ old('payment_method') }}"
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
                        <input type="number" name="subtotal" value="{{ old('subtotal') }}" min="0" step="0.01"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('subtotal') border-red-400 @enderror"
                               placeholder="0.00">
                        @error('subtotal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">نسبة الضريبة (%)</label>
                        <input type="number" name="tax_rate" value="{{ old('tax_rate', 15) }}" min="0" max="100" step="0.01"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العملة</label>
                        <select name="currency" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            <option value="SAR" selected>SAR - ريال سعودي</option>
                            <option value="USD">USD - دولار أمريكي</option>
                            <option value="AED">AED - درهم إماراتي</option>
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
                                  placeholder="شروط وأحكام العقد...">{{ old('terms') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ملاحظات إضافية</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm"
                                  placeholder="ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">الإجراء</h3>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4">
                    <p class="text-xs text-blue-700 font-semibold">📋 ملاحظة</p>
                    <p class="text-xs text-blue-600 mt-1">سيتم إنشاء العقد كـ"مسودة". الزيارات تُجدَّل تلقائياً عند تفعيل العقد.</p>
                </div>
                <button type="submit"
                        class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm cursor-pointer">
                    ✓ إنشاء العقد
                </button>
                <a href="{{ route('dashboard.contracts.index') }}"
                   class="block text-center w-full mt-3 text-gray-500 hover:text-gray-700 text-sm transition">إلغاء</a>
            </div>
        </div>
    </div>
</form>
@endsection
