@extends('layouts.dashboard')

@section('title', 'سجل العمليات - CADY EST')
@section('page_title', '🕵️ سجل العمليات (Audit Log)')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-[#0b192c]">سجل العمليات</h2>
        <p class="text-sm text-gray-500 mt-1">مراقبة وتتبع جميع الإجراءات والأنشطة على النظام</p>
    </div>
    <span class="bg-[#0b192c] text-white px-4 py-2 rounded-xl text-sm font-bold">
        المجموع: {{ $logs->total() }} سجل
    </span>
</div>

{{-- Search & Filter Form --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('dashboard.audit_logs.index') }}" class="flex flex-wrap gap-4 items-end">
        {{-- Text Search --}}
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-600 mb-1">بحث في العمليات والوصف</label>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="ابحث عن إجراء أو وصف..."
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm"
            >
        </div>

        {{-- User Filter --}}
        <div class="min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-600 mb-1">تصفية بالمستخدم</label>
            <select
                name="user_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm bg-white"
            >
                <option value="">— كل المستخدمين —</option>
                @foreach($staffUsers as $staffUser)
                    <option value="{{ $staffUser->id }}" {{ request('user_id') === $staffUser->id ? 'selected' : '' }}>
                        {{ $staffUser->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2">
            <button type="submit" class="bg-[#0b192c] hover:bg-navy-900 text-white px-5 py-2 rounded-xl text-sm font-bold transition">
                🔍 بحث
            </button>
            @if(request('search') || request('user_id'))
                <a href="{{ route('dashboard.audit_logs.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-xl text-sm font-bold transition">
                    ✕ إلغاء
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Audit Logs Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    @if($logs->isEmpty())
        <div class="p-10 text-center">
            <x-empty-state message="لا توجد سجلات عمليات بعد." />
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right text-gray-700">
                <thead class="bg-[#0b192c] text-white text-xs">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold">التاريخ والوقت</th>
                        <th class="px-4 py-3 text-right font-semibold">المستخدم</th>
                        <th class="px-4 py-3 text-right font-semibold">العملية</th>
                        <th class="px-4 py-3 text-right font-semibold">الوصف</th>
                        <th class="px-4 py-3 text-right font-semibold">نوع الكيان</th>
                        <th class="px-4 py-3 text-right font-semibold">معرّف الكيان</th>
                        <th class="px-4 py-3 text-right font-semibold">عنوان IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Date & Time --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-gray-800 font-medium">
                                    {{ $log->created_at->format('Y/m/d') }}
                                </div>
                                <div class="text-gray-400 text-xs">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                            </td>

                            {{-- User --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-semibold text-gray-800">{{ $log->user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-gray-400 italic text-xs">النظام / مجهول</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-4 py-3">
                                @php
                                    $actionColor = match(true) {
                                        str_contains($log->action, 'created') => 'success',
                                        str_contains($log->action, 'deleted') => 'danger',
                                        str_contains($log->action, 'updated') => 'info',
                                        str_contains($log->action, 'approved') => 'success',
                                        str_contains($log->action, 'rejected') || str_contains($log->action, 'terminated') => 'danger',
                                        str_contains($log->action, 'login') => 'navy',
                                        default => 'warning',
                                    };
                                @endphp
                                <x-badge :type="$actionColor">
                                    {{ $log->action }}
                                </x-badge>
                            </td>

                            {{-- Description --}}
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-gray-700 text-xs leading-relaxed truncate" title="{{ $log->description ?? '—' }}">
                                    {{ $log->description ?? '—' }}
                                </p>
                            </td>

                            {{-- Entity Type --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($log->entity_type)
                                    @php
                                        $entityShortName = class_basename($log->entity_type);
                                    @endphp
                                    <x-badge type="info">{{ $entityShortName }}</x-badge>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Entity ID --}}
                            <td class="px-4 py-3">
                                @if($log->entity_id)
                                    <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                        {{ Str::limit($log->entity_id, 8, '…') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- IP Address --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-xs text-gray-600">
                                    {{ $log->ip_address ?? '—' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
