@props(['value' => '', 'autofocus' => false, 'showError' => true])

<div>
    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/>
            </svg>
        </span>
        <input id="email" type="email" name="email" value="{{ $value }}" required
               @if ($autofocus) autofocus @endif
               autocomplete="username" inputmode="email" placeholder="tu.correo@ollinfit.com"
               aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
               @class([
                   'block w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-slate-900 placeholder-slate-400 shadow-sm transition focus:outline-none focus:ring-4',
                   'border-red-400 focus:border-red-500 focus:ring-red-100' => $errors->has('email'),
                   'border-slate-300 focus:border-orange-500 focus:ring-orange-100' => ! $errors->has('email'),
               ])>
    </div>
    @if ($showError)
        @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    @endif
</div>
