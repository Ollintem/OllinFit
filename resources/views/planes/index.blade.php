<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Header de la sección -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Catálogo de Planes</h1>
                <p class="text-gray-500 text-sm mt-1">Gestiona los precios y duraciones de las suscripciones del gimnasio.</p>
            </div>
        </div>

        <!-- Grid de Planes -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            
            @foreach($planes as $plan)
                <!-- Tarjeta de Plan -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between h-full relative transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    
                    <!-- BLOQUE SUPERIOR -->
                    <div class="flex-1 flex flex-col">
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

                        <!-- Contenido de la tarjeta -->
                        <div class="p-6 flex-1 flex flex-col">
                            
                            <!-- Badge MÁS POPULAR Compacto (Optimizado) -->
                            @if(isset($mostPopularPlanId) && $plan->id === $mostPopularPlanId)
                                <div class="mb-3 self-start inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200/80 text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-xs">
                                    <span>⭐</span> MÁS POPULAR
                                </div>
                            @endif

                            <!-- Título y Badge de Estado -->
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-lg font-bold text-gray-800 capitalize">{{ $plan->name }}</h3>
                                @if($plan->is_active)
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-600 text-[10px] uppercase font-bold px-2.5 py-1 rounded-full border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 text-[10px] uppercase font-bold px-2.5 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>

                            <!-- Precio -->
                            <div class="mb-1 flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-xs font-bold text-gray-400">MXN</span>
                            </div>

                            <!-- Texto destacado (Ahorro) -->
                            <div class="h-6 mb-2">
                                @if($plan->highlight_text)
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-orange-500 bg-orange-50 px-2 py-0.5 rounded-md">
                                        🏷️ {{ $plan->highlight_text }}
                                    </span>
                                @endif
                            </div>

                            <!-- Descripción -->
                            <p class="text-[13px] text-gray-500 mb-6 flex-1 leading-relaxed line-clamp-3">
                                {{ $plan->description ?? 'Sin descripción adicional.' }}
                            </p>

                            <!-- Caja de estadísticas -->
                            <div class="bg-gray-50/80 rounded-xl p-3.5 space-y-2 border border-gray-100 mt-auto">
                                <div class="flex justify-between items-center text-[13px]">
                                    <div class="flex items-center gap-1.5 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="font-medium text-gray-500">Duración:</span>
                                    </div>
                                    <span class="font-bold text-gray-800">{{ $plan->duration_days }} Días</span>
                                </div>
                                
                                <div class="flex items-center justify-between text-[12px] text-gray-500 pt-2 border-t border-gray-200/60">
                                    <div class="flex items-center gap-1.5 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        <span class="font-medium text-gray-500">Socios activos:</span>
                                    </div>
                                    <span class="font-bold text-gray-800">{{ $plan->members_count ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE INFERIOR FIJO: Botones de Acción (Footer) -->
                    <div class="border-t border-gray-100 p-3.5 px-6 flex justify-end items-center gap-4 bg-white">
                        <a href="{{ route('planes.edit', $plan->id) }}" class="flex items-center gap-1 text-[11px] font-bold text-gray-400 hover:text-blue-600 transition-colors uppercase tracking-wide">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Editar
                        </a>

                        @if(($plan->members_count ?? 0) === 0)
                            <button type="button" onclick="confirmarEliminacionPlan({{ $plan->id }}, '{{ $plan->name }}')" class="flex items-center gap-1 text-[11px] font-bold text-gray-400 hover:text-red-600 transition-colors uppercase tracking-wide cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar
                            </button>
                        @else
                            <span class="flex items-center gap-1 text-[11px] font-bold text-gray-300 cursor-not-allowed uppercase tracking-wide" title="No se puede eliminar porque tiene socios activos inscritos">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Tarjeta "Crear nuevo plan" -->
            <a href="{{ route('planes.create') }}" class="rounded-2xl border-2 border-dashed border-gray-200 hover:border-orange-400 bg-gray-50/50 hover:bg-orange-50/20 transition-all duration-300 flex flex-col items-center justify-center text-gray-400 hover:text-orange-600 group h-full min-h-[300px] p-6 shadow-xs hover:shadow-md">
                <div class="w-12 h-12 bg-white group-hover:bg-orange-500 rounded-full shadow-sm group-hover:shadow-md flex items-center justify-center mb-3 group-hover:scale-110 transition-all duration-300">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="font-bold text-sm text-gray-600 group-hover:text-orange-600 transition-colors">Crear nuevo plan</span>
                <span class="text-xs text-gray-400 mt-1">Añade precios y duraciones</span>
            </a>

        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="modal-delete-plan" class="fixed inset-0 z-50 hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modal-delete-plan-content">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4 border-4 border-red-50">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2">¿Eliminar este Plan?</h3>
                <p class="text-gray-500 text-sm mb-6">Estás a punto de eliminar el plan <strong id="delete-plan-name" class="text-gray-800 uppercase"></strong>. Esta acción no se puede deshacer.</p>

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

    <!-- Script para Modal de Eliminación -->
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