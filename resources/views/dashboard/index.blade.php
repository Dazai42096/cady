@extends('layouts.dashboard')

@section('title', 'لوحة الإحصائيات العامة - كادي للمولدات')
@section('page_title', 'لوحة الإحصائيات العامة')

@section('content')
<div class="mb-8">
    <h3 class="text-sm font-bold text-gray-400 mb-3 uppercase tracking-wider">الإجراءات السريعة</h3>
    <div class="flex flex-wrap gap-4">
        
        @if(Auth::user()->isAdmin() || Auth::user()->isSales())
            <a href="{{ route('dashboard.customers.create') }}" class="bg-[#0b192c] hover:bg-navy-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center transition shadow-sm">
                <span class="ml-2">➕</span> إضافة عميل جديد
            </a>
            
            <a href="{{ route('dashboard.generators.create') }}" class="bg-[#0b192c] hover:bg-navy-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center transition shadow-sm">
                <span class="ml-2">🔌</span> إضافة مولد جديد
            </a>

            <a href="{{ route('dashboard.quotations.create') }}" class="bg-[#0b192c] hover:bg-navy-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center transition shadow-sm">
                <span class="ml-2">📄</span> إنشاء عرض سعر
            </a>

            <a href="{{ route('dashboard.contracts.create') }}" class="bg-[#0b192c] hover:bg-navy-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center transition shadow-sm">
                <span class="ml-2">✍️</span> تنظيم عقد صيانة
            </a>
        @endif

        @if(Auth::user()->isAdmin())
            <a href="{{ route('dashboard.customers.pending') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center transition shadow-sm">
                <span class="ml-2">⏳</span> معالجة طلبات الربط مع العملاء
            </a>
        @endif

    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <x-stat-card title="العملاء النشطين" :value="$stats['active_customers']" icon="👥" color="green" />
    
    @if(Auth::user()->isAdmin())
        <x-stat-card title="العملاء بانتظار التفعيل" :value="$stats['pending_customers']" icon="⏳" color="warning" />
    @endif
    
    <x-stat-card title="إجمالي المولدات" :value="$stats['total_generators']" icon="⚡" color="navy" />
    
    <x-stat-card title="عقود الصيانة السارية" :value="$stats['active_contracts']" icon="✍️" color="green" />

    <x-stat-card title="قيمة العقود النشطة" :value="number_format($stats['total_contract_value'], 2) . ' JOD'" icon="💰" color="navy" />
    
    <x-stat-card title="المولدات المتوفرة" :value="$stats['available_generators']" icon="⚙️" color="green" />
    
    <x-stat-card title="إجمالي عروض الأسعار" :value="$stats['total_quotations']" icon="📄" color="navy" />

    <x-stat-card title="الزيارات القادمة المجدولة" :value="$stats['upcoming_visits']" icon="🔧" color="warning" />

</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-slate-900">أحدث العمليات وسجل التدقيق الفني</h3>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('dashboard.audit_logs.index') }}" class="text-[#00d26a] font-bold text-xs hover:underline">عرض جميع السجلات ←</a>
        @endif
    </div>

    @if($latestLogs->isEmpty())
        <x-empty-state title="لا توجد أنشطة" message="لم يتم تسجيل أي عمليات في النظام حالياً." />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 font-bold">
                        <th class="px-6 py-3">المستخدم</th>
                        <th class="px-6 py-3">العملية</th>
                        <th class="px-6 py-3">النوع/السجل</th>
                        <th class="px-6 py-3">عنوان IP</th>
                        <th class="px-6 py-3">الوقت والتاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-gray-600">
                    @foreach($latestLogs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $log->user->name ?? 'عملية النظام تلقائياً' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-slate-100 rounded text-xs font-mono font-bold text-slate-700">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400">
                                {{ $log->entity_type ? class_basename($log->entity_type) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-400">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
