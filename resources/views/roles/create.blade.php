<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">

        <!-- Header con botón de regreso -->
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Rol</h1>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- SECCIÓN 1: Detalles del Puesto -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    Detalles del Puesto
                </h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rol <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Entrenador Personal, Cajero, Gerente..." class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (Opcional)</label>
                        <textarea name="description" rows="2" placeholder="Breve descripción de las responsabilidades de este rol..." class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: Matriz de Permisos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-8 pb-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Matriz de Permisos
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Activa cada acción que este rol podrá realizar dentro de cada módulo de Ollinfit.</p>
                </div>

                <div class="p-8 pt-6">
                    @include('roles.partials.matriz-permisos', ['role' => null])
                </div>

                <!-- Nota de Seguridad -->
                <div class="m-6 bg-blue-50/80 border border-blue-100 p-4 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm text-blue-800 leading-relaxed">
                        <strong class="font-bold">Nota de Seguridad:</strong> Ten cuidado al otorgar permisos de "Eliminar" y de operar la Caja, ya que estas acciones son irreversibles y afectan el corte de caja.
                    </p>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="flex items-center justify-end gap-4 mt-6">
                <a href="{{ route('empleados.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">Cancelar</a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Guardar Rol
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
