@extends('layouts.dashboard')

@section('title', '{{ $customer->company_name }} - CADY EST')
@section('page_title', 'بيانات العميل')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">{{ $customer->company_name }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $customer->business_activity }}</p>
    </div>
    <div class="flex items-center gap-3">
        @can('update', $customer)
        <a href="{{ route('dashboard.customers.edit', $customer) }}"
           class="bg-amber-500 hover:bg-amber-400 text-white px-4 py-2 rounded-xl text-sm font-bold transition">
            تعديل
        </a>
        @endcan
        <a href="{{ route('dashboard.customers.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold transition">
            → القائمة
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Customer Info --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-[#0b192c]">بيانات التواصل</h3>
                <x-badge :status="$customer->status" />
            </div>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-400 font-medium mb-1">جهة الاتصال</dt>
                    <dd class="text-gray-800 font-semibold">{{ $customer->contact_person }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium mb-1">رقم الهاتف</dt>
                    <dd class="text-gray-800 font-semibold dir-ltr">{{ $customer->phone }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium mb-1">البريد الإلكتروني</dt>
                    <dd class="text-gray-800 dir-ltr">{{ $customer->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium mb-1">العنوان</dt>
                    <dd class="text-gray-800">{{ $customer->address ?? '—' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-gray-400 font-medium mb-1">ملاحظات</dt>
                    <dd class="text-gray-600">{{ $customer->notes ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Generators List --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-[#0b192c]">المولدات الكهربائية ({{ $generatorsCount }})</h3>
                @can('create', App\Models\Generator::class)
                <a href="{{ route('dashboard.generators.create') }}"
                   class="text-xs bg-[#00d26a] text-white px-3 py-1.5 rounded-lg font-bold hover:bg-green-500 transition">
                    + إضافة مولد
                </a>
                @endcan
            </div>
            @if($customer->generators->isEmpty())
                <p class="text-sm text-gray-400 py-4 text-center">لا توجد مولدات مسجلة لهذا العميل</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="text-xs text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="pb-2">المسلسل</th>
                            <th class="pb-2">الماركة / الموديل</th>
                            <th class="pb-2">القدرة</th>
                            <th class="pb-2">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($customer->generators as $gen)
                        <tr>
                            <td class="py-2">
                                <a href="{{ route('dashboard.generators.show', $gen) }}"
                                   class="font-mono text-[#0b192c] hover:text-[#00d26a] font-semibold text-xs transition">
                                    {{ $gen->serial_number }}
                                </a>
                            </td>
                            <td class="py-2 text-gray-600">{{ $gen->brand }} / {{ $gen->model }}</td>
                            <td class="py-2 text-gray-600">{{ number_format($gen->capacity_kva, 0) }} KVA</td>
                            <td class="py-2"><x-badge :status="$gen->status" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar Stats & Actions --}}
    <div class="space-y-6">
        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-blue-50 rounded-2xl p-4 text-center">
                <p class="text-2xl font-bold text-blue-700">{{ $generatorsCount }}</p>
                <p class="text-xs text-blue-500 mt-1">مولدات</p>
            </div>
            <div class="bg-purple-50 rounded-2xl p-4 text-center">
                <p class="text-2xl font-bold text-purple-700">{{ $quotationsCount }}</p>
                <p class="text-xs text-purple-500 mt-1">عروض</p>
            </div>
            <div class="bg-green-50 rounded-2xl p-4 text-center">
                <p class="text-2xl font-bold text-green-700">{{ $contractsCount }}</p>
                <p class="text-xs text-green-500 mt-1">عقود</p>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">روابط سريعة</h3>
            <div class="space-y-2">
                <a href="{{ route('dashboard.quotations.index', ['search' => $customer->company_name]) }}"
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#0b192c] py-1.5 transition">
                    📄 <span>عروض الأسعار</span>
                    <span class="mr-auto bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $quotationsCount }}</span>
                </a>
                <a href="{{ route('dashboard.contracts.index', ['search' => $customer->company_name]) }}"
                   class="flex items-center gap-2 text-sm text-gray-600 hover:text-[#0b192c] py-1.5 transition">
                    ✍️ <span>عقود الصيانة</span>
                    <span class="mr-auto bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $contractsCount }}</span>
                </a>
            </div>
        </div>

        {{-- Admin Actions --}}
        @can('approve', $customer)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-[#0b192c] mb-4 pb-3 border-b border-gray-100">إجراءات الإدارة</h3>
            <div class="space-y-2">
                @if($customer->status->value === 'pending_admin_link')
                <form action="{{ route('dashboard.customers.approve', $customer) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        ✓ تفعيل العميل
                    </button>
                </form>
                <form action="{{ route('dashboard.customers.reject', $customer) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        ✗ رفض الطلب
                    </button>
                </form>
                @elseif($customer->status->value === 'active')
                <form action="{{ route('dashboard.customers.suspend', $customer) }}" method="POST"
                      onsubmit="return confirm('هل تريد إيقاف هذا العميل مؤقتاً؟')">
                    @csrf
                    <button type="submit"
                            class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        ⏸ إيقاف مؤقت
                    </button>
                </form>
                @elseif($customer->status->value === 'suspended')
                <form action="{{ route('dashboard.customers.approve', $customer) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        ▶ إعادة التفعيل
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endcan

        {{-- Meta --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 text-xs text-gray-400 space-y-1">
            <p>تاريخ الإضافة: {{ $customer->created_at->format('Y/m/d') }}</p>
            <p>آخر تحديث: {{ $customer->updated_at->diffForHumans() }}</p>
        </div>
    </div>
</div>
@endsection
