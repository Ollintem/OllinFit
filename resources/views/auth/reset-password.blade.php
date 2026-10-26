<x-auth-layout title="Nueva contraseña" :step="3">
    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6l8-3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>
        </svg>
    </div>

    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Crea tu nueva contraseña</h2>
    <p class="mt-2 mb-8 text-slate-500">Código verificado. Elige una contraseña segura que no hayas usado antes.</p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5"
          x-data="{
              sending: false, password: '', confirmation: '',
              get checks() {
                  return [
                      { label: 'Mínimo 8 caracteres', ok: this.password.length >= 8 },
                      { label: 'Una mayúscula', ok: /[A-Z]/.test(this.password) },
                      { label: 'Un número', ok: /\d/.test(this.password) },
                      { label: 'Un símbolo', ok: /[^A-Za-z0-9]/.test(this.password) },
                  ]
              },
              get score() { return this.checks.filter(c => c.ok).length },
              get matches() { return this.confirmation.length > 0 && this.password === this.confirmation },
          }"
          @submit="sending = true">
        @csrf

        <div>
            <x-auth-password label="Nueva contraseña" autocomplete="new-password" model="password" autofocus />

            {{-- Medidor de fuerza --}}
            <div class="mt-3" x-show="password.length" x-cloak>
                <div class="flex gap-1.5">
                    <template x-for="i in 4" :key="i">
                        <span class="h-1.5 flex-1 rounded-full transition-colors"
                              :class="i <= score ? ['bg-red-500','bg-amber-500','bg-lime-500','bg-emerald-500'][score - 1] : 'bg-slate-200'"></span>
                    </template>
                </div>
                <ul class="mt-3 grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                    <template x-for="check in checks" :key="check.label">
                        <li class="flex items-center gap-1.5" :class="check.ok ? 'text-emerald-700' : 'text-slate-500'">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true">
                                <path x-show="check.ok" stroke-linecap="round" stroke-linejoin="round" d="m5 12 5 5L20 7"/>
                                <circle x-show="!check.ok" cx="12" cy="12" r="4"/>
                            </svg>
                            <span x-text="check.label"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <div>
            <x-auth-password name="password_confirmation" label="Confirmar contraseña" autocomplete="new-password" model="confirmation" />
            <p x-show="confirmation.length" x-cloak class="mt-2 text-xs font-medium"
               :class="matches ? 'text-emerald-700' : 'text-red-600'"
               x-text="matches ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden'"></p>
        </div>

        <x-auth-submit loading="Guardando..." disabled="sending || password.length < 8 || !matches">
            Guardar contraseña
        </x-auth-submit>
    </form>

    <a href="{{ route('login') }}" class="mt-8 flex items-center justify-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6 6-6-6 6-6"/></svg>
        Cancelar y volver
    </a>
</x-auth-layout>
