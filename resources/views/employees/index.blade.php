<x-app-layout>
    <div class="p-6 space-y-6">
        
        <!-- Título de la página -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Personal y Roles</h1>
        </div>

        <!-- Grid Principal (2 Columnas en pantallas grandes) -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <!-- COLUMNA IZQUIERDA: Directorio de Personal -->
            <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
                
                <!-- Cabecera de la tabla -->
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Directorio de Personal</h2>
                        <p class="text-sm text-gray-500 mt-1">Gestiona los accesos y datos de tu equipo.</p>
                    </div>
                    <!-- Botón para ir al formulario de alta -->
                    <a href="{{ route('empleados.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo Empleado
                    </a>
                </div>

                <!-- Tabla de Empleados -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs font-semibold text-gray-400 uppercase bg-gray-50/50">
                                <th class="px-6 py-4">Foto</th>
                                <th class="px-6 py-4">Nombre y Correo</th>
                                <th class="px-6 py-4">Rol Asignado</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($employees as $employee)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- Foto (Muestra la imagen o las iniciales como respaldo) -->
                                <td class="px-6 py-4">
                                    @if($employee->profile_photo_path)
                                        <!-- Muestra la foto de perfil real -->
                                        <img src="{{ asset('storage/' . $employee->profile_photo_path) }}" 
                                            alt="Foto de {{ $employee->name }}" 
                                            class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm">
                                    @else
                                        <!-- Fallback: Iniciales si no subieron foto -->
                                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <!-- Nombre y Correo -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $employee->name }} {{ $employee->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                                </td>
                                <!-- Rol -->
                                <td class="px-6 py-4">
                                    @if($employee->hasRole('Administrador'))
                                        <span class="bg-gray-900 text-white text-xs px-3 py-1 rounded-full font-medium">Administrador</span>
                                    @elseif($employee->roles->count() > 0)
                                        <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-medium">{{ $employee->roles->first()->name }}</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">Sin acceso al sistema</span>
                                    @endif
                                </td>
                                <!-- Estado -->
                                <td class="px-6 py-4">
                                    @if($employee->is_active)
                                        <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Activo</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full font-medium">Inactivo</span>
                                    @endif
                                </td>
                                <!-- Acciones -->
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('empleados.edit', $employee->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <!-- Si no es admin, mostrar botón eliminar -->
                                    @if(!$employee->hasRole('Administrador'))
                                        <!-- Botón de Eliminar Empleado (Envuelto en form) -->
                                        <form action="{{ route('empleados.destroy', $employee->id) }}" method="POST" class="inline-block form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar empleado">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Gestión de Roles -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col gap-6">
                
                <div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Gestión de Roles
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Crea puestos de trabajo para clasificar a tu personal o limitar sus accesos.</p>
                </div>

                <!-- Añadir Nuevo Rol -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Añadir Nuevo Rol</label>
                    
                    <!-- Enlace con diseño de input -->
                    <a href="{{ route('roles.create') }}" class="flex items-center justify-between w-full border border-gray-200 bg-white rounded-lg text-sm p-2 hover:border-orange-500 hover:ring-1 hover:ring-orange-500 transition-all group cursor-pointer">
                        
                        <span class="text-gray-400 group-hover:text-gray-600 px-2">Ej. Entrenador, Limpiador...</span>
                        
                        <div class="bg-gray-900 text-white p-1.5 rounded-md group-hover:bg-orange-500 transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        
                    </a>
                </div>

                <!-- Lista de Roles -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-3">Roles Actuales</label>
                    <div class="space-y-3">
                        @foreach($roles as $role)
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $role->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($role->name === 'Administrador')
                                            <!-- Candado: Bloqueado por seguridad -->
                                            <svg class="w-4 h-4 text-gray-300" title="Rol protegido" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        @else
                                            <!-- Botones de Acción para el Rol -->
                                            <div class="flex items-center gap-3">
                                                <!-- Editar Rol -->
                                                <a href="{{ route('roles.edit', $role->id) }}" class="text-gray-400 hover:text-blue-500 transition-colors" title="Editar Rol">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                                
                                                <!-- Eliminar Rol -->
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block form-eliminar-rol">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar Rol">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($role->name === 'Administrador')
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Script para confirmar eliminación con SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Alerta para Empleados
            const formsEliminarEmpleado = document.querySelectorAll('.form-eliminar');
            formsEliminarEmpleado.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); 
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "El empleado perderá su acceso al sistema de forma permanente.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f97316', 
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) { this.submit(); }
                    });
                });
            });

            // 2. Alerta para Roles
            const formsEliminarRol = document.querySelectorAll('.form-eliminar-rol');
            formsEliminarRol.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); 
                    Swal.fire({
                        title: '¿Eliminar este Rol?',
                        text: "Los empleados que tengan este rol se quedarán sin accesos asignados.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444', // Rojo
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Sí, eliminar rol',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) { this.submit(); }
                    });
                });
            });

        });
    </script>
</x-app-layout>