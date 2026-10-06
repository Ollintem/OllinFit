<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">

        <!-- Encabezado -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Inventario y Artículos</h1>
                <p class="text-sm text-gray-500 mt-0.5">Gestión de pases de acceso y stock de productos para la tienda.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url('/punto-de-venta') }}"
                    class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                    Ir al Punto de Venta
                </a>
                <a href="{{ route('productos.create') }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                    + Nuevo Producto
                </a>
            </div>
        </div>
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.duration.500ms
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-sm font-medium shadow-xs mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- SECCIÓN 1: ACCESOS Y PASES (is_service == 1) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Accesos y Pases (Servicios sin
                    Stock)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3 px-4">Concepto / Nombre</th>
                            <th class="py-3 px-4">Precio</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if ($productos->where('is_service', 1)->count() > 0)
                            @foreach ($productos->where('is_service', 1) as $acc)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3.5 px-4 font-bold text-gray-800">{{ $acc->name }}</td>
                                    <td class="py-3.5 px-4 font-extrabold text-orange-600">
                                        ${{ number_format($acc->price, 2) }}</td>
                                    <td class="py-3.5 px-4"><span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-600">Acceso
                                            / Pase</span></td>
                                    <td class="py-3.5 px-4 text-right space-x-2">
                                        <!-- Botón de Editar -->
                                        <a href="{{ route('productos.edit', $acc->id) }}"
                                            class="text-indigo-500 hover:text-indigo-700 font-bold text-xs"
                                            title="Editar">
                                            ✏️
                                        </a>

                                        <!-- Botón de Eliminar -->
                                        <form action="{{ route('productos.destroy', $acc->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Eliminar este acceso?')"
                                                class="text-gray-400 hover:text-rose-600 font-bold cursor-pointer"
                                                title="Eliminar">🗑️</button>
                                        </form>
                                    </td>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400">No hay accesos registrados.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN 2: TIENDA Y PRODUCTOS (is_service == 0) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Tienda y Productos (Con Control de
                    Stock)</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
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
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3.5 px-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center border border-gray-200">
                                            @if ($prod->image_path)
                                                <img src="{{ asset('storage/' . $prod->image_path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xs text-gray-400">📦</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-gray-800">{{ $prod->name }}</td>
                                    <td class="py-3.5 px-4 font-extrabold text-emerald-600">
                                        ${{ number_format($prod->price, 2) }}</td>
                                    <td class="py-3.5 px-4">
                                        @if ($prod->stock <= ($prod->min_stock ?? 5))
                                            <span
                                                class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">⚠️
                                                Stock Bajo: {{ $prod->stock }}</span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600">Óptimo:
                                                {{ $prod->stock }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-2">
                                        <!-- Botón de Editar -->
                                        <a href="{{ route('productos.edit', $prod->id) }}"
                                            class="text-indigo-500 hover:text-indigo-700 font-bold text-xs"
                                            title="Editar">
                                            ✏️
                                        </a>

                                        <!-- Botón de Eliminar -->
                                        <form action="{{ route('productos.destroy', $prod->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Eliminar este producto?')"
                                                class="text-gray-400 hover:text-rose-600 font-bold cursor-pointer"
                                                title="Eliminar">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400">No hay productos en tienda
                                    registrados.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
