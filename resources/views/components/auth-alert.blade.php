@props(['type' => 'error'])

@php
    $styles = [
        'error' => ['bg-red-50 text-red-800 ring-red-200', 'text-red-500', 'M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z'],
        'success' => ['bg-emerald-50 text-emerald-800 ring-emerald-200', 'text-emerald-500', 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'info' => ['bg-sky-50 text-sky-800 ring-sky-200', 'text-sky-500', 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ][$type];
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}" aria-live="polite"
     {{ $attributes->merge(['class' => "mb-6 flex items-start gap-3 rounded-xl p-4 text-sm ring-1 {$styles[0]}"]) }}>
    <svg class="mt-0.5 h-5 w-5 shrink-0 {{ $styles[1] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $styles[2] }}"/>
    </svg>
    <div class="font-medium">{{ $slot }}</div>
</div>
