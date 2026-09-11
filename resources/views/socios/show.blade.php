<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-2 border-b border-gray-200 pb-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('socios.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Perfil del Socio</h1>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- COLUMNA IZQUIERDA (Perfil y QR) -->
            <div class="space-y-6">
                
                <!-- Tarjeta de Perfil -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="h-24 bg-gray-900"></div>
                    
                    <!-- ¡AQUÍ ESTÁ LA MAGIA! Cambiamos el mt-14 por pt-16 directo en el contenedor padre -->
                    <div class="px-6 pt-16 pb-6 relative text-center">
                        
                        <!-- Foto de perfil superpuesta -->
                        <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                            <div class="w-24 h-24 rounded-full border-4 border-white bg-gray-200 overflow-hidden shadow-sm flex items-center justify-center">
                                @if($member->profile_photo_path)
                                    <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-2xl font-bold text-gray-500">{{ substr($member->name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Contenedor del texto (Ya no necesita margen) -->
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $member->name }} {{ $member->last_name }}</h2>
                            <p class="text-sm text-gray-500 mt-1">Folio: <span class="font-bold text-gray-700">#{{ $member->folio }}</span></p>
                            
                            <div class="mt-4">
                                @if($member->status === 'Activo')
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Membresía Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> {{ $member->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tarjeta del Gafete QR -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <h3 class="font-bold text-gray-800">Gafete Digital</h3>
                    </div>
                    <p class="text-[12px] text-gray-500 mb-6 px-4">Escanea este código en recepción para registrar la entrada rápida.</p>
                    
                    <!-- Código QR Generado -->
                    <div class="inline-block p-4 border-2 border-dashed border-gray-200 rounded-xl mb-6">
                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->margin(1)->generate($member->folio) !!}
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button class="flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Imprimir
                        </button>
                        <button class="flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Enviar
                        </button>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA (Plan e Historial) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Tarjeta del Plan Actual -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800">Plan Actual</h2>
                        <button id="btn-open-renew" type="button" class="bg-orange-50 text-orange-600 hover:bg-orange-100 px-4 py-2 rounded-lg text-xs font-bold transition-colors">
                            Renovar / Cambiar Plan
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                        <div>
                            <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo de Plan</span>
                            <span class="text-base font-bold text-gray-800">{{ $member->plan ? $member->plan->name : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fecha de Inicio</span>
                            <span class="text-sm font-medium text-gray-600">{{ $fechaInicio ? $fechaInicio->translatedFormat('d \d\e F, Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fecha de Vencimiento</span>
                            <span class="text-sm font-bold {{ $member->status === 'Activo' ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $member->expiration_date ? $member->expiration_date->translatedFormat('d \d\e F, Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Barra de Progreso -->
                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-500 mb-2">
                            <span>Progreso del plan</span>
                            <span class="font-bold text-gray-800">{{ $diasRestantes }} días restantes</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $porcentajeProgreso }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Historial (Pestañas dinámicas) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200">
                        <button id="tab-accesos" class="px-6 py-4 text-sm font-bold text-orange-500 border-b-2 border-orange-500 bg-white focus:outline-none transition-colors">Historial de Accesos</button>
                        <button id="tab-pagos" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors">Historial de Pagos</button>
                    </div>

                                    <div id="content-accesos" class="block">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                                        <th class="px-6 py-4">Fecha y Hora</th>
                                        <th class="px-6 py-4">Método de Acceso</th>
                                        <th class="px-6 py-4">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-sm">
                                    @forelse($ultimosAccesos as $acceso)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-800">{{ $acceso->created_at->format('d/M/Y - h:i A') }}</td>
                                        <td class="px-6 py-4 text-gray-500 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                            {{ $acceso->access_method }}
                                        </td>
                                                <td class="px-6 py-4">
                                        @if($acceso->status === 'Denegado')
                                            <span class="text-red-500 font-bold text-xs bg-red-50 px-2 py-1 rounded">Acceso Denegado</span>
                                        @elseif($acceso->status === 'Entrada')
                                            <span class="text-emerald-600 font-bold text-xs bg-emerald-50 px-2 py-1 rounded border border-emerald-100">Entrada</span>
                                        @elseif($acceso->status === 'Salida')
                                            <span class="text-blue-600 font-bold text-xs bg-blue-50 px-2 py-1 rounded border border-blue-100">Salida</span>
                                        @else
                                            <!-- Para los registros viejitos que decían "Permitido" -->
                                            <span class="text-gray-500 font-medium text-xs">{{ $acceso->status }}</span>
                                        @endif
                                    </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500 text-sm">No hay accesos registrados para este socio.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-4 text-center border-t border-gray-100">
                            <a href="{{ route('socios.accesos', $member->id) }}" class="text-sm font-medium text-orange-500 hover:text-orange-600">Ver historial completo de Accesos</a>
                        </div>
                    </div>
                    <!-- PESTAÑA 2: HISTORIAL DE PAGOS -->
                    <div id="content-pagos" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                                        <th class="px-6 py-4">Fecha / Folio</th>
                                        <th class="px-6 py-4">Concepto</th>
                                        <th class="px-6 py-4">Método</th>
                                        <th class="px-6 py-4 text-right">Importe</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-sm">
                                    @forelse($pagos as $pago)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="block font-medium text-gray-800">{{ $pago->created_at->format('d/M/Y - h:i A') }}</span>
                                            <span class="block text-[11px] text-gray-400 mt-0.5">{{ $pago->folio_pago }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">{{ $pago->plan_name }}</td>
                                        <td class="px-6 py-4 text-gray-500">
                                            @if($pago->payment_method === 'efectivo')
                                                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg> Efectivo</span>
                                            @else
                                                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> Tarjeta</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-800">
                                            ${{ number_format($pago->amount, 2) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">No hay pagos registrados para este socio.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Enlace exclusivo para Pagos -->
                        <div class="p-4 text-center border-t border-gray-100">
                            <a href="{{ route('socios.pagos', $member->id) }}" class="text-sm font-medium text-orange-500 hover:text-orange-600">Ver historial completo de Pagos</a>
                        </div>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
    <!-- MODAL DE RENOVACIÓN -->
    <div id="renew-modal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300" id="renew-modal-content">
            
            <form action="{{ route('socios.renew', $member->id) }}" method="POST">
                @csrf
                
                <!-- Header del Modal -->
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Renovar o Cambiar Plan
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Socio: <span class="font-bold text-gray-800">{{ $member->name }} {{ $member->last_name }} (#{{ $member->folio }})</span></p>
                    </div>
                    <button type="button" id="btn-close-renew" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 p-2 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body del Modal -->
                <div class="p-6 space-y-6">
                    
                    <!-- 1. Selección de Plan -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3">1. Selecciona el Plan</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($planes as $plan)
                            <label class="renew-plan-card relative p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 transition-all flex flex-col justify-between">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only" data-price="{{ $plan->price }}" data-days="{{ $plan->duration_days }}" required {{ $member->plan_id == $plan->id ? 'checked' : '' }}>
                                <div class="absolute top-3 right-3 w-3.5 h-3.5 rounded-full border border-gray-300 peer-checked:border-orange-500 peer-checked:border-[3.5px] transition-all bg-white"></div>
                                
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-bold text-gray-800 text-[13px] pr-5 leading-tight">{{ $plan->name }}</span>
                                    @if($plan->highlight_text)
                                        <span class="text-[9px] font-bold text-emerald-600 uppercase">{{ $plan->highlight_text }}</span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <span class="text-lg font-extrabold text-orange-500">${{ number_format($plan->price, 2) }}<span class="text-[10px] text-gray-400 font-bold ml-0.5">MXN</span></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        +{{ $plan->duration_days }} días
                                    </span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Método de Pago -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3">2. Método de Pago</label>
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
                    </div>
                    
                    <!-- Campos de Tarjeta (Ocultos por defecto) -->
                    <div id="card-details-form" class="hidden mt-4 space-y-4 border-t border-gray-100 pt-4">
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Datos de la Tarjeta</label>
                        
                        <div>
                            <input type="text" name="card_name" id="card_name" placeholder="Nombre en la tarjeta" class="w-full border-gray-200 rounded-lg text-sm focus:ring-gray-800 focus:border-gray-800">
                        </div>
                        <div>
                            <input type="text" name="card_number" id="card_number" placeholder="Número de la tarjeta (0000 0000 0000 0000)" maxlength="16" class="w-full border-gray-200 rounded-lg text-sm focus:ring-gray-800 focus:border-gray-800">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="card_expiry" id="card_expiry" placeholder="MM/AA" maxlength="5" class="w-full border-gray-200 rounded-lg text-sm focus:ring-gray-800 focus:border-gray-800">
                            <input type="text" name="card_cvc" id="card_cvc" placeholder="CVC" maxlength="4" class="w-full border-gray-200 rounded-lg text-sm focus:ring-gray-800 focus:border-gray-800">
                        </div>
                        <p class="text-[10px] text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Transacción encriptada y segura.
                        </p>
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
                    <button type="button" id="btn-cancel-renew" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors px-4 py-2">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Registrar Pago y Renovar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lógica para Abrir/Cerrar Modal
            const modal = document.getElementById('renew-modal');
            const modalContent = document.getElementById('renew-modal-content');
            const btnOpen = document.getElementById('btn-open-renew');
            const btnClose = document.getElementById('btn-close-renew');
            const btnCancel = document.getElementById('btn-cancel-renew');

            function openModal() {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-95');
                }, 10);
                calculateRenewal(); // Calcular el precio al abrir
            }

            function closeModal() {
                modal.classList.add('opacity-0');
                modalContent.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            if (btnOpen) btnOpen.addEventListener('click', openModal);
            if (btnClose) btnClose.addEventListener('click', closeModal);
            if (btnCancel) btnCancel.addEventListener('click', closeModal);

            // Cerrar modal si hacen clic fuera de la tarjeta blanca
            modal.addEventListener('click', function(e) {
                if(e.target === modal) closeModal();
            });

            // Lógica de Precios y Fechas
            const radiosPlans = document.querySelectorAll('input[name="plan_id"]');
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            const summaryTotal = document.getElementById('renew-summary-total');
            const summaryDate = document.getElementById('renew-summary-date');
            
            // Calculamos la fecha base desde PHP: Si está vigente, sumamos a su vigencia. Si está vencido, sumamos a HOY.
            const isFuture = {{ $member->expiration_date && $member->expiration_date->isFuture() ? 'true' : 'false' }};
            const baseDateTimestamp = {{ $member->expiration_date && $member->expiration_date->isFuture() ? $member->expiration_date->timestamp * 1000 : now()->timestamp * 1000 }};
            const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };

            function calculateRenewal() {
                const selectedPlan = document.querySelector('input[name="plan_id"]:checked');
                if(!selectedPlan) return;

                // Estilos visuales Tarjetas de Planes
                document.querySelectorAll('.renew-plan-card').forEach(card => {
                    card.classList.remove('border-orange-500', 'bg-orange-50/30');
                });
                selectedPlan.closest('.renew-plan-card').classList.add('border-orange-500', 'bg-orange-50/30');

                // Actualizar Precio
                const price = parseFloat(selectedPlan.dataset.price).toLocaleString('es-MX', {minimumFractionDigits: 2});
                summaryTotal.innerHTML = `$${price} <span class="text-xs font-bold text-gray-400">MXN</span>`;

                // Actualizar Fecha
                const days = parseInt(selectedPlan.dataset.days);
                const newDate = new Date(baseDateTimestamp);
                newDate.setDate(newDate.getDate() + days);
                
                summaryDate.innerHTML = `
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    ${newDate.toLocaleDateString('es-MX', dateOptions)}
                `;
            }

            // Escuchar cambios en los planes
            radiosPlans.forEach(radio => {
                radio.addEventListener('change', calculateRenewal);
            });

            // Estilos visuales y despliegue para los métodos de pago
            const cardForm = document.getElementById('card-details-form');
            const cardInputs = cardForm.querySelectorAll('input');

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Limpiar estilos de botones
                    document.querySelectorAll('.payment-card').forEach(card => {
                        card.classList.remove('border-orange-500', 'bg-orange-50/10', 'border-gray-800', 'bg-gray-50');
                    });
                    
                    if(this.checked) {
                        if(this.value === 'efectivo') {
                            this.closest('.payment-card').classList.add('border-orange-500', 'bg-orange-50/10');
                            // Ocultar formulario de tarjeta y quitar required
                            cardForm.classList.add('hidden');
                            cardInputs.forEach(input => input.required = false);
                        } else {
                            this.closest('.payment-card').classList.add('border-gray-800', 'bg-gray-50');
                            // Mostrar formulario de tarjeta y hacerlos required
                            cardForm.classList.remove('hidden');
                            cardInputs.forEach(input => input.required = true);
                        }
                    }
                });
            });

            // Dar estilo inicial al método de pago
            document.querySelector('input[name="payment_method"]:checked').dispatchEvent(new Event('change'));
        });
        // Lógica de Pestañas (Tabs)
            const tabAccesos = document.getElementById('tab-accesos');
            const tabPagos = document.getElementById('tab-pagos');
            const contentAccesos = document.getElementById('content-accesos');
            const contentPagos = document.getElementById('content-pagos');

            const activeTabClasses = ['text-orange-500', 'border-b-2', 'border-orange-500', 'font-bold'];
            const inactiveTabClasses = ['text-gray-500', 'font-medium', 'hover:text-gray-700'];

            tabAccesos.addEventListener('click', () => {
                contentAccesos.classList.remove('hidden');
                contentPagos.classList.add('hidden');
                tabAccesos.classList.add(...activeTabClasses);
                tabAccesos.classList.remove(...inactiveTabClasses);
                tabPagos.classList.remove(...activeTabClasses);
                tabPagos.classList.add(...inactiveTabClasses);
            });

            tabPagos.addEventListener('click', () => {
                contentPagos.classList.remove('hidden');
                contentAccesos.classList.add('hidden');
                tabPagos.classList.add(...activeTabClasses);
                tabPagos.classList.remove(...inactiveTabClasses);
                tabAccesos.classList.remove(...activeTabClasses);
                tabAccesos.classList.add(...inactiveTabClasses);
            });
    </script>
</x-app-layout>