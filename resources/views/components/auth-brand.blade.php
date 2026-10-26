@props(['dark' => false])

<div class="flex items-center gap-2.5">
    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 shadow-lg shadow-orange-500/30">
        {{-- Mancuerna --}}
        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.5 6.5v11M17.5 6.5v11M3.5 9v6M20.5 9v6M6.5 12h11"/>
        </svg>
    </div>
    <span class="text-2xl font-extrabold tracking-tight {{ $dark ? 'text-white' : 'text-slate-900' }}">Ollin<span class="text-orange-500">fit</span></span>
</div>
