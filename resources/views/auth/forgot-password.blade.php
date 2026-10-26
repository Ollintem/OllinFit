<x-auth-layout title="Recuperar contraseña" :step="1">
    <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="8" cy="15" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="m10.85 12.15 8.65-8.65M18 5l2 2M15 8l2 2"/>
        </svg>
    </div>

    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">¿Olvidaste tu contraseña?</h2>
    <p class="mt-2 mb-8 text-slate-500">
        No te preocupes. Escribe el correo de tu cuenta y te enviaremos un <strong class="text-slate-700">código de 6 dígitos</strong> para crear una nueva.
    </p>

    <form method="POST" action="{{ route('password.email') }}" x-data="{ sending: false }" @submit="sending = true" class="space-y-6">
        @csrf

        <x-auth-email :value="old('email', session('password_reset.email'))" :autofocus="true" />

        <x-auth-submit loading="Enviando código...">
            Enviar código
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m22 2-7 20-4-9-9-4 20-7Z"/></svg>
        </x-auth-submit>
    </form>

    <a href="{{ route('login') }}" class="mt-8 flex items-center justify-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6 6-6-6 6-6"/></svg>
        Volver a iniciar sesión
    </a>
</x-auth-layout>
