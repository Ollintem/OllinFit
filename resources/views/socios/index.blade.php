<x-app-layout>

    <!-- Contenedor principal expandido -->
    <div x-data class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        <!-- Título de la página -->
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight">Socios</h1>
        </div>

        <!-- TARJETAS DE RESUMEN (KPIs) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6 mb-2">
            <!-- Tarjeta 1: Activos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-emerald-100 flex items-center justify-between group hover:shadow-md transition-shadow">
                <div>
                    <p class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider mb-1">Socios Activos</p>
                    <h3 class="text-3xl font-black text-slate-800">{{ $totalActivos ?? 0 }}</h3>
                </div>
                <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Tarjeta 2: Por Vencer -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-yellow-100 flex items-center justify-between group hover:shadow-md transition-shadow">
                <div>
                    <p class="text-[11px] font-bold text-yellow-500 uppercase tracking-wider mb-1">Por Vencer (5 días)</p>
                    <h3 class="text-3xl font-black text-slate-800">{{ $totalPorVencer ?? 0 }}</h3>
                </div>
                <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Tarjeta 3: Vencidos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-red-100 flex items-center justify-between group hover:shadow-md transition-shadow">
                <div>
                    <p class="text-[11px] font-bold text-red-500 uppercase tracking-wider mb-1">Suscripciones Vencidas</p>
                    <h3 class="text-3xl font-black text-slate-800">{{ $totalVencidos ?? 0 }}</h3>
                </div>
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Tarjeta Principal (Tabla) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
            
            <!-- Cabecera de la tabla -->
            <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Lista de Socios</h2>
                    <p class="text-sm text-gray-500 mt-1">Mostrando {{ $socios->total() }} socios registrados</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <!-- Filtros y Buscador -->
                    <form action="{{ route('socios.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <select name="estado" class="bg-gray-50/50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 py-2.5 pl-3 pr-10 transition-colors cursor-pointer" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="activos" {{ request('estado') == 'activos' ? 'selected' : '' }}>Solo Activos</option>
                            <option value="vencidos" {{ request('estado') == 'vencidos' ? 'selected' : '' }}>Solo Vencidos</option>
                            <option value="inactivos" {{ request('estado') == 'inactivos' ? 'selected' : '' }}>Suspendidos</option>
                        </select>

                        <select name="plan_id" class="bg-gray-50/50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 py-2.5 pl-3 pr-10 transition-colors cursor-pointer" onchange="this.form.submit()">
                            <option value="">Todos los planes</option>
                            @foreach ($planes as $plan)
                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>

                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o folio..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-orange-500 focus:border-orange-500 transition-colors bg-gray-50/50">
                        </div>
                        <button type="submit" class="hidden"></button>
                    </form>
                    
                    <!-- Botón Nuevo Socio -->
                    <a href="{{ route('socios.create') }}" class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nuevo Socio
                    </a>
                </div>
            </div>

            <!-- Tabla de Socios -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-4 rounded-tl-2xl">Folio</th>
                            <th class="px-6 py-4">Foto</th>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Plan Activo</th>
                            <th class="px-6 py-4">Fecha Vencimiento</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 text-right rounded-tr-2xl">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($socios as $socio)
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <!-- Folio -->
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $socio->folio }}</td>
                            
                            <!-- Foto -->
                            <td class="px-6 py-4">
                                @if($socio->profile_photo_path)
                                    <div class="w-10 h-10 rounded-full shadow-sm overflow-hidden border border-gray-200 bg-gray-50">
                                        <img src="{{ asset('storage/' . $socio->profile_photo_path) }}" alt="Foto" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-sm font-bold shadow-sm border border-gray-200">
                                        {{ strtoupper(substr($socio->name, 0, 1)) }}{{ strtoupper(substr($socio->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            
                            <!-- Nombre -->
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $socio->name }} {{$socio->last_name }}</td>
                            
                            <!-- Plan Activo -->
                            <td class="px-6 py-4 text-sm text-gray-500 font-medium">{{ $socio->plan ? $socio->plan->name : 'Sin plan' }}</td>
                            
                            <!-- Fecha de Vencimiento -->
                            <td class="px-6 py-4 text-sm font-medium {{ $socio->status === 'Vencido' ? 'text-red-500' : 'text-gray-500' }}">
                                {{ $socio->expiration_date ? $socio->expiration_date->format('d M Y') : 'N/A' }}
                            </td>
                            
                            <!-- Estado Automático -->
                            <td class="px-6 py-4">
                                @if($socio->status === 'Activo')
                                    <span class="bg-emerald-100 text-emerald-700 text-[11px] font-bold px-3 py-1 rounded-lg border border-emerald-200">Activo</span>
                                @elseif($socio->status === 'Vencido')
                                    <span class="bg-red-100 text-red-700 text-[11px] font-bold px-3 py-1 rounded-lg border border-red-200">Vencido</span>
                                @elseif($socio->status === 'Por vencer')
                                    <span class="bg-yellow-100 text-yellow-700 text-[11px] font-bold px-3 py-1 rounded-lg border border-yellow-200">Por vencer</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 text-[11px] font-bold px-3 py-1 rounded-lg border border-gray-200">{{ $socio->status }}</span>
                                @endif
                            </td>
                            
                            <!-- Acciones -->
                            <td class="px-6 py-4 text-right space-x-3">
                                
                                <!-- Botón Renovar Rápido (Diseño adaptable) -->
                                <button type="button"
                                    data-id="{{ $socio->id }}"
                                    data-nombre="{{ $socio->name }} {{ $socio->last_name }}"
                                    data-folio="{{ $socio->folio }}"
                                    data-exp="{{ $socio->expiration_date ? $socio->expiration_date->format('Y-m-d') : '' }}"
                                    data-plan="{{ $socio->plan_id ?? '' }}"
                                    onclick="abrirModalRenovacion(this)"
                                    class="text-emerald-600 hover:text-emerald-700 transition-colors inline-block bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg font-bold text-xs border border-emerald-200 shadow-sm" 
                                    title="Renovar Mensualidad">
                                    <svg class="w-4 h-4 inline-block mr-1 flex-shrink-0 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Renovar
                                </button>

                                <a href="{{ route('socios.show', $socio->id) }}" class="text-gray-300 hover:text-emerald-500 transition-colors inline-block" title="Ver detalles">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('socios.edit', $socio->id) }}" class="text-gray-300 hover:text-blue-500 transition-colors inline-block" title="Editar socio">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <button type="button" data-id="{{ $socio->id }}" data-nombre="{{ $socio->name }} {{ $socio->last_name }}" onclick="confirmarEliminacionSocio(this)" class="text-gray-300 hover:text-red-500 transition-colors inline-block" title="Eliminar socio">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-gray-100 bg-gray-50/30 rounded-b-2xl">
                {{ $socios->links() }}
            </div>
        </div>

    </div> <!-- FIN DEL CONTENEDOR PRINCIPAL -->

    <!-- ========================================== -->
    <!-- MODAL RENOVACIÓN Y CAMBIO DE PLAN PREMIUM -->
    <!-- ========================================== -->
    <div x-data="{
        showModalRenovar: false,
        loading: true,
        socioId: null,
        nombreSocio: '',
        folioSocio: '',
        planes: [],
        selectedPlan: null,
        paymentMethod: 'Efectivo',
        fechaBase: null,

        async initRenovacion(id) {
            this.showModalRenovar = true;
            this.loading = true;
            this.socioId = id;
            
            try {
                const res = await fetch(`/socios/${id}/datos-renovacion`);
                const data = await res.json();
                
                this.nombreSocio = data.nombre;
                this.folioSocio = data.folio;
                this.planes = data.planes;
                this.fechaBase = new Date(data.fecha_base + 'T00:00:00');
                
                const planSocio = this.planes.find(p => p.id == data.plan_actual_id) || this.planes[0];
                this.selectedPlan = planSocio;

                document.getElementById('form-renovar-socio').action = `/socios/${id}/renovar`;
                
                this.loading = false;
            } catch (e) {
                console.error('Error cargando datos:', e);
            }
        },

        getNuevaVigencia() {
            if (!this.fechaBase || !this.selectedPlan) return '';
            const tempDate = new Date(this.fechaBase.getTime());
            
            if (this.selectedPlan.name === 'Semestral') {
                tempDate.setMonth(tempDate.getMonth() + 6);
            } else {
                tempDate.setMonth(tempDate.getMonth() + 1);
            }

            return tempDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
        }
    }" 
    @abrir-modal-renovar.window="initRenovacion($event.detail.id)"
    x-show="showModalRenovar" 
    style="display: none;" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/80 backdrop-blur-sm p-4">
        
        <div @click.away="showModalRenovar = false"
             x-show="showModalRenovar"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-[32px] shadow-2xl w-full max-w-xl overflow-hidden relative border border-gray-100">
            
            <button type="button" @click="showModalRenovar = false" class="absolute top-6 right-6 w-8 h-8 rounded-full bg-gray-50 hover:bg-rose-50 hover:text-rose-500 text-gray-400 font-bold flex items-center justify-center transition-colors cursor-pointer border border-gray-100 z-10">✕</button>

            <template x-if="loading">
                <div class="p-16 flex flex-col items-center justify-center space-y-4">
                    <svg class="animate-spin h-10 w-10 text-orange-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <p class="text-sm font-semibold text-gray-500">Cargando datos del socio...</p>
                </div>
            </template>

            <template x-if="!loading">
                <form id="form-renovar-socio" method="POST" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="plan_id" :value="selectedPlan?.id">
                    <input type="hidden" name="payment_method" :value="paymentMethod">

                    <div class="space-y-1.5 pr-8">
                        <div class="flex items-center gap-2">
                            <span class="text-xl text-orange-500">🔄</span>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Renovar o Cambiar Plan</h3>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">Socio: <span class="text-gray-800 font-bold" x-text="nombreSocio"></span> <span class="text-gray-500 font-semibold" x-text="`(#${folioSocio})`"></span></p>
                    </div>

                   <!-- 1. Selección de Plan -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3">1. Selecciona el Plan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($planes as $plan): ?>
                            <label class="renew-plan-card relative p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 transition-all flex flex-col justify-between">
                                <input type="radio" name="plan_id" value="<?php echo $plan->id; ?>" class="peer sr-only plan-radio" data-price="<?php echo $plan->price; ?>" data-days="<?php echo $plan->duration_days ?? 30; ?>" required>
                                <div class="absolute top-3 right-3 w-3.5 h-3.5 rounded-full border border-gray-300 peer-checked:border-orange-500 peer-checked:border-[3.5px] transition-all bg-white"></div>
                                
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-bold text-gray-800 text-[13px] pr-5 leading-tight"><?php echo $plan->name; ?></span>
                                    <?php if (!empty($plan->highlight_text)): ?>
                                        <span class="text-[9px] font-bold text-emerald-600 uppercase"><?php echo $plan->highlight_text; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-2">
                                    <span class="text-lg font-extrabold text-orange-500">$<?php echo number_format($plan->price, 2); ?><span class="text-[10px] text-gray-400 font-bold ml-0.5">MXN</span></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        +<?php echo $plan->duration_days ?? 30; ?> días
                                    </span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">2. Método de Pago</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="paymentMethod = 'Efectivo'"
                                :class="paymentMethod === 'Efectivo' ? 'border-orange-500 bg-orange-500/5 text-orange-600 font-bold' : 'border-gray-200 text-gray-500 font-medium hover:border-gray-300'"
                                class="border rounded-xl py-3 flex items-center justify-center gap-2 text-sm transition-all shadow-sm">
                                <span>💵</span> Efectivo
                            </button>
                            <button type="button" @click="paymentMethod = 'Tarjeta'"
                                :class="paymentMethod === 'Tarjeta' ? 'border-orange-500 bg-orange-500/5 text-orange-600 font-bold' : 'border-gray-200 text-gray-500 font-medium hover:border-gray-300'"
                                class="border rounded-xl py-3 flex items-center justify-center gap-2 text-sm transition-all shadow-sm">
                                <span>💳</span> Tarjeta
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50/50 border border-gray-100 rounded-2xl p-5 flex flex-col sm:flex-row justify-between gap-4">
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Total a cobrar:</span>
                            <p class="text-xl font-black text-slate-900 mt-0.5">$<span x-text="Number(selectedPlan?.price || 0).toFixed(2)"></span> <span class="text-xs font-bold text-gray-400">MXN</span></p>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Nueva vigencia:</span>
                            <p class="text-sm font-extrabold text-emerald-600 mt-1 flex items-center sm:justify-end gap-1.5">
                                <span>➔</span> <span x-text="getNuevaVigencia()"></span>
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showModalRenovar = false" class="flex-1 px-4 py-3 bg-gray-50 text-gray-500 hover:bg-gray-100 border border-gray-100 rounded-xl font-bold transition-all text-sm cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-md transition-all text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Registrar Pago y Renovar
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- MODAL SEGURIDAD ELIMINAR SOCIO -->
    <div id="modal-delete-socio" class="fixed inset-0 z-50 hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 transition-transform duration-300" id="modal-delete-socio-content">
            <div class="p-8 text-center">
                <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6 border-8 border-red-50/50">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">¿Estás completamente seguro?</h3>
                <p class="text-gray-500 text-sm mb-8">Estás a punto de eliminar a <strong id="delete-socio-name" class="text-gray-900 uppercase"></strong>. Esta acción eliminará permanentemente su acceso y su historial.</p>

                <form id="form-delete-socio-secure" action="" method="POST" class="flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="cerrarModalEliminarSocio()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-bold transition-colors text-sm w-full">Cancelar</button>
                    <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 font-bold shadow-sm transition-colors text-sm w-full flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT DEL MODAL ELIMINAR -->
    <script>
        function confirmarEliminacionSocio(el) {
            const id = el.dataset.id;
            const nombre = el.dataset.nombre;
            const modal = document.getElementById('modal-delete-socio');
            const modalContent = document.getElementById('modal-delete-socio-content');
            document.getElementById('delete-socio-name').textContent = nombre;
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
    <!-- ========================================== -->
    <!-- MODAL DE RENOVACIÓN PREMIUM (ESTILO POS)   -->
    <!-- ========================================== -->
    <div id="renew-modal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300" id="renew-modal-content">
            
            <form id="renew-form" action="" method="POST">
                @csrf
                
                <!-- Header del Modal -->
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Renovar o Cambiar Plan
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Socio: <span id="renew-socio-name" class="font-bold text-gray-800"></span></p>
                    </div>
                    <button type="button" id="btn-close-renew" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body del Modal -->
                <div class="p-6 space-y-6">
                    
                    <!-- 1. Selección de Plan -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3">1. Selecciona el Plan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <?php foreach ($planes as$plan): ?>
                            <label class="renew-plan-card relative p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 transition-all flex flex-col justify-between">
                                <input type="radio" name="plan_id" value="<?php echo $plan->id; ?>" class="peer sr-only plan-radio" data-price="<?php echo $plan->price; ?>" data-days="<?php echo $plan->duration_days ?? 30; ?>" required>
                                <div class="absolute top-3 right-3 w-3.5 h-3.5 rounded-full border border-gray-300 peer-checked:border-orange-500 peer-checked:border-[3.5px] transition-all bg-white"></div>
                                
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-bold text-gray-800 text-[13px] pr-5 leading-tight"><?php echo $plan->name; ?></span>
                                    <?php if (!empty($plan->highlight_text)): ?>
                                        <span class="text-[9px] font-bold text-emerald-600 uppercase"><?php echo $plan->highlight_text; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-2">
                                    <span class="text-lg font-extrabold text-orange-500">$<?php echo number_format($plan->price, 2); ?><span class="text-[10px] text-gray-400 font-bold ml-0.5">MXN</span></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        +<?php echo $plan->duration_days ?? 30; ?> días
                                    </span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 2. Método de Pago (POS) -->
                    <div class="space-y-4">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">2. Método de Pago</label>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <label class="payment-card relative flex items-center justify-center gap-2 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all text-gray-600 font-semibold text-sm">
                                <input type="radio" name="payment_method" value="efectivo" class="peer sr-only" checked>
                                <svg class="w-5 h-5 text-gray-400 peer-checked:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="peer-checked:text-orange-500 transition-colors">Efectivo</span>
                            </label>
                            <label class="payment-card relative flex items-center justify-center gap-2 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-all text-gray-600 font-semibold text-sm">
                                <input type="radio" name="payment_method" value="tarjeta" class="peer sr-only">
                                <svg class="w-5 h-5 text-gray-400 peer-checked:text-gray-800 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span class="peer-checked:text-gray-800 transition-colors">Tarjeta</span>
                            </label>
                        </div>

                        <!-- Detalles para Efectivo -->
                        <div id="cash-details" class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Efectivo Recibido</label>
                                <input type="number" id="amount_received" name="amount_received" step="0.01" min="0" placeholder="0.00" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 bg-white" required>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                <span class="text-xs font-medium text-gray-500">Cambio a Devolver:</span>
                                <span id="change_amount" class="font-bold text-emerald-600">$0.00</span>
                            </div>
                        </div>

                        <!-- Detalles para Tarjeta -->
                        <div id="card-details" class="hidden bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Últimos 4 dígitos de la tarjeta</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    </div>
                                    <input type="text" id="card_digits" name="card_digits" placeholder="Ej. 4589" maxlength="4" pattern="\d{4}" class="w-full pl-9 border-gray-200 rounded-lg text-sm focus:ring-gray-800 focus:border-gray-800 bg-white">
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400">Requerido para control de caja.</p>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="bg-gray-50 rounded-xl p-4 flex justify-between items-center border border-gray-100">
                        <div>
                            <span class="block text-[11px] font-bold text-gray-400 uppercase">Total a cobrar:</span>
                            <span id="renew-summary-total" class="text-xl font-extrabold text-gray-800">$0.00 <span class="text-xs font-bold text-gray-400">MXN</span></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[11px] font-bold text-gray-400 uppercase">Nueva vigencia:</span>
                            <span id="renew-summary-date" class="text-sm font-bold text-emerald-600 flex items-center gap-1 justify-end">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                -- / -- / ----
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" id="btn-cancel-renew" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors px-4 py-2 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Registrar Pago y Renovar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT DE LÓGICA DEL MODAL POS             -->
    <!-- ========================================== -->
    <script>
        let baseDateTimestamp = Date.now();

        function abrirModalRenovacion(el) {
            const id = el.dataset.id;
            const nombre = el.dataset.nombre;
            const folio = el.dataset.folio;
            const expirationDate = el.dataset.exp;
            const planId = el.dataset.plan || 'null';

            const modal = document.getElementById('renew-modal');
            const modalContent = document.getElementById('renew-modal-content');
            const form = document.getElementById('renew-form');

            if(form) form.action = `/socios/${id}/renovar`;

            // Se arma con textContent / nodos para que el nombre nunca se interprete como HTML.
            const nameSpan = document.getElementById('renew-socio-name');
            if(nameSpan) {
                nameSpan.textContent = nombre + ' ';
                const folioSpan = document.createElement('span');
                folioSpan.className = 'text-gray-400 font-semibold';
                folioSpan.textContent = `(#${folio})`;
                nameSpan.appendChild(folioSpan);
            }

            if (expirationDate) {
                const exp = new Date(expirationDate + 'T00:00:00');
                baseDateTimestamp = exp > Date.now() ? exp.getTime() : Date.now();
            } else {
                baseDateTimestamp = Date.now();
            }

            let planRadio = document.querySelector(`input[name="plan_id"][value="${planId}"]`);
            if (!planRadio) planRadio = document.querySelector('input[name="plan_id"]');
            if (planRadio) planRadio.checked = true;

            if(modal && modalContent) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-95');
                }, 10);
            }

            // Forzar el cálculo al abrir
            if(typeof window.calculateRenewal === 'function') {
                window.calculateRenewal();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('renew-modal');
            const modalContent = document.getElementById('renew-modal-content');
            const btnClose = document.getElementById('btn-close-renew');
            const btnCancel = document.getElementById('btn-cancel-renew');

            function closeModal() {
                if(modal && modalContent) {
                    modal.classList.add('opacity-0');
                    modalContent.classList.add('scale-95');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        // Limpiar el campo de efectivo al cerrar
                        const amountInput = document.getElementById('amount_received');
                        if(amountInput) amountInput.value = '';
                    }, 300);
                }
            }

            if (btnClose) btnClose.addEventListener('click', closeModal);
            if (btnCancel) btnCancel.addEventListener('click', closeModal);
            if (modal) {
                modal.addEventListener('click', function(e) { if(e.target === modal) closeModal(); });
            }

            const radiosPlans = document.querySelectorAll('input[name="plan_id"]');
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            const summaryTotal = document.getElementById('renew-summary-total');
            const summaryDate = document.getElementById('renew-summary-date');
            
            const cashDetails = document.getElementById('cash-details');
            const cardDetails = document.getElementById('card-details');
            const amountInput = document.getElementById('amount_received');
            const changeAmount = document.getElementById('change_amount');
            const cardDigits = document.getElementById('card_digits');

            window.calculateRenewal = function() {
                const selectedPlan = document.querySelector('input[name="plan_id"]:checked');
                if(!selectedPlan) return;

                document.querySelectorAll('.renew-plan-card').forEach(card => {
                    card.classList.remove('border-orange-500', 'bg-orange-50/30');
                });
                selectedPlan.closest('.renew-plan-card').classList.add('border-orange-500', 'bg-orange-50/30');

                const price = parseFloat(selectedPlan.dataset.price) || 0;
                if(summaryTotal) {
                    summaryTotal.innerHTML = `$${price.toLocaleString('es-MX', {minimumFractionDigits: 2})} <span class="text-xs font-bold text-gray-400">MXN</span>`;
                }

                const days = parseInt(selectedPlan.dataset.days) || 30;
                const newDate = new Date(baseDateTimestamp);
                newDate.setDate(newDate.getDate() + days);
                
                if(summaryDate) {
                    const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
                    summaryDate.innerHTML = `
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        ${newDate.toLocaleDateString('es-MX', dateOptions)}
                    `;
                }

                // Disparar evento de input para recalcular el cambio
                if(amountInput) amountInput.dispatchEvent(new Event('input'));
            }

            radiosPlans.forEach(radio => {
                radio.addEventListener('change', window.calculateRenewal);
            });

            if(amountInput) {
                amountInput.addEventListener('input', function() {
                    const selectedPlan = document.querySelector('input[name="plan_id"]:checked');
                    const price = selectedPlan ? parseFloat(selectedPlan.dataset.price) : 0;
                    const received = parseFloat(this.value) || 0;
                    let change = received - price;
                    
                    if (change < 0) change = 0;
                    if(changeAmount) changeAmount.innerHTML = `$${change.toFixed(2)}`;
                });
            }

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.payment-card').forEach(card => {
                        card.classList.remove('border-orange-500', 'bg-orange-50/10', 'border-gray-800', 'bg-gray-50');
                    });
                    
                    if(this.checked) {
                        if(this.value === 'efectivo') {
                            this.closest('.payment-card').classList.add('border-orange-500', 'bg-orange-50/10');
                            if(cashDetails) cashDetails.classList.remove('hidden');
                            if(cardDetails) cardDetails.classList.add('hidden');
                            if(amountInput) amountInput.required = true;
                            if(cardDigits) {
                                cardDigits.required = false;
                                cardDigits.value = '';
                            }
                        } else {
                            this.closest('.payment-card').classList.add('border-gray-800', 'bg-gray-50');
                            if(cashDetails) cashDetails.classList.add('hidden');
                            if(cardDetails) cardDetails.classList.remove('hidden');
                            if(amountInput) {
                                amountInput.required = false;
                                amountInput.value = '';
                                if(changeAmount) changeAmount.innerHTML = '$0.00';
                            }
                            if(cardDigits) cardDigits.required = true;
                        }
                    }
                });
            });

            // Disparar configuración inicial
            setTimeout(() => {
                const checkedPayment = document.querySelector('input[name="payment_method"]:checked');
                if (checkedPayment) checkedPayment.dispatchEvent(new Event('change'));
            }, 50);
        });
    </script>
<!-- ==========================================
         SISTEMA DE IMPRESIÓN AUTOMÁTICA DE TICKET
         ========================================== -->
    @if(session('imprimir_ticket'))
        <!-- Cargamos el ticket en un mini navegador invisible -->
        <iframe id="frameImpresion" src="{{ session('imprimir_ticket') }}" class="hidden" style="display: none;"></iframe>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const iframe = document.getElementById('frameImpresion');
                
                // Esperamos a que el diseño del ticket cargue por completo en el fondo
                iframe.onload = function() {
                    // Disparamos la ventana de impresión enfocando solo el ticket
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                };
            });
        </script>
    @endif
</x-app-layout>