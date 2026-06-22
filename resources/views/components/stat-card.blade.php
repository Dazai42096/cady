@props(['title', 'value', 'icon' => '📈', 'color' => 'navy'])

@php
$colorClasses = match($color) {
    'green' => 'bg-green-50 text-[#00d26a] border-green-100',
    'navy' => 'bg-slate-50 text-[#0b192c] border-slate-200',
    'warning' => 'bg-amber-50 text-amber-600 border-amber-100',
    'danger' => 'bg-red-50 text-red-600 border-red-100',
    default => 'bg-gray-50 text-gray-600 border-gray-150',
};
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex items-center justify-between']) }}>
    <div>
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $title }}</p>
        <h3 class="text-2xl font-bold text-slate-900 mt-2">{{ $value }}</h3>
    </div>
    <div class="text-2xl p-3 rounded-xl border {{ $colorClasses }}">
        {{ $icon }}
    </div>
</div>
