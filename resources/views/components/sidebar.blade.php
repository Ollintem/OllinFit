<aside x-data="{ hovered: false }" 
       @mouseenter="hovered = true" 
       @mouseleave="hovered = false" 
       :class="hovered ? 'w-64' : 'w-20'" 
       class="bg-zinc-950 text-zinc-400 h-screen sticky top-0 flex flex-col justify-between transition-all duration-300 ease-in-out border-r border-zinc-900 shadow-2xl z-50 shrink-0 overflow-x-hidden">
    
    <!-- Logo y Título -->
    <div class="p-5 flex items-center gap-4 border-b border-zinc-900/80 mt-2">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/30">
            <!-- Ícono de Pesa/Gym -->
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 14V10" />
                <path d="M4 14H2V10H4" />
                <path d="M20 14V10" />
                <path d="M20 14H22V10H20" />
                <path d="M7 16V8" />
                <path d="M7 16H4V8H7" />
                <path d="M17 16V8" />
                <path d="M17 16H20V8H17" />
                <path d="M7 12H17" />
            </svg>
        </div>
        <div x-show="hovered" x-transition.opacity.duration.200ms class="truncate whitespace-nowrap">
            <span class="font-black text-white text-xl tracking-wide">Ollin<span class="text-orange-500">Fit</span></span>
            <span class="block text-[9px] text-zinc-500 uppercase tracking-widest font-bold">Punto de Venta</span>
        </div>
    </div>

    <!-- Menú de Navegación -->
    <nav class="px-3 py-4 flex-1 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-zinc-800 space-y-1">
        
        <!-- Dashboard -->
        @can('ver_dashboard')
        <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Dashboard</span>
        </a>
        @endcan

        <!-- Categoría: ADMINISTRACIÓN -->
        @canany(['ver_socios', 'ver_empleados', 'ver_acceso'])
        <div class="pt-5 pb-2 px-3 flex items-center gap-3 overflow-hidden">
            <span x-show="hovered" x-transition.opacity.duration.200ms class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest whitespace-nowrap">Administración</span>
            <hr class="w-full border-zinc-800/80">
        </div>
        @endcanany

        @can('ver_socios')
        <a href="{{ route('socios.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('socios.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('socios.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Socios</span>
        </a>
        @endcan
        @can('ver_empleados')
        <a href="{{ route('empleados.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('empleados.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('empleados.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Empleados</span>
        </a>
        @endcan
        @can('ver_acceso')
        <a href="{{ route('acceso.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('acceso.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('acceso.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Control de Acceso</span>
        </a>
        @endcan

        <!-- Categoría: INVENTARIO -->
        @canany(['ver_planes', 'ver_productos'])
        <div class="pt-5 pb-2 px-3 flex items-center gap-3 overflow-hidden">
            <span x-show="hovered" x-transition.opacity.duration.200ms class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest whitespace-nowrap">Inventario</span>
            <hr class="w-full border-zinc-800/80">
        </div>
        @endcanany

        @can('ver_planes')
        <a href="{{ route('planes.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('planes.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('planes.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Planes</span>
        </a>
        @endcan
        @can('ver_productos')
        <a href="{{ route('productos.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('productos.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('productos.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Inventario y Artículos</span>
        </a>
        @endcan

        <!-- Categoría: GASTOS Y PAGOS -->
        @canany(['ver_proveedores', 'ver_gastos'])
        <div class="pt-5 pb-2 px-3 flex items-center gap-3 overflow-hidden">
            <span x-show="hovered" x-transition.opacity.duration.200ms class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest whitespace-nowrap">Gastos y Pagos</span>
            <hr class="w-full border-zinc-800/80">
        </div>
        @endcanany

        <!-- Botón Proveedores -->
        @can('ver_proveedores')
        <a href="{{ route('proveedores.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('proveedores.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('proveedores.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17V9h5v8M5 13H3v-4a2 2 0 012-2h8"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Proveedores</span>
        </a>
        @endcan

        <!-- Botón Compras y Gastos -->
        @can('ver_gastos')
        <a href="{{ route('gastos.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('gastos.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('gastos.index') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Compras y Gastos</span>
        </a>
        @endcan

        <!-- Categoría: CAJA -->
        @canany(['usar_pos', 'ver_caja'])
        <div class="pt-5 pb-2 px-3 flex items-center gap-3 overflow-hidden">
            <span x-show="hovered" x-transition.opacity.duration.200ms class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest whitespace-nowrap">Caja</span>
            <hr class="w-full border-zinc-800/80">
        </div>
        @endcanany

        @can('usar_pos')
        <a href="{{ route('sales.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('sales.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('sales.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Punto de Venta</span>
        </a>
        @endcan
        @can('ver_caja')
        <a href="{{ route('caja.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('caja.index') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('caja.index') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Flujo de Caja</span>
        </a>
        <a href="{{ route('caja.historial') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('caja.historial') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('caja.historial') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Historial de Cortes</span>
        </a>
        @endcan

        <!-- Categoría: ANÁLISIS -->
        @can('ver_reportes')
        <div class="pt-5 pb-2 px-3 flex items-center gap-3 overflow-hidden">
            <span x-show="hovered" x-transition.opacity.duration.200ms class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest whitespace-nowrap">Análisis</span>
            <hr class="w-full border-zinc-800/80">
        </div>

        <a href="{{ route('reports.index') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-[13px] font-medium transition-all group {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-orange-600 to-orange-500 text-white shadow-lg shadow-orange-500/20' : 'hover:bg-zinc-900 hover:text-white' }} mb-4">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-zinc-500 group-hover:text-orange-500' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Reportes</span>
        </a>
        @endcan

    </nav>

    <!-- Perfil y Cerrar Sesión -->
    <div class="p-4 border-t border-zinc-900 bg-zinc-950 flex items-center justify-between">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-full bg-zinc-800 border border-zinc-700 text-orange-500 font-bold flex items-center justify-center shrink-0">
                {{ substr(Auth::user()->name ?? 'E', 0, 1) }}
            </div>
            <div x-show="hovered" x-transition.opacity.duration.200ms class="truncate whitespace-nowrap">
                <p class="text-[13px] font-bold text-white truncate">{{ Auth::user()->name ?? 'Eduardo' }}</p>
                <p class="text-[10px] text-zinc-500 uppercase tracking-wider font-semibold">Admin Ollinfit</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" x-show="hovered" x-transition.opacity>
            @csrf
            <button type="submit" class="text-zinc-500 hover:text-orange-500 transition-colors p-2 rounded-lg hover:bg-zinc-900" title="Cerrar Sesión">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>
</aside>