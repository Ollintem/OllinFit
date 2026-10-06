<aside x-data="{ hovered: false }" 
       @mouseenter="hovered = true" 
       @mouseleave="hovered = false" 
       :class="hovered ? 'w-64' : 'w-20'" 
       class="bg-slate-900 text-slate-300 h-screen sticky top-0 flex flex-col justify-between transition-all duration-300 ease-in-out shadow-2xl z-50 overflow-hidden shrink-0">
    
    <!-- Logo y Título -->
    <div class="p-5 flex items-center gap-3.5 border-b border-slate-800/60">
        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center font-black shrink-0 shadow-lg shadow-orange-500/30 text-lg">
            ⚖️
        </div>
        <div x-show="hovered" x-transition.opacity.duration.200ms class="truncate whitespace-nowrap">
            <span class="font-black text-white text-base tracking-wider">Ollin<span class="text-orange-500">Fit</span></span>
            <span class="block text-[9px] text-slate-400 uppercase tracking-widest font-bold">Punto de Venta</span>
        </div>
    </div>

    <!-- Menú de Navegación -->
    <nav class="px-3 py-4 space-y-1.5 flex-1 overflow-y-auto overflow-x-hidden">
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-xs font-bold transition-all group {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}" title="Dashboard">
            <span class="text-xl shrink-0 group-hover:scale-110 transition-transform">📊</span>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Punto de Venta -->
        <a href="{{ route('sales.index') }}" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-xs font-bold transition-all group {{ request()->routeIs('sales.*') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}" title="Punto de Venta">
            <span class="text-xl shrink-0 group-hover:scale-110 transition-transform">⚡</span>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Punto de Venta</span>
        </a>

        <!-- Socios -->
        <a href="{{ route('socios.index') }}" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-xs font-bold transition-all group {{ request()->routeIs('socios.*') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}" title="Socios">
            <span class="text-xl shrink-0 group-hover:scale-110 transition-transform">👥</span>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Socios</span>
        </a>

        <!-- Inventario y Planes -->
        <a href="{{ route('productos.index') }}" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-xs font-bold transition-all group {{ request()->routeIs('productos.*') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}" title="Inventario">
            <span class="text-xl shrink-0 group-hover:scale-110 transition-transform">📦</span>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Inventario y Artículos</span>
        </a>

        <!-- Reportes -->
        <a href="{{ route('reports.index') }}" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-xs font-bold transition-all group {{ request()->routeIs('reports.*') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}" title="Reportes">
            <span class="text-xl shrink-0 group-hover:scale-110 transition-transform">📈</span>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Reportes</span>
        </a>

        <!-- Flujo de Caja -->
        <a href="{{ route('caja.index') }}" class="flex items-center gap-4 px-3.5 py-3 rounded-xl text-xs font-bold transition-all group {{ request()->routeIs('caja.*') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'hover:bg-slate-800 text-slate-400 hover:text-white' }}" title="Flujo de Caja">
            <span class="text-xl shrink-0 group-hover:scale-110 transition-transform">💰</span>
            <span x-show="hovered" x-transition.opacity.duration.200ms class="whitespace-nowrap">Flujo de Caja</span>
        </a>

    </nav>

    <!-- Perfil y Cerrar Sesión en la parte inferior -->
    <div class="p-3.5 border-t border-slate-800/60 bg-slate-950/40 flex items-center justify-between">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 rounded-xl bg-orange-500 text-white font-bold flex items-center justify-center text-xs shrink-0">
                {{ substr(Auth::user()->name ?? 'E', 0, 1) }}
            </div>
            <div x-show="hovered" x-transition.opacity.duration.200ms class="truncate whitespace-nowrap">
                <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Administrador</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" x-show="hovered" x-transition.opacity>
            @csrf
            <button type="submit" class="text-slate-400 hover:text-rose-400 transition-colors p-1" title="Cerrar Sesión">
                🚪
            </button>
        </form>
    </div>
</aside>