@extends('layouts.dashboard')

@section('title', 'إدارة العملاء - CADY EST')
@section('page_title', 'إدارة العملاء')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">العملاء</h2>
        <p class="text-sm text-gray-500 mt-1">إجمالي: {{ $customers->total() }} عميل</p>
    </div>
    @can('create', App\Models\Customer::class)
    <a href="{{ route('dashboard.customers.create') }}"
       class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
        <span>+</span> إضافة عميل جديد
    </a>
    @endcan
</div>

{{-- Search Bar --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('dashboard.customers.index') }}" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="بحث بالشركة، الهاتف، أو البريد..."
               class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none">
        <button type="submit"
                class="bg-[#0b192c] text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-navy-700 transition">
            بحث
        </button>
        @if(request('search'))
        <a href="{{ route('dashboard.customers.index') }}"
           class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-200 transition">
            مسح
        </a>
        @endif
    </form>
</div>

{{-- Customers Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($customers->isEmpty())
        <x-empty-state message="لا يوجد عملاء بعد. ابدأ بإضافة عميل جديد." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">الشركة</th>
                    <th class="px-5 py-4">جهة الاتصال</th>
                    <th class="px-5 py-4">الهاتف</th>
                    <th class="px-5 py-4">البريد الإلكتروني</th>
                    <th class="px-5 py-4">الحالة</th>
                    <th class="px-5 py-4">المولدات</th>
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
                        @if($customer->business_activity)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $customer->business_activity }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $customer->contact_person }}</td>
                    <td class="px-5 py-4 text-gray-600 dir-ltr text-right">{{ $customer->phone }}</td>
                    <td class="px-5 py-4 text-gray-600 dir-ltr">{{ $customer->email }}</td>
                    <td class="px-5 py-4">
                        <x-badge :status="$customer->status" />
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold">
                            {{ $customer->generators->count() }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('dashboard.customers.show', $customer) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg font-semibold transition">
                                عرض
                            </a>
                            @can('update', $customer)
                            <a href="{{ route('dashboard.customers.edit', $customer) }}"
                               class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg font-semibold transition">
                                تعديل
                            </a>
                            @endcan
                            @can('delete', $customer)
                            <form action="{{ route('dashboard.customers.destroy', $customer) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer">
                                    حذف
                                </button>
                            </form>
                            @endcan
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
    @endif
</div>
@endsection
