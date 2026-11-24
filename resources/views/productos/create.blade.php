<x-app-layout>
    <div class="max-w-3xl mx-auto p-6" x-data="{ tipo: '0' }">

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            <!-- Encabezado Oscuro -->
            <div class="bg-zinc-900 px-8 py-6 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Nuevo Producto / Artículo
                </h2>
                <a href="{{ route('productos.index') }}" class="text-xs font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg border border-zinc-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver
                </a>
            </div>

            <div class="p-8">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Detalles del Artículo</h3>

                <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <x-form-errors />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre del Artículo / Acceso *</label>
                            <input type="text" name="name" required placeholder="Ej. Visita 1 Día, Agua Embotellada, etc." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <!-- Precio -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Precio de Venta ($) *</label>
                            <input type="number" step="0.01" name="price" required placeholder="0.00" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <!-- Tipo de Artículo -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tipo de Artículo *</label>
                            <select name="is_service" x-model="tipo" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                                <option value="0">Extras (Tienda)</option>
                                <option value="1">Accesos y Pases</option>
                            </select>
                        </div>
                    </div>

                    <!-- CAMPOS DINÁMICOS: Solo se muestran si es de Tienda -->
                    <div x-show="tipo === '0'" class="space-y-6 pt-4 border-t border-gray-100" x-transition>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fotografía del Producto</label>
                            <input type="file" name="image" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 cursor-pointer">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stock Inicial *</label>
                                <input type="number" name="stock" value="10" :required="tipo === '0'" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stock Mínimo (Alerta) *</label>
                                <input type="number" name="min_stock" value="5" :required="tipo === '0'" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('productos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-colors text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 font-bold shadow-md shadow-orange-500/20 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Guardar Producto
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
