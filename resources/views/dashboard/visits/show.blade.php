@extends('layouts.dashboard')

@section('title', 'تفاصيل زيارة الصيانة - CADY EST')
@section('page_title', 'تفاصيل زيارة الصيانة')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard.visits.index') }}" class="text-gray-500 hover:text-gray-700 transition">
            &larr; العودة للزيارات
        </a>
        <span class="text-gray-300">|</span>
        <h2 class="text-2xl font-bold text-[#0b192c]">تفاصيل الزيارة الميدانية</h2>
    </div>
    
    @can('update', $visit)
    <a href="{{ route('dashboard.visits.edit', $visit) }}"
       class="bg-[#0b192c] hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition">
        تعديل الزيارة
    </a>
    @endcan
</div>

{{-- Message Alerts --}}
@if(session('success'))
<div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 text-sm font-medium">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 text-sm font-medium">
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Right Section: Visit info & notes --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- General Info Card --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between border-b border-gray-150 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-400">حالة الزيارة:</span>
                    @php
                        $badgeType = match($visit->status->value) {
                            'scheduled' => 'info',
                            'confirmed' => 'warning',
                            'in_progress' => 'navy',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'default',
                        };
                    @endphp
                    <x-badge :type="$badgeType" class="text-xs">
                        {{ $visit->status->label() }}
                    </x-badge>
                </div>
                <div class="text-sm text-gray-500">
                    رقم العقد المرجعي: 
                    @if($visit->contract)
                        <a href="{{ route('dashboard.contracts.show', $visit->contract) }}" class="text-[#00d26a] hover:underline font-semibold font-mono">
                            {{ $visit->contract->ref_number }}
                        </a>
                    @else
                        <span class="text-gray-400">غير محدد</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="block text-xs font-semibold text-gray-400 mb-1">التاريخ المخطط</span>
                    <span class="text-sm font-bold text-gray-800 font-mono">{{ $visit->planned_date }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400 mb-1">تاريخ التأكيد</span>
                    <span class="text-sm font-bold text-gray-800 font-mono">{{ $visit->confirmed_date ?? 'غير مؤكدة بعد' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400 mb-1">تاريخ الزيارة الفعلي</span>
                    <span class="text-sm font-bold text-gray-800 font-mono">{{ $visit->actual_date ?? 'لم تبدأ بعد' }}</span>
                </div>
            </div>
            
            <div class="border-t border-gray-150 mt-6 pt-6">
                <span class="block text-xs font-semibold text-gray-400 mb-1">الفني المسؤول</span>
                <span class="text-sm font-bold text-gray-800">{{ $visit->technician->name ?? 'لم يتم تعيين فني بعد' }}</span>
            </div>
        </div>

        {{-- Action workflow triggers --}}
        @can('updateStatus', $visit)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4">إجراءات سير العمل</h3>
            <div class="flex flex-wrap gap-3">
                @if($visit->status->value === 'scheduled')
                    <form action="{{ route('dashboard.visits.confirm', $visit) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-xl text-sm font-bold transition cursor-pointer">
                            تأكيد موعد الزيارة
                        </button>
                    </form>
                    <form action="{{ route('dashboard.visits.cancel', $visit) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه الزيارة؟')">
                        @csrf
                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-650 px-5 py-2 rounded-xl text-sm font-bold transition cursor-pointer">
                            إلغاء الزيارة
                        </button>
                    </form>
                @endif

                @if($visit->status->value === 'confirmed')
                    <form action="{{ route('dashboard.visits.start', $visit) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-bold transition cursor-pointer">
                            بدء الزيارة الميدانية (قيد العمل)
                        </button>
                    </form>
                    <form action="{{ route('dashboard.visits.cancel', $visit) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه الزيارة؟')">
                        @csrf
                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-650 px-5 py-2 rounded-xl text-sm font-bold transition cursor-pointer">
                            إلغاء الزيارة
                        </button>
                    </form>
                @endif

                @if($visit->status->value === 'in_progress')
                    <form action="{{ route('dashboard.visits.complete', $visit) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-2 rounded-xl text-sm font-bold transition cursor-pointer">
                            إتمام الزيارة بنجاح
                        </button>
                    </form>
                @endif
                
                @if($visit->status->value === 'completed')
                    <div class="text-sm font-medium text-green-700 bg-green-50 px-4 py-2 rounded-xl border border-green-200">
                        ✓ هذه الزيارة تمت بنجاح واكتملت
                    </div>
                @endif

                @if($visit->status->value === 'cancelled')
                    <div class="text-sm font-medium text-red-700 bg-red-50 px-4 py-2 rounded-xl border border-red-200">
                        ✗ تم إلغاء هذه الزيارة
                    </div>
                @endif
            </div>
        </div>
        @endcan

        {{-- Notes Card --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-[#0b192c] mb-6">ملاحظات وتقرير الزيارة</h3>
            
            <div class="space-y-6">
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 mb-2">تقرير/ملاحظات الفني</h4>
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 min-h-24">
                        {!! nl2br(e($visit->technician_notes ?? 'لا توجد ملاحظات فنية مسجلة حتى الآن.')) !!}
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-semibold text-gray-400 mb-2">ملاحظات العميل وملاحظات الفحص المشترك</h4>
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-700 min-h-24">
                        {!! nl2br(e($visit->customer_notes ?? 'لا توجد ملاحظات من العميل.')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Left Section: Customer & Generator short view --}}
    <div class="space-y-6">
        {{-- Customer details --}}
        @if($visit->contract && $visit->contract->customer)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] border-b border-gray-150 pb-3 mb-4">بيانات العميل</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-xs font-semibold text-gray-400">اسم الشركة</span>
                    <a href="{{ route('dashboard.customers.show', $visit->contract->customer) }}" class="font-bold text-[#0b192c] hover:text-[#00d26a]">
                        {{ $visit->contract->customer->company_name }}
                    </a>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400">جهة الاتصال</span>
                    <span class="font-medium text-gray-700">{{ $visit->contract->customer->contact_person }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400">رقم الهاتف</span>
                    <span class="font-medium text-gray-700 dir-ltr inline-block">{{ $visit->contract->customer->phone }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400">البريد الإلكتروني</span>
                    <span class="font-medium text-gray-700 dir-ltr inline-block">{{ $visit->contract->customer->email }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Generator details --}}
        @if($visit->contract && $visit->contract->generator)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] border-b border-gray-150 pb-3 mb-4">بيانات المولد</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-xs font-semibold text-gray-400">الماركة / الموديل</span>
                    <a href="{{ route('dashboard.generators.show', $visit->contract->generator) }}" class="font-bold text-[#0b192c] hover:text-[#00d26a]">
                        {{ $visit->contract->generator->brand }} - {{ $visit->contract->generator->model }}
                    </a>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400">القدرة (KVA)</span>
                    <span class="font-bold text-[#0b192c]">{{ $visit->contract->generator->capacity_kva }} KVA</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400">الرقم التسلسلي (S/N)</span>
                    <span class="font-medium text-gray-700 font-mono">{{ $visit->contract->generator->serial_number }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-gray-400">موقع المولد</span>
                    <span class="font-medium text-gray-700">{{ $visit->contract->generator->location ?? 'غير محدد' }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
