@props([
    'name' => 'password',
    'id' => null,
    'label' => 'Contraseña',
    'autocomplete' => 'current-password',
    'model' => null, // Variable de Alpine opcional para leer el valor (p. ej. medidor de fuerza)
])

@php($id ??= $name)

<div x-data="{ show: false, caps: false }">
    <label for="{{ $id }}" class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }}</label>
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="4" y="11" width="16" height="10" rx="2"/><path stroke-linecap="round" d="M8 11V7a4 4 0 0 1 8 0v4"/>
            </svg>
        </span>
        <input id="{{ $id }}" name="{{ $name }}" :type="show ? 'text' : 'password'" type="password" required
               autocomplete="{{ $autocomplete }}" placeholder="••••••••"
               @if ($model) x-model="{{ $model }}" @endif
               @keydown="caps = $event.getModifierState && $event.getModifierState('CapsLock')"
               @keyup="caps = $event.getModifierState && $event.getModifierState('CapsLock')"
               @blur="caps = false"
               aria-invalid="{{ $errors->has($name) ? 'true' : 'false' }}"
               {{ $attributes->class([
                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-12 text-slate-900 placeholder-slate-400 shadow-sm transition focus:outline-none focus:ring-4',
                   'border-red-400 focus:border-red-500 focus:ring-red-100' => $errors->has($name),
                   'border-slate-300 focus:border-orange-500 focus:ring-orange-100' => ! $errors->has($name),
               ]) }}>
        <button type="button" @click="show = !show"
                class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3.5 text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:text-orange-600"
                :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'" :aria-pressed="show.toString()">
            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4.2M6.6 6.6C3.9 8.4 2 12 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.6"/>
            </svg>
        </button>
    </div>
    <p x-show="caps" x-cloak class="mt-2 flex items-center gap-1.5 text-xs font-medium text-amber-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4l7 8h-4v4H9v-4H5l7-8ZM9 20h6"/></svg>
        Bloq Mayús está activado
    </p>
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
