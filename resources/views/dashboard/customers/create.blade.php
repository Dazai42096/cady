@extends('layouts.dashboard')

@section('title', 'إضافة عميل جديد - CADY EST')
@section('page_title', 'إضافة عميل جديد')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#0b192c]">إضافة عميل جديد</h2>
    <a href="{{ route('dashboard.customers.index') }}"
       class="text-sm text-gray-500 hover:text-[#0b192c] flex items-center gap-1 transition">
        → العودة للقائمة
    </a>
</div>

<form action="{{ route('dashboard.customers.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-5 pb-3 border-b border-gray-100">بيانات الشركة</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">اسم الشركة <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none text-sm @error('company_name') border-red-400 @enderror"
                               placeholder="مثال: شركة الخليج للطاقة">
                        @error('company_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">جهة الاتصال <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none text-sm @error('contact_person') border-red-400 @enderror"
                               placeholder="اسم المسؤول">
                        @error('contact_person')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">رقم الهاتف <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none text-sm dir-ltr @error('phone') border-red-400 @enderror"
                               placeholder="+966 5x xxx xxxx">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none text-sm dir-ltr @error('email') border-red-400 @enderror"
                               placeholder="info@company.com">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">النشاط التجاري</label>
                        <input type="text" name="business_activity" value="{{ old('business_activity') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none text-sm"
                               placeholder="مثال: مصنع، مستشفى، فندق...">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العنوان</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none text-sm"
                               placeholder="المدينة، المنطقة">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">ملاحظات</h3>
                <textarea name="notes" rows="4"
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none text-sm"
                          placeholder="أي ملاحظات إضافية حول العميل...">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">الإجراء</h3>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                    <p class="text-xs text-amber-700 font-semibold">📋 ملاحظة</p>
                    <p class="text-xs text-amber-600 mt-1">سيتم إنشاء العميل في حالة "قيد الانتظار" ويجب على المدير الموافقة عليه.</p>
                </div>
                <button type="submit"
                        class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm cursor-pointer">
                    ✓ حفظ وإنشاء العميل
                </button>
                <a href="{{ route('dashboard.customers.index') }}"
                   class="block text-center w-full mt-3 text-gray-500 hover:text-gray-700 text-sm transition">
                    إلغاء
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
