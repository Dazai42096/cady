@extends('layouts.dashboard')

@section('title', app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'Add New Employee')

@section('content')
<div class="max-w-3xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            {{ app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'Add New Employee' }}
        </h1>
        <p class="text-slate-600 mt-1">
            {{ app()->getLocale() === 'ar' ? 'المدير فقط يستطيع إنشاء حسابات موظفي المبيعات والدعم الفني.' : 'Only admin can create Sales and Technical Support accounts.' }}
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/dashboard/employees" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full Name' }}</label>
            <input type="text" name="name" required value="{{ old('name') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}</label>
            <input type="text" name="phone" required value="{{ old('phone') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
            <input type="email" name="email" required value="{{ old('email') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'الدور الوظيفي' : 'Employee Role' }}</label>
            <select name="role" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                <option value="sales" @selected(old('role') === 'sales')>{{ app()->getLocale() === 'ar' ? 'مبيعات' : 'Sales' }}</option>
                <option value="support" @selected(old('role') === 'support')>{{ app()->getLocale() === 'ar' ? 'الدعم الفني' : 'Technical Support' }}</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'كلمة السر' : 'Password' }}</label>
            <input type="password" name="password" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">{{ app()->getLocale() === 'ar' ? 'تأكيد كلمة السر' : 'Confirm Password' }}</label>
            <input type="password" name="password_confirmation" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
        </div>

        <div class="flex gap-3">
            <button class="rounded-xl bg-emerald-600 text-white font-bold px-5 py-3">
                {{ app()->getLocale() === 'ar' ? 'إنشاء الحساب' : 'Create Account' }}
            </button>

            <a href="{{ route('dashboard.employees.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">
                {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
            </a>
        </div>
    </form>
</div>
@endsection