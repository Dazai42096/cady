@extends('layouts.dashboard')
@section('title', 'إضافة مولد جديد - CADY EST')
@section('page_title', 'إضافة مولد جديد')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#0b192c]">إضافة مولد كهربائي</h2>
    <a href="{{ route('dashboard.generators.index') }}" class="text-sm text-gray-500 hover:text-[#0b192c] transition">→ العودة للقائمة</a>
</div>

<form action="{{ route('dashboard.generators.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-5 pb-3 border-b border-gray-100">بيانات المولد</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العميل المالك <span class="text-red-500">*</span></label>
                        <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('customer_id') border-red-400 @enderror">
                            <option value="">-- اختر العميل --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">رقم المسلسل <span class="text-red-500">*</span></label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm font-mono @error('serial_number') border-red-400 @enderror"
                               placeholder="مثال: SN-2024-001">
                        @error('serial_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الماركة <span class="text-red-500">*</span></label>
                        <input type="text" name="brand" value="{{ old('brand') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('brand') border-red-400 @enderror"
                               placeholder="مثال: Cummins, Perkins, Volvo">
                        @error('brand')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الموديل <span class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('model') border-red-400 @enderror"
                               placeholder="مثال: C500 D5">
                        @error('model')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">القدرة (KVA) <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity_kva" value="{{ old('capacity_kva') }}" step="0.5" min="0"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('capacity_kva') border-red-400 @enderror"
                               placeholder="500">
                        @error('capacity_kva')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">نوع الوقود <span class="text-red-500">*</span></label>
                        <select name="fuel_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('fuel_type') border-red-400 @enderror">
                            <option value="">-- اختر --</option>
                            <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>⛽ ديزل</option>
                            <option value="gas" {{ old('fuel_type') == 'gas' ? 'selected' : '' }}>🔵 غاز طبيعي</option>
                            <option value="dual" {{ old('fuel_type') == 'dual' ? 'selected' : '' }}>⚡ ثنائي الوقود</option>
                        </select>
                        @error('fuel_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الحالة <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('status') border-red-400 @enderror">
                            <option value="active" {{ old('status','active') == 'active' ? 'selected' : '' }}>✅ فعّال</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>⭕ غير فعّال</option>
                            <option value="under_maintenance" {{ old('status') == 'under_maintenance' ? 'selected' : '' }}>🔧 تحت الصيانة</option>
                            <option value="decommissioned" {{ old('status') == 'decommissioned' ? 'selected' : '' }}>🚫 خارج الخدمة</option>
                        </select>
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الموقع</label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm"
                               placeholder="مثال: مصنع الرياض - المبنى A">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm"
                                  placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">الإجراء</h3>
                <button type="submit"
                        class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm cursor-pointer">
                    ✓ حفظ المولد
                </button>
                <a href="{{ route('dashboard.generators.index') }}"
                   class="block text-center w-full mt-3 text-gray-500 hover:text-gray-700 text-sm transition">إلغاء</a>
            </div>
        </div>
    </div>
</form>
@endsection
