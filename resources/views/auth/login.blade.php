<x-auth-layout title="Iniciar sesión">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">¡Hola de nuevo! 👋</h2>
    <p class="mt-2 mb-8 text-slate-500">Ingresa tus credenciales para acceder al panel de Ollinfit.</p>

    @if (session('status'))
        <x-auth-alert type="success">{{ session('status') }}</x-auth-alert>
    @endif

    {{-- Las credenciales incorrectas llegan como error del campo email; se muestran arriba como alerta general --}}
    @if ($errors->has('email'))
        <x-auth-alert>{{ $errors->first('email') }}</x-auth-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" x-data="{ sending: false }" @submit="sending = true" class="space-y-5">
        @csrf

        <x-auth-email :value="old('email')" :autofocus="true" :show-error="false" />

        <x-auth-password />

        <div class="flex items-center justify-between">
            <label class="flex cursor-pointer select-none items-center gap-2">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500">
                <span class="text-sm text-slate-600">Mantener sesión iniciada</span>
            </label>
            <a href="{{ route('password.request') }}" class="rounded text-sm font-semibold text-orange-600 hover:text-orange-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-300">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

        <x-auth-submit loading="Ingresando...">
            Ingresar
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
        </x-auth-submit>
    </form>

    <p class="mt-10 text-center text-xs text-slate-400">
        ¿Problemas para entrar? Contacta al administrador del gimnasio.
    </p>
</x-auth-layout>
