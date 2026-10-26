@props([
    'title' => 'Ollinfit',
    'step' => null,      // Paso actual del flujo de recuperación (1, 2 o 3)
    'steps' => 3,
])

<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Ollinfit</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full bg-slate-50 font-sans text-slate-900 antialiased">
<div class="flex min-h-full">

    {{-- Panel de marca (solo escritorio) --}}
    <aside class="relative hidden lg:flex lg:w-1/2 xl:w-[55%] flex-col justify-between overflow-hidden bg-slate-900 p-12 text-white"
           style="background-image:url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop');background-size:cover;background-position:center;">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-900/85 to-orange-900/60"></div>

        <div class="relative z-10">
            <x-auth-brand dark />
        </div>

        <div class="relative z-10 max-w-xl">
            <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight tracking-tight">
                Gestión inteligente<br>para tu <span class="text-orange-400">gimnasio</span>
            </h1>
            <p class="mt-4 text-lg text-slate-300">
                Accesos en tiempo real, socios, punto de venta y finanzas en una sola plataforma.
            </p>

            <ul class="mt-10 grid grid-cols-1 xl:grid-cols-3 gap-4 text-sm">
                @foreach ([
                    ['Control de acceso QR', 'M3 4h6v6H3zM15 4h6v6h-6zM3 14h6v6H3zM15 14h2v2h-2zM19 18h2v2h-2zM15 18h2'],
                    ['Caja y punto de venta', 'M3 7h18v12H3zM3 11h18M7 15h2'],
                    ['Reportes financieros', 'M4 20V10M10 20V4M16 20v-7M22 20H2'],
                ] as [$label, $path])
                    <li class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 ring-1 ring-white/10 backdrop-blur-sm">
                        <svg class="h-5 w-5 shrink-0 text-orange-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                        </svg>
                        <span class="font-medium text-slate-100">{{ $label }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="relative z-10 text-xs text-slate-400">&copy; {{ date('Y') }} Ollinfit · Todos los derechos reservados</p>
    </aside>

    {{-- Panel del formulario --}}
    <main class="flex w-full lg:w-1/2 xl:w-[45%] flex-col justify-center px-5 py-10 sm:px-10">
        <div class="mx-auto w-full max-w-md">

            <div class="mb-10 lg:hidden">
                <x-auth-brand />
            </div>

            @if ($step)
                <div class="mb-8" aria-label="Paso {{ $step }} de {{ $steps }}">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-orange-600">Paso {{ $step }} de {{ $steps }}</p>
                    <div class="flex gap-2">
                        @for ($i = 1; $i <= $steps; $i++)
                            <span class="h-1.5 flex-1 rounded-full {{ $i <= $step ? 'bg-orange-500' : 'bg-slate-200' }}"></span>
                        @endfor
                    </div>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>
</body>
</html>
