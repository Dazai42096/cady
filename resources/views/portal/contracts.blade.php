@extends('layouts.portal')

@section('title', 'عقود الصيانة الخاصة بي - CADY EST')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('portal.index') }}" class="text-gray-500 hover:text-gray-700 transition">
        &larr; العودة للوحة التحكم
    </a>
    <span class="text-gray-300">|</span>
    <h2 class="text-2xl font-bold text-[#0b192c]">عقود الصيانة الخاصة بي</h2>
</div>

{{-- Contracts Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($contracts->isEmpty())
        <div class="p-8 text-center text-gray-400">
            لا توجد عقود صيانة مسجلة لحسابك بعد.
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">رقم العقد</th>
                    <th class="px-5 py-4">المولد المتعاقد عليه</th>
                    <th class="px-5 py-4">تاريخ البدء</th>
                    <th class="px-5 py-4">تاريخ الانتهاء</th>
                    <th class="px-5 py-4">قيمة العقد</th>
                    <th class="px-5 py-4">الحالة</th>
                    <th class="px-5 py-4">التحميل</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($contracts as $contract)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4 font-semibold text-gray-900 font-mono">
                        {{ $contract->ref_number }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="font-semibold text-gray-800">
                            {{ $contract->generator->brand }} ({{ $contract->generator->model }})
                        </span>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">S/N: {{ $contract->generator->serial_number }}</p>
                    </td>
                    <td class="px-5 py-4 text-gray-500 font-mono">
                        {{ $contract->start_date }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 font-mono">
                        {{ $contract->end_date }}
                    </td>
                    <td class="px-5 py-4 font-bold text-gray-950 font-mono">
                        {{ number_format($contract->contract_value, 2) }} ر.س
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $badgeType = match($contract->status->value) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'suspended', 'terminated' => 'danger',
                                default => 'default',
                            };
                        @endphp
                        <x-badge :type="$badgeType">
                            {{ $contract->status->label() }}
                        </x-badge>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('portal.contracts.pdf', $contract) }}" 
                           class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-750 px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5 w-fit">
                            📥 تحميل PDF
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100 font-mono">
        {{ $contracts->links() }}
    </div>
    @endif
</div>
@endsection
