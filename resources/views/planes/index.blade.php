<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Header de la sección -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Catálogo de Planes</h1>
                <p class="text-gray-500 text-sm mt-1">Gestiona los precios y duraciones de las suscripciones (Seeders activos).</p>
            </div>
            
            
        </div>

        <!-- Grid de Planes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            @foreach($planes as $plan)
                <!-- Tarjeta de Plan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col relative transition-all hover:shadow-md">
                    
                    <!-- Franja de color superior dinámica -->
                    @php
                        $colorClass = match($plan->color) {
                            'blue' => 'bg-blue-500',
                            'orange' => 'bg-orange-500',
                            'green' => 'bg-green-500',
                            'purple' => 'bg-purple-500',
                            default => 'bg-gray-800'
                        };
                    @endphp
                    <div class="h-1.5 w-full {{ $colorClass }}"></div>

                    <!-- Contenido principal -->
                    <div class="p-5 flex-1 flex flex-col">
                        <!-- Título y Badge -->
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-base font-bold text-gray-800">{{ $plan->name }}</h3>
                            @if($plan->is_active)
                                <span class="bg-green-50 text-green-600 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full tracking-wide">Activo</span>
                            @else
                                <span class="bg-red-50 text-red-600 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full tracking-wide">Inactivo</span>
                            @endif
                        </div>

                        <!-- Precio -->
                        <div class="mb-1">
                            <span class="text-3xl font-extrabold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                            <span class="text-xs font-bold text-gray-400">MXN</span>
                        </div>

                        <!-- Texto destacado (Ahorro) - Mantiene el espacio aunque esté vacío -->
                        <div class="h-5 mb-2">
                            @if($plan->highlight_text)
                                <p class="text-xs font-bold text-orange-500">{{ $plan->highlight_text }}</p>
                            @endif
                        </div>

                        <!-- Descripción -->
                        <p class="text-[13px] text-gray-500 mb-5 flex-1 leading-relaxed">{{ $plan->description }}</p>

                        <!-- Caja de estadísticas -->
                        <div class="bg-gray-50/80 rounded-lg p-3 space-y-2 border border-gray-100">
                            <div class="flex justify-between items-center text-[13px]">
                                <div class="flex items-center gap-1.5 text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="font-medium">Duración:</span>
                                </div>
                                <span class="font-bold text-gray-700">{{ $plan->duration_days }} Días</span>
                            </div>
                            
                           <div class="flex items-center justify-between text-[11px] text-gray-500 mt-2">
        <div class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Socios activos:
        </div>
        <!-- Esta es la variable que imprime el número en pantalla -->
        <span class="font-bold text-gray-800">{{ $plan->members_count }}</span>
    </div>
                        </div>
                    </div>

                    <!-- Botones de Acción (Footer de la tarjeta) -->
                    <div class="border-t border-gray-100 p-3 px-5 flex justify-end gap-4 bg-white">
                        <a href="{{ route('planes.edit', $plan->id) }}" class="flex items-center gap-1 text-[11px] font-bold text-gray-400 hover:text-blue-600 transition-colors uppercase tracking-wide">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Editar
                        </a>
                        <!-- Botón de Eliminar (Envuelto en un form) -->
                        <form action="{{ route('planes.destroy', $plan->id) }}" method="POST" class="inline-block form-eliminar">
                            @csrf
                            @method('DELETE')
                            <!-- Botón de Eliminar (Dispara el Modal Seguro) -->
                         <button type="button" onclick="confirmarEliminacionPlan({{ $plan->id }}, '{{ $plan->name }}')" class="flex items-center gap-1 text-[11px] font-bold text-gray-400 hover:text-red-600 transition-colors uppercase tracking-wide cursor-pointer">
                         <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                         Eliminar
                         </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <!-- Tarjeta "Crear nuevo plan" -->
            <!-- Al usar h-full, tomará exactamente la altura de las tarjetas de al lado en el grid -->
            <a href="{{ route('planes.create') }}" class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 hover:bg-gray-50 hover:border-orange-300 transition-all flex flex-col items-center justify-center text-gray-400 hover:text-orange-500 group h-full min-h-[250px]">
                <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="font-medium text-sm">Crear nuevo plan</span>
            </a>

        </div>
    </div>

    <!-- Script para confirmar eliminación con SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formsEliminar = document.querySelectorAll('.form-eliminar');
            
            formsEliminar.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Detiene el envío inmediato del formulario
                    
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer. Se eliminará el empleado del sistema.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f97316', // Color naranja de OllinFit
                        cancelButtonColor: '#9ca3af',  // Color gris
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit(); // Si el usuario confirma, ahora sí enviamos el formulario
                        }
                    });
                });
            });
        });
    </script>
    <!-- ==========================================
         MODAL DE SEGURIDAD PARA ELIMINAR PLANES
         ========================================== -->
    <div id="modal-delete-plan" class="fixed inset-0 z-50 hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modal-delete-plan-content">
            <div class="p-6 text-center">
                <!-- Icono de Peligro -->
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4 border-4 border-red-50">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2">¿Eliminar este Plan?</h3>
                <p class="text-gray-500 text-sm mb-6">Estás a punto de eliminar el plan <strong id="delete-plan-name" class="text-gray-800 uppercase"></strong>. Si hay socios usando este plan, el sistema bloqueará la acción por seguridad.</p>

                <!-- Formulario Real de Eliminación -->
                <form id="form-delete-plan-secure" action="" method="POST" class="flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="cerrarModalEliminarPlan()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm w-full">
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

    <!-- Script de Seguridad -->
    <script>
        function confirmarEliminacionPlan(id, nombre) {
            const modal = document.getElementById('modal-delete-plan');
            const modalContent = document.getElementById('modal-delete-plan-content');
            
            document.getElementById('delete-plan-name').textContent = nombre;

            let urlBase = '{{ route("planes.destroy", ":id") }}';
            document.getElementById('form-delete-plan-secure').action = urlBase.replace(':id', id);

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            }, 10);
        }

        function cerrarModalEliminarPlan() {
            const modal = document.getElementById('modal-delete-plan');
            const modalContent = document.getElementById('modal-delete-plan-content');

            modal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</x-app-layout>