@extends('layouts.portal')

@section('title', 'بوابة العميل - CADY EST')

@section('content')
{{-- Welcome banner --}}
<div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <span class="text-xs font-bold text-[#00d26a] tracking-widest uppercase">لوحة التحكم</span>
        <h1 class="text-3xl font-extrabold text-[#0b192c] mt-1">مرحباً، {{ $customer->contact_person }}</h1>
        <p class="text-gray-500 text-sm mt-1">مرحباً بك في بوابة الخدمة الذاتية لمؤسسة كادي. يمكنك متابعة المولدات، العقود، الزيارات، وتحميل عروض الأسعار.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-sm font-semibold text-gray-400">حالة الحساب:</span>
        @php
            $badgeType = match($customer->status->value) {
                'active' => 'success',
                'pending' => 'warning',
                'suspended', 'inactive' => 'danger',
                default => 'default',
            };
        @endphp
        <x-badge :type="$badgeType">
            {{ $customer->status->label() }}
        </x-badge>
    </div>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase">المولدات المسجلة</span>
            <h3 class="text-3xl font-black text-[#0b192c] mt-1">{{ $generatorsCount }}</h3>
        </div>
        <div class="bg-blue-50 text-blue-600 p-3.5 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase">عقود الصيانة النشطة</span>
            <h3 class="text-3xl font-black text-[#0b192c] mt-1">{{ $activeContractsCount }}</h3>
        </div>
        <div class="bg-green-50 text-[#00d26a] p-3.5 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-gray-400 uppercase">عروض الأسعار المعتمدة</span>
            <h3 class="text-3xl font-black text-[#0b192c] mt-1">{{ $sentQuotationsCount }}</h3>
        </div>
        <div class="bg-amber-50 text-amber-600 p-3.5 rounded-xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Left side column: Upcoming visits & Quick actions --}}
    <div class="space-y-8">
        {{-- Upcoming visits --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-base font-extrabold text-[#0b192c] mb-4 border-b border-gray-150 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                زيارات الصيانة القادمة
            </h3>
            
            @if($upcomingVisits->isEmpty())
                <p class="text-sm text-gray-400 py-3 text-center">لا توجد زيارات مجدولة قريباً.</p>
            @else
                <div class="space-y-4">
                    @foreach($upcomingVisits as $visit)
                        <div class="border border-gray-100 rounded-xl p-3.5 hover:border-gray-200 transition">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-gray-500 font-mono">{{ $visit->planned_date }}</span>
                                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-semibold">{{ $visit->status->label() }}</span>
                            </div>
                            <p class="text-xs text-gray-500">
                                المولد: {{ $visit->contract->generator->brand }} ({{ $visit->contract->generator->model }})
                            </p>
                            <p class="text-xs text-gray-450 mt-1">
                                الفني: {{ $visit->technician->name ?? 'قيد التعيين' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Support card --}}
        <div class="bg-gradient-to-br from-[#0b192c] to-[#122c4d] text-white rounded-2xl p-6 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-lg font-bold">هل تحتاج مساعدة فنية؟</h3>
                <p class="text-xs text-gray-300 mt-2 leading-relaxed">فريق الصيانة والدعم الفني لدينا متاح دائماً للرد على بلاغات الأعطال الطارئة على مدار 24 ساعة.</p>
                <div class="mt-4 pt-3 border-t border-navy-700 space-y-2">
                    <p class="text-xs text-gray-400">اتصل بنا على الخط الساخن:</p>
                    <p class="text-lg font-black text-[#00d26a] dir-ltr text-right">0790000000</p>
                </div>
            </div>
            <div class="absolute -bottom-8 -right-8 w-24 h-24 bg-navy-800 rounded-full opacity-30"></div>
        </div>
    </div>

    {{-- Right side columns: Generators, Contracts, Quotations --}}
    <div class="lg:col-span-2 space-y-8">
        {{-- Generators list --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-base font-extrabold text-[#0b192c] mb-4 border-b border-gray-150 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    المولدات التابعة لك
                </span>
            </h3>
            
            @if($customer->generators->isEmpty())
                <p class="text-sm text-gray-400 py-3 text-center">لا توجد مولدات مسجلة.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-right text-gray-700">
                        <thead class="bg-gray-50 text-gray-500 font-bold">
                            <tr>
                                <th class="px-4 py-3">الرقم التسلسلي</th>
                                <th class="px-4 py-3">الماركة / الموديل</th>
                                <th class="px-4 py-3">القدرة (KVA)</th>
                                <th class="px-4 py-3">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($customer->generators as $gen)
                                <tr>
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900">{{ $gen->serial_number }}</td>
                                    <td class="px-4 py-3">{{ $gen->brand }} - {{ $gen->model }}</td>
                                    <td class="px-4 py-3 font-bold">{{ $gen->capacity_kva }} KVA</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $genBadge = match($gen->status->value) {
                                                'active' => 'success',
                                                'under_maintenance' => 'warning',
                                                'inactive', 'decommissioned' => 'danger',
                                                default => 'default',
                                            };
                                        @endphp
                                        <x-badge :type="$genBadge">{{ $gen->status->label() }}</x-badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Active Contracts list --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-base font-extrabold text-[#0b192c] mb-4 border-b border-gray-150 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    عقود الصيانة النشطة
                </span>
                <a href="{{ route('portal.contracts') }}" class="text-xs text-[#00d26a] hover:underline font-bold">عرض الكل &larr;</a>
            </h3>
            
            @if($customer->maintenanceContracts->isEmpty())
                <p class="text-sm text-gray-400 py-3 text-center">لا توجد عقود صيانة نشطة.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-right text-gray-700">
                        <thead class="bg-gray-50 text-gray-500 font-bold">
                            <tr>
                                <th class="px-4 py-3">رقم العقد</th>
                                <th class="px-4 py-3">المولد</th>
                                <th class="px-4 py-3">فترة العقد</th>
                                <th class="px-4 py-3">تنزيل العقد PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($customer->maintenanceContracts as $contract)
                                <tr>
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900">{{ $contract->ref_number }}</td>
                                    <td class="px-4 py-3">{{ $contract->generator->brand }} ({{ $contract->generator->capacity_kva }} KVA)</td>
                                    <td class="px-4 py-3">{{ $contract->start_date }} إلى {{ $contract->end_date }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('portal.contracts.pdf', $contract) }}" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1.5">
                                            📥 تحميل PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Recent Quotations list --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-base font-extrabold text-[#0b192c] mb-4 border-b border-gray-150 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    آخر عروض الأسعار
                </span>
                <a href="{{ route('portal.quotations') }}" class="text-xs text-[#00d26a] hover:underline font-bold">عرض الكل &larr;</a>
            </h3>
            
            @if($customer->quotations->isEmpty())
                <p class="text-sm text-gray-400 py-3 text-center">لا توجد عروض أسعار مرسلة.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-right text-gray-700">
                        <thead class="bg-gray-50 text-gray-500 font-bold">
                            <tr>
                                <th class="px-4 py-3">رقم العرض</th>
                                <th class="px-4 py-3">الموضوع</th>
                                <th class="px-4 py-3">المبلغ الإجمالي</th>
                                <th class="px-4 py-3">الحالة</th>
                                <th class="px-4 py-3">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($customer->quotations as $quotation)
                                <tr>
                                    <td class="px-4 py-3 font-mono font-bold text-gray-900">{{ $quotation->ref_number }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $quotation->subject }}</td>
                                    <td class="px-4 py-3 font-bold font-mono">{{ number_format($quotation->total_amount, 2) }} ر.س</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $qBadge = match($quotation->status->value) {
                                                'accepted' => 'success',
                                                'sent' => 'info',
                                                'draft' => 'default',
                                                'rejected' => 'danger',
                                                default => 'default',
                                            };
                                        @endphp
                                        <x-badge :type="$qBadge">{{ $quotation->status->label() }}</x-badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if(in_array($quotation->status->value, ['sent', 'accepted']))
                                            <a href="{{ route('portal.quotations.pdf', $quotation) }}" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1.5">
                                                📥 تحميل PDF
                                            </a>
                                        @else
                                            <span class="text-gray-400">غير متاح للتحميل</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
