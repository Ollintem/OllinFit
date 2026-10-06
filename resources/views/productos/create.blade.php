<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6" x-data="{ tipo: 'Tienda' }">
        
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Nuevo Producto / Artículo</h1>
                <p class="text-sm text-gray-500 mt-0.5">Registra un nuevo artículo o acceso para el Punto de Venta.</p>
            </div>
            <a href="{{ route('productos.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">← Volver al inventario</a>
        </div>

        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
            @csrf

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
   <select name="is_service" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
    <option value="0">Extras (Tienda)</option>
    <option value="1">Accesos y Pases</option>
</select>


            <!-- CAMPOS DINÁMICOS: Solo se muestran si es de Tienda -->
            <div x-show="tipo === 'Tienda'" class="space-y-6 pt-4 border-t border-gray-100">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fotografía del Producto</label>
                    <input type="file" name="image" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 cursor-pointer">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stock Inicial *</label>
                        <input type="number" name="stock" value="10" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stock Mínimo (Alerta) *</label>
                        <input type="number" name="min_stock" value="5" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('productos.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100">Cancelar</a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md transition-colors cursor-pointer">Guardar Producto</button>
            </div>
        </form>
    </div>
</x-app-layout>