<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        
        <!-- Header con botón de regreso -->
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Rol</h1>
                <p class="text-sm text-gray-500 mt-1">Modifica el nombre y los accesos permitidos para este puesto.</p>
            </div>
        </div>

        <form action="{{ route('roles.update', $role->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Datos Generales -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Puesto / Rol <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" class="w-full md:w-1/2 border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
            </div>

            <!-- Matriz de Permisos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-4 pb-4 border-b border-gray-100">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <h2 class="text-base font-bold text-gray-800">Accesos y Permisos</h2>
                </div>

                <!-- Grid de Permisos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                        <label class="flex items-start gap-3 p-3 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <div class="relative inline-flex items-center mt-0.5">
                                <!-- Checkbox para enviar el ID del permiso -->
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="sr-only peer" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-500"></div>
                            </div>
                            <div class="flex-1">
                                <!-- Formateamos el nombre del permiso (ej: 'mostrar_planes' -> 'Mostrar planes') -->
                                <span class="text-sm font-semibold text-gray-700 capitalize">{{ str_replace('_', ' ', $permission->name) }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('empleados.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</x-app-layout>