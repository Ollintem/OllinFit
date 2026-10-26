@props([
    'loading' => 'Procesando...',
    'disabled' => 'sending', // Expresión de Alpine; el formulario padre debe definir "sending" en su x-data
])

<button type="submit" :disabled="{{ $disabled }}"
        {{ $attributes->merge(['class' => 'flex w-full items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3.5 font-bold text-white shadow-lg shadow-orange-500/25 transition hover:bg-orange-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-200 disabled:cursor-not-allowed disabled:opacity-60']) }}>
    <svg x-show="sending" x-cloak class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"/>
    </svg>
    <span x-show="!sending" class="inline-flex items-center gap-2">{{ $slot }}</span>
    <span x-show="sending" x-cloak>{{ $loading }}</span>
</button>
