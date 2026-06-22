@extends('layouts.dashboard')

@section('title', 'تعديل زيارة الصيانة - CADY EST')
@section('page_title', 'تعديل زيارة الصيانة')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('dashboard.visits.show', $visit) }}" class="text-gray-500 hover:text-gray-700 transition">
        &larr; إلغاء وتراجع
    </a>
    <span class="text-gray-300">|</span>
    <h2 class="text-2xl font-bold text-[#0b192c]">تعديل بيانات الزيارة</h2>
</div>

{{-- Form validation errors --}}
@if($errors->any())
<div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 text-sm font-medium">
    <ul class="list-disc pr-4 space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('dashboard.visits.update', $visit) }}" class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
    @csrf
    @method('PUT')

    {{-- Details grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">التاريخ المخطط <span class="text-red-500">*</span></label>
            <input type="date" name="planned_date" value="{{ old('planned_date', $visit->planned_date) }}" required
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">تاريخ التأكيد</label>
            <input type="date" name="confirmed_date" value="{{ old('confirmed_date', $visit->confirmed_date) }}"
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">تاريخ الزيارة الفعلي</label>
            <input type="date" name="actual_date" value="{{ old('actual_date', $visit->actual_date) }}"
                   class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-600 mb-2">الفني المسؤول عن الزيارة</label>
        <select name="assigned_to" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
            <option value="">-- لم يتم التعيين --</option>
            @foreach($technicians as $tech)
                <option value="{{ $tech->id }}" {{ old('assigned_to', $visit->assigned_to) == $tech->id ? 'selected' : '' }}>
                    {{ $tech->name }} ({{ $tech->role->label() }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 pt-6">
        <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">ملاحظات وتقرير الفني</label>
            <textarea name="technician_notes" rows="5" placeholder="أدخل تفاصيل حالة المولد، الفحص الفني، وقطع الغيار المطلوبة/المستبدلة..."
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">{{ old('technician_notes', $visit->technician_notes) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-600 mb-2">ملاحظات العميل</label>
            <textarea name="customer_notes" rows="5" placeholder="ملاحظات العميل أو شكاوى محددة بخصوص المولد..."
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">{{ old('customer_notes', $visit->customer_notes) }}</textarea>
        </div>
    </div>

    <div class="flex items-center gap-3 border-t border-gray-100 pt-6">
        <button type="submit" class="bg-[#00d26a] hover:bg-green-500 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition">
            حفظ التغييرات
        </button>
        <a href="{{ route('dashboard.visits.show', $visit) }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2.5 rounded-xl text-sm font-bold transition">
            إلغاء
        </a>
    </div>
</form>
@endsection
