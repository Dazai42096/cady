@extends('layouts.dashboard')

@section('title', 'Rental Control - CADY EST')
@section('page_title', 'Rental Control / إدارة التأجير')

@section('content')
<div class="space-y-6" dir="rtl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#0b192c]">إدارة التأجير</h2>
            <p class="text-sm text-gray-500 mt-1">متابعة عروض التأجير والمولدات المؤجرة.</p>
        </div>
        <a href="{{ route('dashboard.quotations.create') }}" class="bg-[#00d26a] text-white px-4 py-2 rounded-xl text-sm font-bold">+ إنشاء عرض تأجير</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-sm text-gray-500">عروض التأجير</p>
            <p class="text-3xl font-bold text-[#0b192c] mt-2">{{ $rentalQuotations->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <p class="text-sm text-gray-500">المولدات المؤجرة حالياً</p>
            <p class="text-3xl font-bold text-[#0b192c] mt-2">{{ $rentedGenerators->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 font-bold text-[#0b192c]">عروض التأجير</div>
        <table class="w-full text-sm">
            <thead class="bg-[#0b192c] text-white">
                <tr>
                    <th class="px-5 py-3 text-right">رقم العرض</th>
                    <th class="px-5 py-3 text-right">العميل</th>
                    <th class="px-5 py-3 text-right">التاريخ</th>
                    <th class="px-5 py-3 text-right">الإجمالي</th>
                    <th class="px-5 py-3 text-right">الحالة</th>
                    <th class="px-5 py-3 text-right">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rentalQuotations as $quotation)
                    <tr>
                        <td class="px-5 py-3 font-bold">{{ $quotation->ref_number }}</td>
                        <td class="px-5 py-3">{{ $quotation->customer?->company_name }}</td>
                        <td class="px-5 py-3">{{ $quotation->quotation_date?->format('Y-m-d') }}</td>
                        <td class="px-5 py-3">{{ $quotation->currency }} {{ number_format($quotation->total, 2) }}</td>
                        <td class="px-5 py-3"><x-badge :status="$quotation->status" /></td>
                        <td class="px-5 py-3"><a class="text-blue-600 font-bold" href="{{ route('dashboard.quotations.show', $quotation) }}">عرض</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">لا توجد عروض تأجير بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $rentalQuotations->links() }}</div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 font-bold text-[#0b192c]">المولدات المؤجرة</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-5 py-3 text-right">الرقم التسلسلي</th>
                    <th class="px-5 py-3 text-right">الماركة / الموديل</th>
                    <th class="px-5 py-3 text-right">القدرة</th>
                    <th class="px-5 py-3 text-right">العميل</th>
                    <th class="px-5 py-3 text-right">الموقع</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rentedGenerators as $generator)
                    <tr>
                        <td class="px-5 py-3 font-bold">{{ $generator->serial_number }}</td>
                        <td class="px-5 py-3">{{ $generator->brand }} / {{ $generator->model }}</td>
                        <td class="px-5 py-3">{{ $generator->capacity_kva }} KVA</td>
                        <td class="px-5 py-3">{{ $generator->customer?->company_name ?? 'غير مرتبط' }}</td>
                        <td class="px-5 py-3">{{ $generator->location }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">لا توجد مولدات مؤجرة حالياً.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection