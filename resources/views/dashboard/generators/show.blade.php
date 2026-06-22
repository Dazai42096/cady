@extends('layouts.dashboard')
@section('title', 'تفاصيل المولد - CADY EST')
@section('page_title', 'تفاصيل المولد')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c] font-mono">{{ $generator->serial_number }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $generator->brand }} / {{ $generator->model }}</p>
    </div>
    <div class="flex items-center gap-3">
        @can('update', $generator)
        <a href="{{ route('dashboard.generators.edit', $generator) }}"
           class="bg-amber-500 hover:bg-amber-400 text-white px-4 py-2 rounded-xl text-sm font-bold transition">تعديل</a>
        @endcan
        <a href="{{ route('dashboard.generators.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold transition">→ القائمة</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-[#0b192c]">المواصفات الفنية</h3>
                <x-badge :status="$generator->status" />
            </div>
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <dt class="text-gray-400 text-xs mb-1">القدرة</dt>
                    <dd class="text-2xl font-bold text-[#0b192c]">{{ number_format($generator->capacity_kva, 0) }}</dd>
                    <dd class="text-xs text-gray-400">KVA</dd>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <dt class="text-gray-400 text-xs mb-1">الماركة</dt>
                    <dd class="font-bold text-[#0b192c]">{{ $generator->brand }}</dd>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <dt class="text-gray-400 text-xs mb-1">الموديل</dt>
                    <dd class="font-bold text-[#0b192c]">{{ $generator->model }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium mb-1">نوع الوقود</dt>
                    <dd class="font-semibold">
                        @switch($generator->fuel_type)
                            @case('diesel') ⛽ ديزل @break
                            @case('gas') 🔵 غاز طبيعي @break
                            @case('dual') ⚡ ثنائي الوقود @break
                        @endswitch
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium mb-1">الموقع</dt>
                    <dd class="font-semibold">{{ $generator->location ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium mb-1">تاريخ التسجيل</dt>
                    <dd class="text-gray-600">{{ $generator->created_at->format('Y/m/d') }}</dd>
                </div>
                @if($generator->notes)
                <div class="col-span-full">
                    <dt class="text-gray-400 font-medium mb-1">ملاحظات</dt>
                    <dd class="text-gray-600">{{ $generator->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Maintenance Contracts --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">
                عقود الصيانة ({{ $generator->maintenanceContracts->count() }})
            </h3>
            @if($generator->maintenanceContracts->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">لا توجد عقود صيانة لهذا المولد</p>
            @else
            <div class="space-y-3">
                @foreach($generator->maintenanceContracts as $contract)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div>
                        <a href="{{ route('dashboard.contracts.show', $contract) }}"
                           class="font-semibold text-[#0b192c] hover:text-[#00d26a] text-sm transition font-mono">
                            {{ $contract->ref_number }}
                        </a>
                        <p class="text-xs text-gray-400">{{ $contract->contract_start_date->format('Y/m/d') }} – {{ $contract->contract_end_date->format('Y/m/d') }}</p>
                    </div>
                    <x-badge :status="$contract->status" />
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">العميل المالك</h3>
            @if($generator->customer)
            <a href="{{ route('dashboard.customers.show', $generator->customer) }}"
               class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                <div class="w-10 h-10 bg-[#0b192c] rounded-xl flex items-center justify-center text-white font-bold text-sm">
                    {{ mb_substr($generator->customer->company_name, 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-[#0b192c] text-sm">{{ $generator->customer->company_name }}</p>
                    <p class="text-xs text-gray-400">{{ $generator->customer->phone }}</p>
                </div>
            </a>
            @endif
        </div>

        @can('delete', $generator)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-red-600 mb-4 pb-3 border-b border-gray-100">منطقة الخطر</h3>
            <form action="{{ route('dashboard.generators.destroy', $generator) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المولد نهائياً؟')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                    🗑 حذف المولد
                </button>
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
