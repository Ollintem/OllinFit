<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        
        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Producto</h1>
                <p class="text-sm text-gray-500 mt-1">Actualiza los datos o reabastece el stock de: <span class="font-bold text-gray-700">{{ $product->name }}</span></p>
            </div>
            <a href="{{ route('productos.index') }}" class="text-gray-500 hover:text-orange-500 transition-colors text-sm font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver al inventario
            </a>
        </div>

        <!-- Manejo de Errores -->
        @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded-lg text-sm border border-red-100">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tarjeta del Formulario -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            
            <form action="{{ route('productos.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Retícula Principal de 2 Columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- COLUMNA IZQUIERDA -->
                    <div class="space-y-4">
                        <!-- Nombre del Producto -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                   class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                        </div>

                        <!-- Precio de Venta -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta ($) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required
                                   class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                        </div>

                        <!-- Tipo de Artículo (Desplegable) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Artículo <span class="text-red-500">*</span></label>
                            <select name="tipo_item" id="tipo_item" onchange="toggleStockFields()" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                                <option value="extra" {{ old('tipo_item', $product->is_service ? 'acceso' : 'extra') == 'extra' ? 'selected' : '' }}>Extras (Tienda)</option>
                                <option value="acceso" {{ old('tipo_item', $product->is_service ? 'acceso' : 'extra') == 'acceso' ? 'selected' : '' }}>Accesos y Pases</option>
                            </select>
                        </div>

                        <!-- Contenedor Dinámico de Stock (Se oculta si es Acceso y Pases) -->
                        <div id="stock-container" class="space-y-4">
                            <!-- Stock Mínimo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo (Alerta) <span class="text-red-500">*</span></label>
                                <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $product->min_stock ?? 5) }}"
                                       class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                            </div>

                            <!-- Control de Stock Actual y Entrada -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual</label>
                                    <input type="text" value="{{ $product->stock }} unids." disabled 
                                           class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-500 cursor-not-allowed text-center font-bold text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Entrada de Mercancía *</label>
                                    <input type="number" name="stock_a_agregar" id="stock_a_agregar" value="0" min="0" 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500 text-center font-bold text-orange-600 text-sm">
                                </div>
                            </div>
                            <p class="text-xs text-orange-500 font-medium">Las piezas ingresadas se sumarán automáticamente al inventario actual al guardar.</p>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA: Fotografía y Botones de Acción -->
                    <div class="space-y-4 flex flex-col justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fotografía del Producto</label>
                            
                            <!-- Foto actual si existe -->
                            @if($product->image_path)
                                <div class="mb-3 relative w-20 h-20 rounded-lg border border-gray-200 overflow-hidden shadow-sm bg-gray-50">
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto actual" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                        <span class="text-white text-[10px] font-bold uppercase tracking-wider">Actual</span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-center px-4 pt-4 pb-4 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="space-y-1 text-center">
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="image" class="relative cursor-pointer bg-transparent rounded-md font-medium text-orange-500 hover:text-orange-600">
                                            <span id="file-name-display">Sube un archivo nuevo</span>
                                            <input id="image" name="image" type="file" accept=".png, .jpg, .jpeg, .webp" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-[11px] text-gray-500">PNG, JPG, WEBP.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botones Alineados al Fondo de la Columna Derecha -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('productos.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-xs text-center">
                                Cancelar
                            </a>
                            <button type="submit" class="px-5 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-bold shadow-sm transition-colors text-xs flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    
    <!-- Script para alternar los campos de stock y el nombre del archivo -->
    <script>
        function toggleStockFields() {
            const tipo = document.getElementById('tipo_item').value;
            const container = document.getElementById('stock-container');
            const minStockInput = document.getElementById('min_stock');
            const stockAgregaInput = document.getElementById('stock_a_agregar');

            if (tipo === 'acceso') {
                container.style.display = 'none';
                minStockInput.disabled = true;
                if (stockAgregaInput) stockAgregaInput.disabled = true;
            } else {
                container.style.display = 'block';
                minStockInput.disabled = false;
                if (stockAgregaInput) stockAgregaInput.disabled = false;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleStockFields();
        });

        document.getElementById('image').addEventListener('change', function(e) {
            const fileNameDisplay = document.getElementById('file-name-display');
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                fileNameDisplay.textContent = '¡Listo! ' + fileName;
                fileNameDisplay.classList.remove('text-orange-500');
                fileNameDisplay.classList.add('text-emerald-600');
            }
        });
    </script>
</x-app-layout>