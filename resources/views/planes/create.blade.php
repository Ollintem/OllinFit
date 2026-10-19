<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        
        <!-- Header con botón de regreso -->
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('planes.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Plan</h1>
        </div>

        <form action="{{ route('planes.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Tarjeta 1: Detalles de la Membresía -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                
                <!-- Encabezado de la tarjeta -->
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h2 class="text-base font-bold text-gray-800">Detalles de la Membresía</h2>
                </div>
                <p class="text-[13px] text-gray-500 mb-6">Define el nombre, costo y la duración en días exactos para que el sistema calcule los vencimientos de forma automática.</p>

                <div class="space-y-5">
                    <!-- Nombre del Plan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Plan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. Trimestral VIP" required>
                    </div>

                    <!-- Fila de Precio y Duración -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Precio -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Precio (MXN) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    
                                </div>
                                <input type="number" step="0.01" name="price" class="pl-8 w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="$" required>
                            </div>
                        </div>

                        <!-- Duración -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Duración (Días) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="duration_days" class="pr-12 w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. 30" required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none border-l border-gray-200 my-2">
                                    
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">Ingresa el valor exacto en días (ej. 30, 90, 180).</p>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Descripción breve (Opcional)</label>
                        <input type="text" name="description" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. Ahorro del 15% vs mensual. Incluye acceso a todas las áreas.">
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2: Estado -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Estado del Plan</h2>
                    <p class="text-[12px] text-gray-500 mt-1">Si está inactivo, no aparecerá como opción al registrar o renovar un socio.</p>
                </div>
                
                <!-- Toggle Switch -->
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                    <span class="text-sm font-bold text-emerald-600">Activo</span>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('planes.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Guardar Plan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>