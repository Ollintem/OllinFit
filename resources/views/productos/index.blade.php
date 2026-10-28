<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{ 
        searchQuery: '', 
        showDeleteModal: false, 
        deleteAction: '',
        
        filterRow(name) {
            if (this.searchQuery === '') return true;
            return name.toLowerCase().includes(this.searchQuery.toLowerCase());
        },

        hasAccesosResults() {
            let names = [
                @foreach ($productos->where('is_service', 1) as $acc)
                    '{{ addslashes($acc->name) }}',
                @endforeach
            ];
            if (this.searchQuery === '') return names.length > 0;
            return names.some(name => name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },

        hasTiendaResults() {
            let names = [
                @foreach ($productos->where('is_service', 0) as $prod)
                    '{{ addslashes($prod->name) }}',
                @endforeach
            ];
            if (this.searchQuery === '') return names.length > 0;
            return names.some(name => name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        }
    }">

        <!-- Encabezado Principal -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Inventario y Artículos</h1>
                <p class="text-sm text-gray-500 mt-0.5">Control y gestión de accesos, pases y stock de productos de la tienda.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url('/punto-de-venta') }}"
                    class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center gap-2 shadow-sm cursor-pointer">
                    🛒 Ir al Punto de Venta
                </a>
                <a href="{{ route('productos.create') }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center gap-2 shadow-sm cursor-pointer">
                    + Nuevo Producto
                </a>
            </div>
        </div>

        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.duration.500ms
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-sm font-medium shadow-xs mb-6 flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <!-- TARJETAS DE RESUMEN / ESTADÍSTICAS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center font-bold text-lg">
                    🎫
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Accesos y Pases</span>
                    <h3 class="text-xl font-black text-gray-800">{{ $productos->where('is_service', 1)->count() }}</h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    📦
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Productos en Tienda</span>
                    <h3 class="text-xl font-black text-gray-800">{{ $productos->where('is_service', 0)->count() }}</h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg">
                    ⚠️
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Alertas de Stock</span>
                    <h3 class="text-xl font-black text-gray-800">{{ $productos->where('is_service', 0)->where('stock', '<=', 5)->count() }}</h3>
                </div>
            </div>
        </div>

        <!-- BARRA DE BÚSQUEDA PROFESIONAL -->
        <div class="bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" 
                x-model="searchQuery" 
                placeholder="Buscar artículo, pase de acceso o producto en inventario..." 
                class="w-full text-xs font-semibold text-gray-800 bg-transparent border-none focus:outline-none focus:ring-0 placeholder-gray-400">
            <button x-show="searchQuery.length > 0" @click="searchQuery = ''" class="text-xs font-bold text-gray-400 hover:text-gray-600 px-2 py-1 bg-gray-100 rounded-lg cursor-pointer transition-colors">Limpiar</button>
        </div>

        <!-- SECCIÓN 1: ACCESOS Y PASES -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4" x-show="hasAccesosResults()">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Accesos y Pases (Servicios sin Stock)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3 px-4">Concepto / Nombre</th>
                            <th class="py-3 px-4">Precio</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if ($productos->where('is_service', 1)->count() > 0)
                            @foreach ($productos->where('is_service', 1) as $acc)
                                <tr class="hover:bg-gray-50/60 transition-colors" x-show="filterRow(@js($acc->name))">
                                    <td class="py-3.5 px-4 font-bold text-gray-800">{{ $acc->name }}</td>
                                    <td class="py-3.5 px-4 font-extrabold text-orange-600">
                                        ${{ number_format($acc->price, 2) }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-100">Acceso / Pase</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('productos.edit', $acc->id) }}"
                                                class="w-8 h-8 flex items-center justify-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl transition-colors shadow-2xs"
                                                title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <button type="button" 
                                                @click="showDeleteModal = true; deleteAction = '{{ route('productos.destroy', $acc->id) }}'"
                                                class="w-8 h-8 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors cursor-pointer shadow-2xs"
                                                title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400">No hay accesos registrados.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN 2: TIENDA Y PRODUCTOS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4" x-show="hasTiendaResults()">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Tienda y Productos (Con Control de Stock)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3 px-4">Imagen</th>
                            <th class="py-3 px-4">Producto</th>
                            <th class="py-3 px-4">Precio</th>
                            <th class="py-3 px-4">Stock Actual</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if ($productos->where('is_service', 0)->count() > 0)
                            @foreach ($productos->where('is_service', 0) as $prod)
                                <tr class="hover:bg-gray-50/60 transition-colors" x-show="filterRow(@js($prod->name))">
                                    <td class="py-3.5 px-4">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center border border-gray-200">
                                            @if ($prod->image_path)
                                                <img src="{{ asset('storage/' . $prod->image_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xs text-gray-400">📦</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-gray-800">{{ $prod->name }}</td>
                                    <td class="py-3.5 px-4 font-extrabold text-emerald-600">
                                        ${{ number_format($prod->price, 2) }}</td>
                                    <td class="py-3.5 px-4">
                                        @if ($prod->stock <= 0)
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">🔴 Agotado: {{ $prod->stock }}</span>
                                        @elseif ($prod->stock <= ($prod->min_stock ?? 5))
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">⚠️ Stock Bajo: {{ $prod->stock }}</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Óptimo: {{ $prod->stock }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('productos.edit', $prod->id) }}"
                                                class="w-8 h-8 flex items-center justify-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl transition-colors shadow-2xs"
                                                title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <button type="button" 
                                                @click="showDeleteModal = true; deleteAction = '{{ route('productos.destroy', $prod->id) }}'"
                                                class="w-8 h-8 flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors cursor-pointer shadow-2xs"
                                                title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400">No hay productos en tienda registrados.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MENSAJE SI NO HAY NINGÚN RESULTADO -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center space-y-2" x-show="!hasAccesosResults() && !hasTiendaResults()">
            <span class="text-3xl">🔍</span>
            <h3 class="font-bold text-gray-800 text-sm">Sin coincidencias encontradas</h3>
            <p class="text-xs text-gray-400">No se encontraron artículos o productos que coincidan con "<span x-text="searchQuery" class="font-bold"></span>".</p>
            <button @click="searchQuery = ''" class="mt-2 inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-2 px-4 rounded-xl transition-colors cursor-pointer">
                Ver todo el inventario
            </button>
        </div>

        <!-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN -->
        <div x-show="showDeleteModal" 
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4"
            style="display: none;"
            x-transition.opacity>
            <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-6 space-y-4 border border-gray-100 text-center">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 mx-auto flex items-center justify-center text-xl font-bold">
                    ⚠️
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-base">¿Estás seguro?</h3>
                    <p class="text-xs text-gray-500 mt-1">Esta acción eliminará el registro permanentemente del inventario.</p>
                </div>
                <div class="flex gap-2 pt-2">
                    <button @click="showDeleteModal = false" 
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs py-2.5 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <form :action="deleteAction" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs py-2.5 rounded-xl transition-colors cursor-pointer shadow-md">
                            Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>