<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Registro de Compras y Gastos
                </h1>
                <p class="text-sm text-gray-500 mt-1">Historial de salidas de efectivo e inventario.</p>
            </div>
            <a href="{{ route('gastos.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Registrar Nuevo Gasto
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Contenedor Principal -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- BUSCADOR Y FILTROS INTELIGENTES -->
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <form action="{{ route('gastos.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
                    
                    <!-- Barra de Búsqueda de Texto -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por concepto o descripción..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    </div>

                    <!-- Filtro: Tipo de Gasto -->
                    <select name="type" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl py-2 pl-3 pr-8 focus:ring-orange-500 focus:border-orange-500 cursor-pointer" onchange="this.form.submit()">
                        <option value="">Todos los Tipos</option>
                        <option value="gasto_general" {{ request('type') == 'gasto_general' ? 'selected' : '' }}>Gastos Generales</option>
                        <option value="compra_proveedor" {{ request('type') == 'compra_proveedor' ? 'selected' : '' }}>Compras de Inventario</option>
                    </select>

                    <!-- Filtro: Método de Pago -->
                    <select name="payment_method" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl py-2 pl-3 pr-8 focus:ring-orange-500 focus:border-orange-500 cursor-pointer" onchange="this.form.submit()">
                        <option value="">Todos los Métodos</option>
                        <option value="Efectivo" {{ request('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="Tarjeta" {{ request('payment_method') == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                    </select>

                    <!-- Filtro: Proveedor -->
                    <select name="supplier_id" class="bg-white border border-gray-200 text-gray-700 text-sm rounded-xl py-2 pl-3 pr-8 focus:ring-orange-500 focus:border-orange-500 cursor-pointer" onchange="this.form.submit()">
                        <option value="">Todos los Proveedores</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="hidden"></button>
                </form>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Proveedor / Concepto</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Descripción</th>
                            <th class="px-6 py-4">Método Pago</th>
                            <th class="px-6 py-4">Responsable</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($gastos as $gasto)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-gray-500 font-medium">#{{ $gasto->id }}</td>
                                
                                <td class="px-6 py-4">
                                    @if($gasto->type === 'compra_proveedor')
                                        <span class="block font-bold text-blue-600">{{ $gasto->supplier->name ?? 'Compra General (Sin Proveedor)' }}</span>
                                        <span class="block text-[11px] text-gray-500 italic mt-0.5">Compra de Inventario</span>
                                    @else
                                        <span class="block font-bold text-orange-600">{{ $gasto->concept }}</span>
                                        <span class="block text-[11px] text-gray-500 italic mt-0.5">Gasto General</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-gray-600 font-medium">
                                    {{ $gasto->created_at->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 text-gray-500 max-w-xs truncate" title="{{ $gasto->description }}">
                                    {{ Str::limit($gasto->description, 40) }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($gasto->payment_method === 'Efectivo')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                                            💵 Efectivo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-bold border border-gray-300">
                                            💳 {{ $gasto->payment_method }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-zinc-800 text-white flex items-center justify-center text-[10px] font-bold">
                                            {{ substr($gasto->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="text-gray-700 font-medium text-xs">{{ $gasto->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right font-extrabold text-red-600">
                                    ${{ number_format($gasto->amount, 2) }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Botón Editar -->
                                        <a href="{{ route('gastos.edit', $gasto->id) }}" class="p-1.5 text-orange-500 bg-orange-50 hover:bg-orange-100 rounded transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <!-- Botón Eliminar (Dispara el Modal) -->
                                        <button type="button" onclick="confirmarEliminacionGasto({{ $gasto->id }}, '{{ $gasto->type === 'compra_proveedor' ? 'Compra a proveedor' : $gasto->concept }}')" class="p-1.5 text-red-500 bg-red-50 hover:bg-red-100 rounded transition-colors" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <p class="text-base font-bold text-gray-500">No se encontraron resultados</p>
                                    <p class="text-sm mt-1">Ajusta los filtros o registra un nuevo gasto.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $gastos->links() }}
            </div>
        </div>
    </div>

    <!-- MODAL SEGURIDAD ELIMINAR GASTO -->
    <div id="modal-delete-gasto" class="fixed inset-0 z-50 hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modal-delete-gasto-content">
            <div class="p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6 border-8 border-red-50/50">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Eliminar Gasto</h3>
                <p class="text-gray-500 text-sm mb-6">Vas a borrar: <strong id="delete-gasto-name" class="text-gray-900"></strong>. <br><span class="text-red-500 font-bold">¡Si fue compra de inventario, los productos se restarán de tu stock actual!</span></p>

                <form id="form-delete-gasto-secure" action="" method="POST" class="flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="cerrarModalEliminarGasto()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-bold transition-colors text-sm w-full">Cancelar</button>
                    <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 font-bold shadow-sm transition-colors text-sm w-full flex items-center justify-center gap-2">
                        Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT DEL MODAL -->
    <script>
        function confirmarEliminacionGasto(id, concepto) {
            const modal = document.getElementById('modal-delete-gasto');
            const modalContent = document.getElementById('modal-delete-gasto-content');
            document.getElementById('delete-gasto-name').textContent = concepto;
            let urlBase = '{{ route("gastos.destroy", ":id") }}';
            document.getElementById('form-delete-gasto-secure').action = urlBase.replace(':id', id);
            
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
        }
        function cerrarModalEliminarGasto() {
            const modal = document.getElementById('modal-delete-gasto');
            const modalContent = document.getElementById('modal-delete-gasto-content');
            modal.classList.add('opacity-0'); modalContent.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }
    </script>
</x-app-layout>