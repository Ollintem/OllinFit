<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            <!-- Encabezado Oscuro -->
            <div class="bg-zinc-900 px-8 py-6 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Editar Rol: {{ $role->name }}
                </h2>
                <a href="{{ route('empleados.index') }}" class="text-xs font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg border border-zinc-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver
                </a>
            </div>

            <div class="p-8">
                <form action="{{ route('roles.update', $role->id) }}" method="POST" class="space-y-8">
                    @csrf
                    <x-form-errors />
                    @method('PUT')

                    <!-- SECCIÓN 1: Datos Generales -->
                    <div>
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Detalles del Puesto</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Puesto / Rol <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}" class="w-full md:w-1/2 border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Matriz de Permisos -->
                    <div class="pt-8 border-t border-gray-100">
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Accesos y Permisos</h3>
                        <p class="text-sm text-gray-500 mb-6">Modifica las acciones permitidas para este puesto dentro de cada módulo.</p>

                        @include('roles.partials.matriz-permisos', ['role' => $role])
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('empleados.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-colors text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 font-bold shadow-md shadow-orange-500/20 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
