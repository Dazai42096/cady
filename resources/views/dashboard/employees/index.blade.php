@extends('layouts.dashboard')

@section('title', app()->getLocale() === 'ar' ? 'إدارة الموظفين' : 'Employees')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                {{ app()->getLocale() === 'ar' ? 'إدارة الموظفين' : 'Employees' }}
            </h1>
            <p class="text-slate-600 mt-1">
                {{ app()->getLocale() === 'ar' ? 'المدير هو المسؤول عن إضافة موظفي المبيعات والدعم الفني.' : 'Admin can create Sales and Technical Support employee accounts.' }}
            </p>
        </div>

        <a href="{{ route('dashboard.employees.create') }}" style="background:#059669;color:white;padding:12px 20px;border-radius:12px;font-weight:700;text-decoration:none;">
            {{ app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'Add New Employee' }}
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full Name' }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone' }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">{{ app()->getLocale() === 'ar' ? 'الدور' : 'Role' }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($employees as $employee)
                    <tr>
                        <td class="px-4 py-4 font-bold">{{ $employee->name }}</td>
                        <td class="px-4 py-4">{{ $employee->email }}</td>
                        <td class="px-4 py-4">{{ $employee->phone ?? '-' }}</td>
                        <td class="px-4 py-4">
                            <span class="rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-bold">
                                {{ $employee->role === 'support' ? (app()->getLocale() === 'ar' ? 'الدعم الفني' : 'Technical Support') : ucfirst($employee->role) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                            {{ app()->getLocale() === 'ar' ? 'لا يوجد موظفين.' : 'No employees found.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-200">{{ $employees->links() }}</div>
    </div>
</div>
@endsection