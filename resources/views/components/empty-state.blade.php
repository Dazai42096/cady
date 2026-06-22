@props(['title' => 'لا توجد بيانات', 'message' => 'لم يتم العثور على أي سجلات في هذا القسم حالياً.', 'icon' => '📁'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm']) }}>
    <div class="text-5xl mb-4 select-none">{{ $icon }}</div>
    <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
    <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">{{ $message }}</p>
    @if(isset($slot) && $slot->isNotEmpty())
        <div class="mt-6">
            {{ $slot }}
        </div>
    @endif
</div>
