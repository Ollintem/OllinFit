<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Ollinfit</title>
    <!-- Directiva vital para compilar Tailwind en Laravel -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white antialiased flex h-screen overflow-hidden">

    <!-- Lado Izquierdo (Imagen de fondo del gimnasio) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 text-white p-12 flex-col justify-end"
         style="background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop'); background-size: cover; background-position: center;">
        
        <!-- Capa de superposición oscura -->
        <div class="absolute inset-0 bg-slate-900/80"></div> 
        
        <!-- Texto descriptivo sobre la imagen -->
        <div class="relative z-10 mb-10">
            <h1 class="text-5xl font-bold mb-4 leading-tight">Gestión inteligente para tu<br>gimnasio</h1>
            <p class="text-lg text-gray-300 max-w-lg">Control de accesos en tiempo real, administración de socios y reportes financieros unificados en una sola plataforma.</p>
        </div>
    </div>

    <!-- Lado Derecho (Formulario de Login) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            
            <!-- Logotipo Ollinfit -->
            <div class="mb-8 flex items-center gap-2">
                <div class="bg-orange-500 p-2 rounded-lg">
                    <!-- Icono simple en SVG -->
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                </div>
                <span class="text-3xl font-extrabold text-slate-800">Ollin<span class="text-orange-500">fit</span></span>
            </div>

            <!-- Títulos -->
            <h2 class="text-3xl font-bold text-gray-900 mb-2">¡Hola de nuevo!</h2>
            <p class="text-gray-500 mb-8">Ingresa tus credenciales para acceder al panel.</p>

            <!-- Alerta de Errores de Laravel -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">
                                {{ $errors->first() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulario conectado a Breeze -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Campo Correo -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                        placeholder="contacto@ollinfit.com">
                </div>

                <!-- Campo Contraseña -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Recordarme y Contraseña Olvidada -->
                <div class="flex items-center justify-between mb-8">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-orange-500 hover:text-orange-600">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Botón de Ingreso -->
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-4 rounded-lg transition-colors shadow-md focus:outline-none focus:ring-4 focus:ring-slate-200">
                    Ingresar
                </button>
            </form>
            
        </div>
    </div>
</body>
</html>
