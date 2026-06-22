@props([
    'type' => null,
    'color' => null,
    'status' => null,
])

@php
    $value = $color ?? $type ?? $status;
    $label = null;

    if ($value instanceof \BackedEnum) {
        $label = method_exists($value, 'label') ? $value->label() : $value->value;
        $value = $value->value;
    } elseif ($value instanceof \UnitEnum) {
        $label = method_exists($value, 'label') ? $value->label() : $value->name;
        $value = $value->name;
    }

    $value = is_string($value) ? $value : 'gray';

    $classes = match ($value) {
        'active', 'approved', 'accepted', 'completed', 'success', 'green', 'available' => 'bg-green-50 text-green-700 border-green-200',

        'pending', 'pending_admin_link', 'warning', 'yellow', 'scheduled', 'draft' => 'bg-amber-50 text-amber-700 border-amber-200',

        'inactive', 'rejected', 'cancelled', 'danger', 'error', 'red', 'expired', 'terminated' => 'bg-red-50 text-red-700 border-red-200',

        'suspended', 'gray' => 'bg-gray-50 text-gray-700 border-gray-200',

        'sent', 'confirmed', 'blue', 'info' => 'bg-blue-50 text-blue-700 border-blue-200',

        'rented', 'orange' => 'bg-orange-50 text-orange-700 border-orange-200',

        'maintenance', 'in_progress', 'purple' => 'bg-purple-50 text-purple-700 border-purple-200',

        'navy' => 'bg-[#0b192c] text-white border-gray-700',

        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border {{ $classes }}">
    @if(trim((string) $slot) !== '')
        {{ $slot }}
    @else
        {{ $label ?? $value }}
    @endif
</span>