@php
    // Enmascara el correo: ad***@ollinfit.com
    [$user, $domain] = array_pad(explode('@', $email, 2), 2, '');
    $maskedEmail = mb_substr($user, 0, 2).str_repeat('•', max(mb_strlen($user) - 2, 3)).'@'.$domain;
@endphp

<x-auth-layout title="Verificar código" :step="2">
    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/>
        </svg>
    </div>

    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Revisa tu correo</h2>
    <p class="mt-2 mb-8 text-slate-500">
        Escribe el código de 6 dígitos que enviamos a <strong class="text-slate-700">{{ $maskedEmail }}</strong>.
        Vence en {{ $expiresMinutes }} minutos.
    </p>

    @if (session('status'))
        <x-auth-alert type="success">{{ session('status') }}</x-auth-alert>
    @endif

    @error('code')
        <x-auth-alert>{{ $message }}</x-auth-alert>
    @enderror

    <form method="POST" action="{{ route('password.code.verify') }}" class="space-y-6"
          x-data="otpForm()" x-ref="form" @submit="sending = true">
        @csrf
        <input type="hidden" name="code" :value="code">

        <fieldset>
            <legend class="sr-only">Código de verificación</legend>
            <div class="flex justify-between gap-2 sm:gap-3" @paste.prevent="paste($event)">
                <template x-for="(digit, i) in digits" :key="i">
                    <input type="text" inputmode="numeric" autocomplete="one-time-code"
                           :aria-label="'Dígito ' + (i + 1)"
                           x-model="digits[i]"
                           @input="onInput(i, $event)"
                           @keydown.backspace="onBackspace(i, $event)"
                           @keydown.arrow-left.prevent="focus(i - 1)"
                           @keydown.arrow-right.prevent="focus(i + 1)"
                           @focus="$event.target.select()"
                           class="h-14 w-12 sm:h-16 sm:w-14 rounded-xl border bg-white text-center text-2xl font-bold text-slate-900 shadow-sm transition focus:border-orange-500 focus:outline-none focus:ring-4 focus:ring-orange-100 {{ $errors->has('code') ? 'border-red-400' : 'border-slate-300' }}">
                </template>
            </div>
        </fieldset>

        <x-auth-submit loading="Verificando..." disabled="sending || code.length !== 6">
            Verificar código
        </x-auth-submit>
    </form>

    {{-- Reenviar código con cuenta regresiva --}}
    <div class="mt-8 text-center text-sm text-slate-500"
         x-data="{ left: {{ $resendIn }}, init() { const t = setInterval(() => { if (this.left > 0) this.left--; else clearInterval(t) }, 1000) } }">
        ¿No te llegó? Revisa tu carpeta de spam o
        <form method="POST" action="{{ route('password.code.resend') }}" class="inline">
            @csrf
            <button type="submit" :disabled="left > 0"
                    class="font-semibold text-orange-600 hover:text-orange-700 disabled:cursor-not-allowed disabled:text-slate-400">
                <span x-show="left === 0">reenvía el código</span>
                <span x-show="left > 0" x-cloak>reenvía en <span x-text="left"></span> s</span>
            </button>
        </form>
    </div>

    <div class="mt-6 flex flex-col items-center gap-3 text-sm">
        <a href="{{ route('password.request') }}" class="font-semibold text-slate-600 hover:text-slate-900">Usar otro correo</a>
        <a href="{{ route('login') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-900">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6 6-6-6 6-6"/></svg>
            Volver a iniciar sesión
        </a>
    </div>

    <script>
        function otpForm() {
            return {
                digits: ['', '', '', '', '', ''],
                sending: false,
                get code() { return this.digits.join('') },
                init() { this.$nextTick(() => this.focus(0)) },
                inputs() { return this.$refs.form.querySelectorAll('input[inputmode=numeric]') },
                focus(i) { const el = this.inputs()[i]; if (el) el.focus() },
                onInput(i, e) {
                    const value = e.target.value.replace(/\D/g, '')
                    if (value.length > 1) return this.fill(value, i)
                    this.digits[i] = value
                    if (value) this.next(i)
                },
                onBackspace(i, e) {
                    if (!this.digits[i] && i > 0) { e.preventDefault(); this.digits[i - 1] = ''; this.focus(i - 1) }
                },
                paste(e) { this.fill((e.clipboardData.getData('text') || '').replace(/\D/g, ''), 0) },
                fill(value, start) {
                    value.slice(0, 6 - start).split('').forEach((d, k) => this.digits[start + k] = d)
                    this.next(Math.min(start + value.length, 6) - 1)
                },
                next(i) {
                    if (i < 5) return this.focus(i + 1)
                    // Último dígito: se envía automáticamente
                    this.$nextTick(() => { if (this.code.length === 6) { this.sending = true; this.$refs.form.submit() } })
                },
            }
        }
    </script>
</x-auth-layout>
