@extends('layouts.dashboard')

@section('title', 'عروض الأسعار - CADY EST')
@section('page_title', 'عروض الأسعار')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#0b192c]">إدارة عروض الأسعار</h2>
            <p class="text-sm text-gray-500 mt-1">
                إجمالي: {{ method_exists($quotations, 'total') ? $quotations->total() : $quotations->count() }} عرض
            </p>
        </div>

        <a href="{{ route('dashboard.quotations.create') }}"
           class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-3 rounded-xl font-bold text-sm transition">
            + إنشاء عرض سعر
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('dashboard.quotations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="ابحث برقم العرض، اسم العميل، أو المشروع..."
                   class="md:col-span-2 px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">

            <select name="status"
                    class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00d26a] outline-none text-sm">
                <option value="">كل الحالات</option>
                <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                <option value="sent" @selected(request('status') === 'sent')>تم الإرسال</option>
                <option value="accepted" @selected(request('status') === 'accepted')>مقبول</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
            </select>

            <button type="submit"
                    class="bg-[#0b192c] text-white px-5 py-2.5 rounded-xl font-bold text-sm">
                بحث
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#0b192c] text-white">
                <tr>
                    <th class="px-4 py-4 text-right">رقم العرض</th>
                    <th class="px-4 py-4 text-right">العميل</th>
                    <th class="px-4 py-4 text-right">نوع العرض</th>
                    <th class="px-4 py-4 text-right">التاريخ</th>
                    <th class="px-4 py-4 text-right">الإجمالي</th>
                    <th class="px-4 py-4 text-right">الحالة</th>
                    <th class="px-4 py-4 text-right">الإجراءات</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($quotations as $quotation)
                    @php
                        $statusLabel = is_object($quotation->status) && method_exists($quotation->status, 'label')
                            ? $quotation->status->label()
                            : (string) ($quotation->status->value ?? $quotation->status ?? 'غير محدد');

                        $typeLabel = is_object($quotation->type) && method_exists($quotation->type, 'label')
                            ? $quotation->type->label()
                            : (string) ($quotation->type->value ?? $quotation->type ?? 'غير محدد');

                        $date = $quotation->quotation_date ?? $quotation->date ?? optional($quotation->created_at)->toDateString();
                        $total = $quotation->total ?? $quotation->total_amount ?? 0;
                        $currency = $quotation->currency ?? 'JOD';
                    @endphp

                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 font-bold text-[#0b192c]">
                            {{ $quotation->ref_number }}
                        </td>

                        <td class="px-4 py-4">
                            @if($quotation->customer)
                                <a href="{{ route('dashboard.customers.show', $quotation->customer) }}"
                                   class="text-blue-600 hover:underline">
                                    {{ $quotation->customer->company_name ?? 'غير محدد' }}
                                </a>
                            @else
                                غير محدد
                            @endif
                        </td>

                        <td class="px-4 py-4 text-gray-700">
                            {{ $typeLabel }}
                        </td>

                        <td class="px-4 py-4 text-gray-600">
                            {{ $date }}
                        </td>

                        <td class="px-4 py-4 font-bold text-[#0b192c]">
                            {{ number_format($total, 2) }} {{ $currency }}
                        </td>

                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('dashboard.quotations.show', $quotation) }}"
                                   class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold hover:bg-blue-100">
                                    عرض
                                </a>

                                <a href="{{ route('dashboard.quotations.pdf', $quotation) }}"
                                   class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-700 text-xs font-bold hover:bg-gray-100">
                                    PDF
                                </a>

                                <a href="{{ route('dashboard.quotations.edit', $quotation) }}"
                                   class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold hover:bg-amber-100">
                                    تعديل
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                            لا توجد عروض أسعار حتى الآن.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($quotations, 'links'))
            <div class="p-4 border-t border-gray-100">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
