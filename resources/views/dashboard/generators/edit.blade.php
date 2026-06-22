@extends('layouts.dashboard')
@section('title', 'تعديل المولد - CADY EST')
@section('page_title', 'تعديل بيانات المولد')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-[#0b192c]">تعديل: {{ $generator->serial_number }}</h2>
    <a href="{{ route('dashboard.generators.show', $generator) }}" class="text-sm text-gray-500 hover:text-[#0b192c] transition">→ العودة للمولد</a>
</div>

<form action="{{ route('dashboard.generators.update', $generator) }}" method="POST">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-5 pb-3 border-b border-gray-100">بيانات المولد</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">العميل المالك <span class="text-red-500">*</span></label>
                        <select name="customer_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $generator->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">رقم المسلسل <span class="text-red-500">*</span></label>
                        <input type="text" name="serial_number" value="{{ old('serial_number', $generator->serial_number) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm font-mono @error('serial_number') border-red-400 @enderror">
                        @error('serial_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الماركة <span class="text-red-500">*</span></label>
                        <input type="text" name="brand" value="{{ old('brand', $generator->brand) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('brand') border-red-400 @enderror">
                        @error('brand')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الموديل <span class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model', $generator->model) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm @error('model') border-red-400 @enderror">
                        @error('model')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">القدرة (KVA) <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity_kva" value="{{ old('capacity_kva', $generator->capacity_kva) }}" step="0.5" min="0"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">نوع الوقود <span class="text-red-500">*</span></label>
                        <select name="fuel_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            <option value="diesel" {{ old('fuel_type', $generator->fuel_type) == 'diesel' ? 'selected' : '' }}>⛽ ديزل</option>
                            <option value="gas" {{ old('fuel_type', $generator->fuel_type) == 'gas' ? 'selected' : '' }}>🔵 غاز طبيعي</option>
                            <option value="dual" {{ old('fuel_type', $generator->fuel_type) == 'dual' ? 'selected' : '' }}>⚡ ثنائي الوقود</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الحالة <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                            <option value="active" {{ old('status', $generator->status->value) == 'active' ? 'selected' : '' }}>✅ فعّال</option>
                            <option value="inactive" {{ old('status', $generator->status->value) == 'inactive' ? 'selected' : '' }}>⭕ غير فعّال</option>
                            <option value="under_maintenance" {{ old('status', $generator->status->value) == 'under_maintenance' ? 'selected' : '' }}>🔧 تحت الصيانة</option>
                            <option value="decommissioned" {{ old('status', $generator->status->value) == 'decommissioned' ? 'selected' : '' }}>🚫 خارج الخدمة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">الموقع</label>
                        <input type="text" name="location" value="{{ old('location', $generator->location) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">{{ old('notes', $generator->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">الحالة</h3>
                <div class="text-center py-2 mb-4"><x-badge :status="$generator->status" /></div>
                <button type="submit"
                        class="w-full bg-[#00d26a] hover:bg-green-500 text-white py-3 rounded-xl font-bold transition text-sm cursor-pointer">
                    ✓ حفظ التعديلات
                </button>
                <a href="{{ route('dashboard.generators.show', $generator) }}"
                   class="block text-center w-full mt-3 text-gray-500 hover:text-gray-700 text-sm transition">إلغاء</a>
            </div>
        </div>
    </div>
</form>
@endsection
