@extends('layouts.dashboard')

@section('title', 'العملاء المعلقون - CADY EST')
@section('page_title', 'العملاء في انتظار الموافقة')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">طلبات العملاء المعلقة</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $customers->total() }} طلب بانتظار مراجعتك</p>
    </div>
    <a href="{{ route('dashboard.customers.index') }}"
       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold transition">
        → جميع العملاء
    </a>
</div>

@if($customers->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
        <div class="text-5xl mb-4">✅</div>
        <h3 class="text-lg font-bold text-gray-700 mb-2">لا توجد طلبات معلقة</h3>
        <p class="text-sm text-gray-400">جميع طلبات العملاء تمت معالجتها</p>
    </div>
@else
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">الشركة</th>
                    <th class="px-5 py-4">جهة الاتصال</th>
                    <th class="px-5 py-4">الهاتف</th>
                    <th class="px-5 py-4">البريد</th>
                    <th class="px-5 py-4">النشاط</th>
                    <th class="px-5 py-4">تاريخ الطلب</th>
                    <th class="px-5 py-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($customers as $customer)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <a href="{{ route('dashboard.customers.show', $customer) }}"
                           class="font-semibold text-[#0b192c] hover:text-[#00d26a] transition">
                            {{ $customer->company_name }}
                        </a>
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $customer->contact_person }}</td>
                    <td class="px-5 py-4 text-gray-600 dir-ltr">{{ $customer->phone }}</td>
                    <td class="px-5 py-4 text-gray-600 dir-ltr text-xs">{{ $customer->email }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $customer->business_activity ?? '—' }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $customer->created_at->format('Y/m/d') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <form action="{{ route('dashboard.customers.approve', $customer) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-bold transition cursor-pointer">
                                    ✓ تفعيل
                                </button>
                            </form>
                            <form action="{{ route('dashboard.customers.reject', $customer) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                                @csrf
                                <button type="submit"
                                        class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg font-bold transition cursor-pointer">
                                    ✗ رفض
                                </button>
                            </form>
                            <a href="{{ route('dashboard.customers.show', $customer) }}"
                               class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg font-semibold transition">
                                عرض
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $customers->links() }}
    </div>
</div>
@endif
@endsection
