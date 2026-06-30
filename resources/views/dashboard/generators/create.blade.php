@extends('layouts.dashboard')

@section('title', app()->getLocale() === 'ar' ? 'إضافة مولد جديد' : 'Add New Generator')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            {{ app()->getLocale() === 'ar' ? 'إضافة مولد جديد' : 'Add New Generator' }}
        </h1>
        <p class="text-slate-600 mt-1">
            {{ app()->getLocale() === 'ar' ? 'إضافة مولد كهربائي جديد إلى سجل المولدات.' : 'Add a new generator to the generator registry.' }}
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/dashboard/generators" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'الرقم التسلسلي' : 'Serial Number' }}</label>
                <input type="text" name="serial_number" required value="{{ old('serial_number') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'الموديل' : 'Model' }}</label>
                <input type="text" name="model" value="{{ old('model') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'الشركة المصنعة' : 'Brand' }}</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'القدرة KVA' : 'Capacity KVA' }}</label>
                <input type="number" step="0.1" min="0" name="capacity_kva" value="{{ old('capacity_kva') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</label>
                <input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</label>
                <select name="status" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="available">{{ app()->getLocale() === 'ar' ? 'متاح' : 'Available' }}</option>
                    <option value="rented">{{ app()->getLocale() === 'ar' ? 'مؤجر' : 'Rented' }}</option>
                    <option value="maintenance">{{ app()->getLocale() === 'ar' ? 'صيانة' : 'Maintenance' }}</option>
                    <option value="inactive">{{ app()->getLocale() === 'ar' ? 'غير فعال' : 'Inactive' }}</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes' }}</label>
            <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3">
            <button class="rounded-xl bg-emerald-600 text-white font-bold px-5 py-3">
                {{ app()->getLocale() === 'ar' ? 'إضافة المولد' : 'Add Generator' }}
            </button>

            <a href="{{ route('dashboard.generators.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">
                {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
            </a>
        </div>
    </form>
</div>
@endsection