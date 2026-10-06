<x-app-layout>

    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Título de la página -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Socios</h1>
        </div>

        <!-- Tarjeta Principal -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
            
            <!-- Cabecera de la tabla (Título, Buscador y Botón) -->
            <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Lista de Socios</h2>
                    <p class="text-sm text-gray-500 mt-1">Mostrando 7 socios registrados</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <!-- Buscador -->
                        <form action="{{ route('socios.index') }}" method="GET" class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            
                            <!-- El atributo name="search" es la clave aquí -->
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Buscar socio por nombre o folio..." 
                                   class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 transition-colors bg-gray-50/50"
                                   onchange="this.form.submit()"> <!-- Envía el form al presionar Enter -->
                        </form>
                    
                    <!-- Botón Nuevo Socio -->
                    <a href="{{ route('socios.create') }}" class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo Socio
                    </a>
                </div>
            </div>

            <!-- Tabla de Socios -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4">Folio</th>
                            <th class="px-6 py-4">Foto</th>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Plan Activo</th>
                            <th class="px-6 py-4">Fecha Vencimiento</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($socios as $socio)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <!-- Folio -->
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $socio->folio }}
                            </td>
                            
                            <!-- Foto -->
                            <td class="px-6 py-4">
                                @if($socio->profile_photo_path)
                                    <!-- Si el socio tiene foto, mostramos la imagen -->
                                    <div class="w-8 h-8 rounded-full shadow-sm overflow-hidden border border-gray-200 bg-gray-50">
                                        <img src="{{ asset('storage/' . $socio->profile_photo_path) }}" alt="Foto de {{ $socio->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <!-- Si no tiene foto (o falló), mostramos sus iniciales -->
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold shadow-sm overflow-hidden border border-gray-200">
                                        {{ strtoupper(substr($socio->name, 0, 1)) }}{{ strtoupper(substr($socio->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Nombre -->
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                {{ $socio->name }} {{ $socio->last_name }}
                            </td>
                            
                            <!-- Plan Activo (Relación) -->
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $socio->plan ? $socio->plan->name : 'Sin plan asignado' }}
                            </td>
                            
                            <!-- Fecha de Vencimiento -->
                            <td class="px-6 py-4 text-sm font-medium {{ $socio->status === 'Vencido' ? 'text-red-500' : 'text-gray-600' }}">
                                {{ $socio->expiration_date ? $socio->expiration_date->format('d/M/Y') : 'N/A' }}
                            </td>
                            
                            <!-- Estado Automático -->
                            <td class="px-6 py-4">
                                @if($socio->status === 'Activo')
                                    <span class="bg-emerald-100 text-emerald-700 text-[11px] font-bold px-2.5 py-1 rounded-full">Activo</span>
                                @elseif($socio->status === 'Vencido')
                                    <span class="bg-red-100 text-red-700 text-[11px] font-bold px-2.5 py-1 rounded-full">Vencido</span>
                                @elseif($socio->status === 'Por vencer')
                                    <span class="bg-yellow-100 text-yellow-700 text-[11px] font-bold px-2.5 py-1 rounded-full">Por vencer</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 text-[11px] font-bold px-2.5 py-1 rounded-full">{{ $socio->status }}</span>
                                @endif
                            </td>
                            
                        
                            <!-- Acciones -->
                            <td class="px-6 py-4 text-right space-x-3">
                                <!-- BOTÓN VER -->
                                <a href="{{ route('socios.show', $socio->id) }}" class="text-gray-300 hover:text-emerald-500 transition-colors inline-block" title="Ver detalles">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                <!-- BOTÓN EDITAR -->
                                <a href="{{ route('socios.edit', $socio->id) }}" class="text-gray-300 hover:text-blue-500 transition-colors inline-block" title="Editar socio">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>

                                <!-- BOTÓN ELIMINAR (Dispara el modal) -->
                                <button type="button" onclick="confirmarEliminacionSocio({{ $socio->id }}, '{{ $socio->name }} {{ $socio->last_name }}')" class="text-gray-300 hover:text-red-500 transition-colors inline-block" title="Eliminar socio">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
            {{ $socios->links() }}
        </div>

        </div>
    </div>
    <!-- MODAL SEGURIDAD ELIMINAR SOCIO -->
    <div id="modal-delete-socio" class="fixed inset-0 z-50 hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modal-delete-socio-content">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4 border-4 border-red-50">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2">¿Estás completamente seguro?</h3>
                <p class="text-gray-500 text-sm mb-6">Estás a punto de eliminar a <strong id="delete-socio-name" class="text-gray-800 uppercase"></strong>. Esta acción eliminará permanentemente su acceso.</p>

                <form id="form-delete-socio-secure" action="" method="POST" class="flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="cerrarModalEliminarSocio()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm w-full">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-bold shadow-sm transition-colors text-sm w-full flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT DEL MODAL -->
    <script>
        function confirmarEliminacionSocio(id, nombre) {
            const modal = document.getElementById('modal-delete-socio');
            const modalContent = document.getElementById('modal-delete-socio-content');
            
            document.getElementById('delete-socio-name').textContent = nombre;

            // Inyectar ruta
            let urlBase = '{{ route("socios.destroy", ":id") }}';
            document.getElementById('form-delete-socio-secure').action = urlBase.replace(':id', id);

            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); }, 10);
        }

        function cerrarModalEliminarSocio() {
            const modal = document.getElementById('modal-delete-socio');
            const modalContent = document.getElementById('modal-delete-socio-content');

            modal.classList.add('opacity-0'); modalContent.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }
    </script>
</x-app-layout>