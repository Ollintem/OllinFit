<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{ openModalApertura: false, openModalCierre: false }">

        <!-- Encabezado -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Flujo de Caja</h1>
                <p class="text-sm text-gray-500 mt-0.5">Control de apertura, efectivo en gaveta y cierre de turno.</p>
            </div>

            <div>
                @if (!$cajaActiva)
                    <button @click="openModalApertura = true"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 px-5 rounded-xl transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Abrir Caja Turno
                    </button>
                @else
                    <button @click="openModalCierre = true"
                        class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2.5 px-5 rounded-xl transition-colors shadow-sm flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Realizar Corte y Cerrar Caja
                    </button>
                @endif
            </div>
        </div>
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition.duration.500ms
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif


        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs font-bold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Estado Actual de la Caja -->
        @if ($cajaActiva)
        <!-- Tarjetas de Resumen (Estado, Fondo, Ingresos, Tarjetas) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <span class="text-xs font-bold text-gray-400 uppercase">Estado Actual</span>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span> ABIERTA
                </div>
                <p class="text-[11px] text-gray-400 mt-2">Desde: {{ \Carbon\Carbon::parse($cajaActiva->opened_at)->format('d/m/Y h:i A') }}</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <span class="text-xs font-bold text-gray-400 uppercase">Fondo Inicial (Cambio)</span>
                <div class="text-2xl font-extrabold text-gray-800 mt-2">
                    ${{ number_format($cajaActiva->opening_amount, 2) }} <span class="text-xs text-gray-400">MXN</span>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">Dinero base en gaveta</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <span class="text-xs font-bold text-gray-400 uppercase">Ingresos Efectivo (Turno)</span>
                <div class="text-2xl font-extrabold text-emerald-600 mt-2">
                    ${{ number_format($ingresosEfectivoTurno, 2) }} <span class="text-xs text-gray-400">MXN</span>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">{{ $ventasTurnoCount }} transacciones en efectivo</p>
            </div>

            @php
                // Calculamos los gastos con tarjeta 
                $egresosTarjeta = $gastosTurno->where('payment_method', '!=', 'Efectivo')->sum('amount');
                $balanceTarjeta = ($ingresosTarjetaTurno ?? 0) - $egresosTarjeta;
            @endphp

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase">Flujo Electrónico (Tarjetas)</span>
                <div class="text-2xl font-extrabold text-blue-600 mt-2">
                    ${{ number_format($balanceTarjeta, 2) }} <span class="text-xs text-gray-400 font-medium">MXN</span>
                </div>
                
                <!-- Mini desglose para el dinero de banco -->
                <div class="flex justify-between items-center text-[10px] text-gray-500 mt-3 pt-3 border-t border-gray-50">
                    <span class="flex items-center gap-1 font-bold" title="Ingresos electrónicos">
                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                        ${{ number_format($ingresosTarjetaTurno ?? 0, 2) }}
                    </span>
                    <span class="flex items-center gap-1 font-bold" title="Salidas electrónicas">
                        <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                        -${{ number_format($egresosTarjeta, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- SECCIÓN INTERMEDIA CORREGIDA: Tarjeta Negra + Tabla de Entradas con scroll -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 mt-6 items-start"> <!-- Añadido items-start para evitar que la tarjeta negra se estire -->
            
            <!-- COLUMNA IZQ: TARJETA NEGRA (Ocupa 1 espacio - Compacta y sin estirarse) -->
            <div class="lg:col-span-1">
                <div class="bg-gray-900 text-white rounded-2xl p-6 shadow-xl flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Efectivo Esperado en Caja</span>
                        <div class="text-4xl font-extrabold text-emerald-400 mt-3">
                            ${{ number_format($expectedAmount, 2) }}
                        </div>
                        
                        @if($expectedAmount < 300)
                            <div class="text-[10px] text-orange-400 font-bold mt-1 bg-orange-400/10 inline-block px-2 py-1 rounded">
                                ⚠️ Fondo bajo para dar cambio
                            </div>
                        @endif

                        <!-- Desglose -->
                        <div class="mt-6 pt-6 border-t border-gray-800 space-y-3 text-sm text-gray-400">
                            <div class="flex justify-between items-center">
                                <span>Fondo Inicial:</span> 
                                <span class="text-white font-medium">+ ${{ number_format($cajaActiva->opening_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Entradas:</span> 
                                <span class="text-emerald-400 font-medium">+ ${{ number_format($ingresosEfectivoTurno, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Salidas:</span> 
                                <span class="text-red-400 font-medium">- ${{ number_format($egresosEfectivo, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DER: TABLA DE ENTRADAS CON FILTROS Y SCROLL (Ocupa 2 espacios) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between" x-data="{ filtro: 'todos' }">
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                            <div>
                                <h2 class="text-base font-bold text-gray-800 mb-1">Entradas del Turno Actual</h2>
                                <p class="text-xs text-gray-400">Todos los cobros registrados en este turno.</p>
                            </div>
                            
                            <!-- Botones de Filtro Interactivos -->
                            <div class="flex bg-gray-100 p-1 rounded-xl shadow-inner">
                                <button @click="filtro = 'todos'" :class="filtro === 'todos' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all">Todos</button>
                                <button @click="filtro = 'Efectivo'" :class="filtro === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all flex items-center gap-1">💵 Efectivo</button>
                                <button @click="filtro = 'Tarjeta'" :class="filtro === 'Tarjeta' ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all flex items-center gap-1">💳 Tarjeta</button>
                            </div>
                        </div>
                        
                        <!-- Contenedor con altura máxima y scrollbar para evitar que la página crezca infinitamente -->
                        <div class="overflow-y-auto max-h-96 pr-1 custom-scrollbar">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="sticky top-0 bg-white z-10"> <!-- El encabezado se queda arriba al hacer scroll -->
                                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                                        <th class="py-3 px-4">Hora</th>
                                        <th class="py-3 px-4">Concepto / ID</th>
                                        <th class="py-3 px-4">Tipo</th>
                                        <th class="py-3 px-4 text-right">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($detalleTransacciones as $tx)
                                        <tr class="hover:bg-gray-50/50 transition-colors" 
                                            x-show="filtro === 'todos' || filtro === '{{ $tx->origen === 'Efectivo' ? 'Efectivo' : 'Tarjeta' }}'">
                                            
                                            <td class="py-3 px-4 font-medium text-gray-600">
                                                {{ \Carbon\Carbon::parse($tx->created_at)->format('h:i A') }}
                                            </td>
                                            <td class="py-3 px-4 font-bold text-gray-800">{{ $tx->concepto_id }}</td>
                                            <td class="py-3 px-4">
                                                @if($tx->origen === 'Efectivo')
                                                    <span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-100">💵 Efectivo</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded font-semibold text-[10px] bg-blue-50 text-blue-700 border border-blue-100">💳 {{ $tx->origen }}</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right font-extrabold {{ $tx->origen === 'Efectivo' ? 'text-emerald-600' : 'text-blue-600' }}">
                                                ${{ number_format($tx->monto, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-gray-400">Aún no hay cobros registrados en este turno.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- TABLA DE SALIDAS Y EGRESOS CON FILTROS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6 w-full" x-data="{ filtroEgresos: 'todos' }">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Salidas y Egresos (Turno Actual)
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Listado de todas las compras y gastos registrados desde la apertura.</p>
                </div>
                
                <!-- Botones de Filtro -->
                <div class="flex bg-gray-200/50 p-1 rounded-xl shadow-inner">
                    <button @click="filtroEgresos = 'todos'" :class="filtroEgresos === 'todos' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all">Todos</button>
                    <button @click="filtroEgresos = 'Efectivo'" :class="filtroEgresos === 'Efectivo' ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all flex items-center gap-1">💵 Efectivo</button>
                    <button @click="filtroEgresos = 'Tarjeta'" :class="filtroEgresos === 'Tarjeta' ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 text-[10px] font-bold rounded-lg transition-all flex items-center gap-1">💳 Tarjeta</button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-white border-b border-gray-100">
                            <th class="px-6 py-3">Hora</th>
                            <th class="px-6 py-3">Concepto</th>
                            <th class="px-6 py-3">Responsable</th>
                            <th class="px-6 py-3">Método</th>
                            <th class="px-6 py-3 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <!-- IMPORTANTE: El ciclo debe llamar a todos los gastos -->
                        @forelse($gastosTurno as $gasto)
                            <!-- Lógica para ocultar la fila si no coincide con el filtro -->
                            <tr class="hover:bg-gray-50 transition-colors"
                                x-show="filtroEgresos === 'todos' || filtroEgresos === '{{ $gasto->payment_method === 'Efectivo' ? 'Efectivo' : 'Tarjeta' }}'">
                                
                                <td class="px-6 py-3 text-gray-500 font-medium">{{ $gasto->created_at->format('h:i A') }}</td>
                                <td class="px-6 py-3 font-bold text-gray-800">
                                    {{ $gasto->type === 'compra_proveedor' ? 'Compra de Inventario: ' . ($gasto->supplier->name ?? 'General') : $gasto->concept }}
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full bg-zinc-800 text-white flex items-center justify-center text-[9px] font-bold">
                                            {{ substr($gasto->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        {{ $gasto->user->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    @if($gasto->payment_method === 'Efectivo')
                                        <span class="text-[10px] font-bold bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-md border border-emerald-200">💵 Efectivo</span>
                                    @else
                                        <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md border border-blue-200">💳 {{ $gasto->payment_method }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right font-extrabold" :class="'{{ $gasto->payment_method }}' === 'Efectivo' ? 'text-red-500' : 'text-blue-500'">
                                    - ${{ number_format($gasto->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
                                    No se han registrado salidas ni compras en este turno.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

                @else <!-- Esta era tu línea 142 que se recorrió hacia abajo -->
                    <!-- PANTALLA DE CAJA CERRADA ... -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Columna Izquierda: Aviso y Botón de Abrir -->
                <div class="bg-white rounded-2xl p-10 text-center border border-gray-100 shadow-sm space-y-4 flex flex-col justify-center items-center h-full">
                    <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">🔒</div>
                    <h2 class="text-xl font-bold text-gray-800">La caja se encuentra cerrada</h2>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto">Para comenzar a registrar cobros y llevar el control diario, es necesario abrir un nuevo turno indicando el fondo inicial.</p>
                    <button @click="openModalApertura = true" class="bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md transition-colors cursor-pointer mt-2">
                        Abrir Caja Ahora
                    </button>
                </div>

                <!-- Columna Derecha: Resumen del Último Turno -->
                <div class="bg-slate-900 rounded-2xl p-8 border border-slate-800 shadow-sm text-white flex flex-col justify-center h-full relative overflow-hidden">
                    <!-- Detalle visual de fondo -->
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                    
                    <h3 class="text-sm font-bold text-slate-300 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        Resumen del Último Turno
                    </h3>
                    
                    @if($ultimoTurno)
                        <div class="space-y-6 relative z-10">
                            <div>
                                <p class="text-[11px] text-slate-400 uppercase font-semibold mb-1">Cajero Anterior</p>
                                <p class="text-base font-bold text-white">{{ $ultimoTurno->user->name ?? 'Usuario Desconocido' }}</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[11px] text-slate-400 uppercase font-semibold mb-1">Cierre</p>
                                    <p class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($ultimoTurno->closed_at)->format('d/m/Y h:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] text-slate-400 uppercase font-semibold mb-1">Efectivo Entregado</p>
                                    <p class="text-sm font-bold text-emerald-400">${{ number_format($ultimoTurno->closing_amount, 2) }}</p>
                                </div>
                            </div>

                            <div class="pt-5 border-t border-slate-700/50">
                                <p class="text-[11px] text-slate-400 uppercase font-semibold mb-2">Cuadre de Caja</p>
                                @if($ultimoTurno->difference == 0)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Caja cuadrada perfectamente
                                    </div>
                                @elseif($ultimoTurno->difference > 0)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Sobrante: ${{ number_format($ultimoTurno->difference, 2) }}
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Faltante: ${{ number_format(abs($ultimoTurno->difference), 2) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="py-8 text-center text-slate-500 text-xs relative z-10">
                            No hay registros de turnos anteriores en el sistema.
                        </div>
                    @endif
                </div>
            </div>
        @endif
        <!-- MODAL APERTURA DE CAJA -->
        <div x-show="openModalApertura"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            style="display: none;">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 space-y-4">
                <h3 class="font-bold text-gray-800 text-base">Apertura de Turno / Caja</h3>
                <p class="text-xs text-gray-500">Ingresa la cantidad de efectivo inicial (cambio o fondo fijo) con la
                    que inicia la gaveta.</p>

                <form action="{{ route('caja.open') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Fondo Inicial (MXN)</label>
                        <input type="number" step="0.01" name="opening_amount" required placeholder="0.00"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="openModalApertura = false"
                            class="px-4 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-xl">Cancelar</button>
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm">Abrir
                            Caja</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL CIERRE DE CAJA (CORTE) -->
        @if ($cajaActiva)
            <div x-show="openModalCierre"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                style="display: none;">
                <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 space-y-4">
                    <h3 class="font-bold text-gray-800 text-base">Corte de Caja y Cierre de Turno</h3>
                    <p class="text-xs text-gray-500">Cuenta físicamente el dinero en efectivo que tienes en la gaveta e
                        ingrésalo a continuación para calcular diferencias.</p>

                    <!-- Formulario con Alpine.js para calcular en vivo -->
            <form action="{{ route('caja.close') }}" method="POST" 
                  x-data="{ 
                      efectivoContado: '', 
                      esperado: {{ $expectedAmount }},
                      get diferencia() {
                          if (this.efectivoContado === '') return null;
                          return (parseFloat(this.efectivoContado) - this.esperado).toFixed(2);
                      }
                  }" class="space-y-4">
                @csrf
                
                <!-- Desglose Matemático Actualizado -->
                <div class="space-y-2 text-sm bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Fondo Inicial:</span>
                        <span class="font-medium">${{ number_format($cajaActiva->opening_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Ingresos Efectivo:</span>
                        <span class="text-emerald-600 font-medium">+ ${{ number_format($ingresosEfectivoTurno, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Gastos y Salidas:</span>
                        <span class="text-rose-500 font-medium">- ${{ number_format($egresosEfectivo, 2) }}</span>
                    </div>
                    <div class="pt-2 mt-2 border-t border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-gray-800">Total Esperado:</span>
                        <span class="font-extrabold text-lg text-gray-900">${{ number_format($expectedAmount, 2) }}</span>
                    </div>
                </div>

                <!-- Input del Cajero -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Efectivo Físico Contado (MXN)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 font-bold">$</span>
                        </div>
                        <input type="number" step="0.01" name="closing_amount" x-model="efectivoContado" required placeholder="0.00" class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">Ingresa exactamente cuánto dinero en billetes y monedas tienes físicamente.</p>
                </div>

                <!-- ALERTA DE DESCUADRE EN VIVO -->
                <div x-show="diferencia !== null" x-transition class="p-3 rounded-xl border" 
                     :class="{
                         'bg-emerald-50 border-emerald-200': diferencia == 0,
                         'bg-rose-50 border-rose-200': diferencia < 0,
                         'bg-blue-50 border-blue-200': diferencia > 0
                     }" style="display: none;">
                    
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold"
                              :class="{
                                 'text-emerald-700': diferencia == 0,
                                 'text-rose-700': diferencia < 0,
                                 'text-blue-700': diferencia > 0
                              }">
                            <span x-show="diferencia == 0">✅ Corte Perfecto</span>
                            <span x-show="diferencia < 0">⚠️ Faltante detectado</span>
                            <span x-show="diferencia > 0">ℹ️ Sobrante detectado</span>
                        </span>
                        
                        <span x-show="diferencia != 0" class="text-base font-extrabold"
                              :class="diferencia < 0 ? 'text-rose-600' : 'text-blue-600'">
                            <span x-show="diferencia > 0">+</span>$<span x-text="Math.abs(diferencia).toFixed(2)"></span>
                        </span>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="openModalCierre = false" class="px-4 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all">
                        Confirmar Corte y Cerrar
                    </button>
                </div>
            </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
