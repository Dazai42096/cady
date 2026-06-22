@extends('layouts.portal')

@section('title', 'عروض الأسعار الخاصة بي - CADY EST')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('portal.index') }}" class="text-gray-500 hover:text-gray-700 transition">
        &larr; العودة للوحة التحكم
    </a>
    <span class="text-gray-300">|</span>
    <h2 class="text-2xl font-bold text-[#0b192c]">عروض الأسعار الخاصة بي</h2>
</div>

{{-- Quotations Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($quotations->isEmpty())
        <div class="p-8 text-center text-gray-400">
            لا توجد عروض أسعار مسجلة لحسابك بعد.
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">رقم العرض</th>
                    <th class="px-5 py-4">الموضوع</th>
                    <th class="px-5 py-4">التاريخ</th>
                    <th class="px-5 py-4">تاريخ الانتهاء</th>
                    <th class="px-5 py-4">المبلغ الإجمالي</th>
                    <th class="px-5 py-4">الحالة</th>
                    <th class="px-5 py-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($quotations as $quotation)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4 font-semibold text-gray-900 font-mono">
                        {{ $quotation->ref_number }}
                    </td>
                    <td class="px-5 py-4 font-semibold">
                        {{ $quotation->subject }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 font-mono">
                        {{ $quotation->date }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 font-mono">
                        {{ $quotation->valid_until }}
                    </td>
                    <td class="px-5 py-4 font-bold text-gray-950 font-mono">
                        {{ number_format($quotation->total_amount, 2) }} ر.س
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $badgeType = match($quotation->status->value) {
                                'accepted' => 'success',
                                'sent' => 'info',
                                'draft' => 'default',
                                'rejected' => 'danger',
                                default => 'default',
                            };
                        @endphp
                        <x-badge :type="$badgeType">
                            {{ $quotation->status->label() }}
                        </x-badge>
                    </td>
                    <td class="px-5 py-4">
                        @if(in_array($quotation->status->value, ['sent', 'accepted']))
                            <a href="{{ route('portal.quotations.pdf', $quotation) }}" 
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-750 px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1.5 w-fit">
                                📥 تحميل PDF
                            </a>
                        @else
                            <span class="text-xs text-gray-400">غير متوفر للتحميل</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100 font-mono">
        {{ $quotations->links() }}
    </div>
    @endif
</div>
@endsection
