<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            <!-- Encabezado Oscuro -->
            <div class="bg-zinc-900 px-8 py-6 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Crear Nuevo Plan
                </h2>
                <a href="{{ route('planes.index') }}" class="text-xs font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg border border-zinc-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver
                </a>
            </div>

            <div class="p-8">
                <form action="{{ route('planes.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <x-form-errors />

                    <!-- SECCIÓN: Detalles de la Membresía -->
                    <div>
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Detalles de la Membresía</h3>
                        <p class="text-[13px] text-gray-500 mb-5">Define el nombre, costo y la duración para que el sistema calcule los vencimientos de forma automática.</p>

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
                                            <span class="text-gray-500 text-sm font-medium">$</span>
                                        </div>
                                        <input type="number" step="0.01" name="price" class="pl-8 w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="0.00" required>
                                    </div>
                                </div>

                                <!-- Duración -->
                                @include('planes.partials.duracion', ['value' => old('duration_value', 1), 'unit' => old('duration_unit', 'months')])
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Descripción breve (Opcional)</label>
                                <input type="text" name="description" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. Ahorro del 15% vs mensual. Incluye acceso a todas las áreas.">
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Estado del Plan -->
                    <div class="pt-6 border-t border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Estado del Plan</h3>
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

                    <!-- Botones de Acción -->
                    <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('planes.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-colors text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 font-bold shadow-md shadow-orange-500/20 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Guardar Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
