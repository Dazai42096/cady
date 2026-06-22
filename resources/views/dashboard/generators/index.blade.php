@extends('layouts.dashboard')

@section('title', 'إدارة المولدات - CADY EST')
@section('page_title', 'إدارة المولدات الكهربائية')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">المولدات الكهربائية</h2>
        <p class="text-sm text-gray-500 mt-1">إجمالي: {{ $generators->total() }} مولد</p>
    </div>
    @can('create', App\Models\Generator::class)
    <a href="{{ route('dashboard.generators.create') }}"
       class="bg-[#00d26a] hover:bg-green-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
        <span>+</span> إضافة مولد جديد
    </a>
    @endcan
</div>

{{-- Search & Filter --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('dashboard.generators.index') }}" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="بحث بالمسلسل، الماركة، أو الموديل..."
               class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] focus:border-transparent outline-none">
        <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#00d26a] outline-none">
            <option value="">كل الحالات</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعّال</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير فعّال</option>
            <option value="under_maintenance" {{ request('status') == 'under_maintenance' ? 'selected' : '' }}>تحت الصيانة</option>
            <option value="decommissioned" {{ request('status') == 'decommissioned' ? 'selected' : '' }}>خارج الخدمة</option>
        </select>
        <button type="submit"
                class="bg-[#0b192c] text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-navy-700 transition">
            بحث
        </button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('dashboard.generators.index') }}"
           class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm hover:bg-gray-200 transition">مسح</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($generators->isEmpty())
        <x-empty-state message="لا توجد مولدات مسجلة بعد." />
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right text-gray-700">
            <thead class="bg-[#0b192c] text-white text-xs uppercase">
                <tr>
                    <th class="px-5 py-4">رقم المسلسل</th>
                    <th class="px-5 py-4">الماركة / الموديل</th>
                    <th class="px-5 py-4">القدرة (KVA)</th>
                    <th class="px-5 py-4">نوع الوقود</th>
                    <th class="px-5 py-4">العميل</th>
                    <th class="px-5 py-4">الموقع</th>
                    <th class="px-5 py-4">الحالة</th>
                    <th class="px-5 py-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($generators as $generator)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4">
                        <a href="{{ route('dashboard.generators.show', $generator) }}"
                           class="font-mono font-semibold text-[#0b192c] hover:text-[#00d26a] transition text-xs">
                            {{ $generator->serial_number }}
                        </a>
                    </td>
                    <td class="px-5 py-4">
                        <span class="font-semibold">{{ $generator->brand }}</span>
                        <span class="text-gray-400"> / {{ $generator->model }}</span>
                    </td>
                    <td class="px-5 py-4 text-center font-semibold text-[#0b192c]">
                        {{ number_format($generator->capacity_kva, 0) }}
                    </td>
                    <td class="px-5 py-4 text-gray-500">
                        @switch($generator->fuel_type)
                            @case('diesel') ⛽ ديزل @break
                            @case('gas') 🔵 غاز @break
                            @case('dual') ⚡ مزدوج @break
                        @endswitch
                    </td>
                    <td class="px-5 py-4">
                        @if($generator->customer)
                        <a href="{{ route('dashboard.customers.show', $generator->customer) }}"
                           class="text-blue-600 hover:text-blue-800 transition text-xs">
                            {{ $generator->customer->company_name }}
                        </a>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $generator->location ?? '—' }}</td>
                    <td class="px-5 py-4"><x-badge :status="$generator->status" /></td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('dashboard.generators.show', $generator) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg font-semibold transition">عرض</a>
                            @can('update', $generator)
                            <a href="{{ route('dashboard.generators.edit', $generator) }}"
                               class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-lg font-semibold transition">تعديل</a>
                            @endcan
                            @can('delete', $generator)
                            <form action="{{ route('dashboard.generators.destroy', $generator) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المولد؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer">حذف</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $generators->links() }}</div>
    @endif
</div>
@endsection
