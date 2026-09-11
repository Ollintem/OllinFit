<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        
        <!-- Header con botón de regreso -->
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Rol</h1>
        </div>

        <!-- FORMULARIO DE ROL -->
        <!-- Nota: La ruta 'roles.store' la crearemos en el siguiente paso -->
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
                        <input type="text" name="name" placeholder="Ej. Entrenador Personal, Cajero, Gerente..." class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (Opcional)</label>
                        <textarea name="description" rows="2" placeholder="Breve descripción de las responsabilidades de este rol..." class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500"></textarea>
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
                    <p class="text-sm text-gray-500 mt-1">Define exactamente qué acciones podrá realizar este rol dentro del sistema de Ollinfit.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 font-semibold">Módulo del Sistema</th>
                                <th class="px-6 py-4 font-semibold text-center">Ver / Acceder</th>
                                <th class="px-6 py-4 font-semibold text-center">Crear / Editar</th>
                                <th class="px-6 py-4 font-semibold text-center text-red-400">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            
                            <!-- Fila: Dashboard -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 flex items-center gap-3 font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Dashboard Principal
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="mostrar_dashboard" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center text-gray-300">-</td>
                                <td class="px-6 py-4 text-center text-gray-300">-</td>
                            </tr>

                            <!-- Fila: Socios -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 flex items-center gap-3 font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    Gestión de Socios
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="mostrar_socios" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="crear_editar_socios" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="eliminar_socios" class="w-5 h-5 rounded border-red-300 text-red-500 focus:ring-red-500 cursor-pointer">
                                </td>
                            </tr>

                            <!-- Fila: Planes -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 flex items-center gap-3 font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    Planes y Membresías
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="mostrar_planes" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="crear_editar_planes" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="eliminar_planes" class="w-5 h-5 rounded border-red-300 text-red-500 focus:ring-red-500 cursor-pointer">
                                </td>
                            </tr>

                            <!-- Fila: Reportes -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 flex items-center gap-3 font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Reportes y Corte de Caja
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="mostrar_reportes" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center text-gray-300">-</td>
                                <td class="px-6 py-4 text-center text-gray-300">-</td>
                            </tr>

                            <!-- Fila: Personal -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 flex items-center gap-3 font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Personal y Roles
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="mostrar_empleados" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="crear_empleados" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="eliminar_empleados" class="w-5 h-5 rounded border-red-300 text-red-500 focus:ring-red-500 cursor-pointer">
                                </td>
                            </tr>

                            <!-- Fila: Configuración -->
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 flex items-center gap-3 font-medium text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Configuración del Sistema
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="mostrar_configuracion" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="permissions[]" value="editar_configuracion" class="w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-center text-gray-300">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Nota de Seguridad -->
                <div class="m-6 bg-blue-50/80 border border-blue-100 p-4 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm text-blue-800 leading-relaxed">
                        <strong class="font-bold">Nota de Seguridad:</strong> Ten cuidado al otorgar permisos de "Eliminar", especialmente en los módulos de Socios y Planes, ya que estas acciones son irreversibles y pueden afectar el corte de caja.
                    </p>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="flex items-center justify-end gap-4 mt-6">
                <a href="{{ route('empleados.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Guardar Rol
                </button>
            </div>
            
        </form>
    </div>
</x-app-layout>