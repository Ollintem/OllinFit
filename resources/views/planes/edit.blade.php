<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        
        <!-- Header con botón de regreso -->
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('planes.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Editar Plan: {{ $plan->name }}</h1>
        </div>

        <form action="{{ route('planes.update', $plan->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Tarjeta 1: Detalles de la Membresía -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <h2 class="text-base font-bold text-gray-800">Modificar Detalles</h2>
                </div>
                <p class="text-[13px] text-gray-500 mb-6">Actualiza los datos de la membresía. Los cambios aplicarán para los nuevos socios que adquieran este plan.</p>

                <div class="space-y-5">
                    <!-- Nombre del Plan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Plan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Precio -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Precio (MXN) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm font-medium">$</span>
                                </div>
                                <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price) }}" class="pl-8 w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                            </div>
                        </div>

                        <!-- Duración -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Duración (Días) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" class="pr-12 w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none border-l border-gray-200 my-2">
                                    <span class="pl-3 text-gray-400 text-sm font-medium">Días</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Descripción breve (Opcional)</label>
                        <input type="text" name="description" value="{{ old('description', $plan->description) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                        <!-- Texto destacado -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Texto Destacado (Opcional)</label>
                            <input type="text" name="highlight_text" value="{{ old('highlight_text', $plan->highlight_text) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. Ahorro del 16% vs mensual">
                        </div>

                        <!-- Color de la tarjeta -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Color de la tarjeta</label>
                            <select name="color" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                                <option value="blue" {{ old('color', $plan->color) == 'blue' ? 'selected' : '' }}>Azul</option>
                                <option value="orange" {{ old('color', $plan->color) == 'orange' ? 'selected' : '' }}>Naranja</option>
                                <option value="green" {{ old('color', $plan->color) == 'green' ? 'selected' : '' }}>Verde</option>
                                <option value="purple" {{ old('color', $plan->color) == 'purple' ? 'selected' : '' }}>Morado</option>
                                <option value="gray" {{ old('color', $plan->color) == 'gray' ? 'selected' : '' }}>Gris</option>
                            </select>
                        </div>
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
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
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
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Actualizar Plan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>