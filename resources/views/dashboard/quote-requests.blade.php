@extends('layouts.dashboard')

@section('title', 'طلبات الموقع العام')
@section('page_title', 'طلبات الموقع العام')

@section('content')
<div class="space-y-6" dir="rtl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#0b192c]">طلبات الموقع العام</h1>
            <p class="text-sm text-gray-500 mt-1">طلبات عروض الأسعار القادمة من صفحة الموقع الخارجي.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-[#0b192c] text-white">
                <tr>
                    <th class="px-5 py-4 text-right">الشركة</th>
                    <th class="px-5 py-4 text-right">جهة الاتصال</th>
                    <th class="px-5 py-4 text-right">الهاتف</th>
                    <th class="px-5 py-4 text-right">البريد الإلكتروني</th>
                    <th class="px-5 py-4 text-right">نوع الخدمة</th>
                    <th class="px-5 py-4 text-right">الحالة</th>
                    <th class="px-5 py-4 text-right">الإجراءات</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $request)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-bold text-[#0b192c]">
                                {{ $request->company_name }}
                            </div>
                            <div class="mt-1 text-xs text-gray-400">
                                {{ $request->created_at?->format('Y-m-d H:i') }}
                            </div>
                        </td>

                        <td class="px-5 py-4 text-gray-700">
                            {{ $request->contact_person }}
                        </td>

                        <td class="px-5 py-4 text-gray-700">
                            {{ $request->phone }}
                        </td>

                        <td class="px-5 py-4 text-gray-700">
                            {{ $request->email }}
                        </td>

                        <td class="px-5 py-4 text-gray-700">
                            {{ $request->service_type }}
                        </td>

                        <td class="px-5 py-4">
                            @if($request->status === 'processed')
                                <span class="inline-flex rounded-md border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700">
                                    تمت المعالجة
                                </span>
                            @else
                                <span class="inline-flex rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">
                                    جديد
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @if($request->status !== 'processed')
                                <form method="POST" action="{{ url('/dashboard/quote-requests/' . $request->id . '/process') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-[#00d26a] px-3 py-2 text-xs font-bold text-white hover:bg-green-600"
                                    >
                                        تحديد كمعالج
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">لا يوجد إجراء</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td colspan="7" class="bg-gray-50 px-5 py-3 text-sm leading-7 text-gray-600">
                            <strong class="text-[#0b192c]">الرسالة:</strong>
                            {{ $request->message ?: 'لا توجد رسالة.' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                            لا توجد طلبات من الموقع العام حتى الآن.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $requests->links() }}
    </div>
</div>
@endsection