<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{ tab: 'ingresos' }">
        
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Corte y Finanzas</h1>
                <p class="text-sm text-gray-500 mt-0.5">Balance diario de ingresos, gastos y utilidad neta.</p>
            </div>

            <!-- Selector de Fecha y Botón Exportar -->
            <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-3">
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 shadow-sm">
                    <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <input type="date" name="fecha" value="{{ $fechaSeleccionada }}" onchange="this.form.submit()" 
                           class="bg-transparent border-none text-xs font-bold text-gray-700 focus:ring-0 p-0 cursor-pointer">
                </div>

                <a href="{{ route('reports.export', ['fecha' => $fechaSeleccionada]) }}" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Exportar PDF
                </a>
            </form>
        </div>

        <!-- Tarjetas de Resumen Financiero (4 Columnas) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- 1. Total Ingresos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Ingresos Brutos</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-gray-800">${{ number_format($totalIngresos, 2) }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-50 flex flex-col text-[10px] text-gray-400 gap-1">
                    <span>Efectivo: <b class="text-gray-600">${{ number_format($efectivoIngresos, 2) }}</b></span>
                    <span>Tarjeta: <b class="text-gray-600">${{ number_format($tarjetaIngresos, 2) }}</b></span>
                </div>
            </div>

            <!-- 2. Total Egresos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-bl-full -z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Gastos y Compras</span>
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-red-600">-${{ number_format($totalEgresos, 2) }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-50 text-[10px] text-gray-400">
                    Dinero extraído de la caja o banco.
                </div>
            </div>

            <!-- 3. Balance Neto (Utilidad) -->
            <div class="bg-slate-900 rounded-2xl p-6 shadow-lg border border-slate-800 relative overflow-hidden flex flex-col justify-between text-white">
                <div class="absolute top-0 right-0 w-24 h-24 bg-slate-800 rounded-bl-full -z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Utilidad Neta</span>
                        <div class="w-8 h-8 rounded-lg bg-slate-800 text-emerald-400 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold {{ $balanceNeto >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                        ${{ number_format($balanceNeto, 2) }}
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-700/50 text-[10px] text-slate-400">
                    Lo que realmente queda en el negocio.
                </div>
            </div>

            <!-- 4. Alerta de Vencimientos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-full -z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[10px] font-bold tracking-wider text-gray-400 uppercase">Renovaciones</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-gray-800">{{ $renovacionesPendientesCount }} <span class="text-xs font-bold text-gray-500">socios</span></div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-50 text-[10px] text-amber-600 font-bold">
                    Vencen en los próximos 5 días
                </div>
            </div>

        </div>

        <!-- TABLAS DIVIDIDAS (INGRESOS VS EGRESOS) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            
            <!-- Pestañas -->
            <div class="flex border-b border-gray-100 bg-gray-50/50 px-2 pt-2">
                <button @click="tab = 'ingresos'" 
                    :class="tab === 'ingresos' ? 'border-emerald-500 text-emerald-700 bg-white shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" 
                    class="px-6 py-3 border-b-2 font-bold text-xs uppercase tracking-wider transition-all rounded-t-lg">
                    Entradas de Dinero
                </button>
                <button @click="tab = 'egresos'" 
                    :class="tab === 'egresos' ? 'border-red-500 text-red-700 bg-white shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'" 
                    class="px-6 py-3 border-b-2 font-bold text-xs uppercase tracking-wider transition-all rounded-t-lg">
                    Salidas (Gastos)
                </button>
            </div>

            <!-- TABLA 1: INGRESOS -->
            <div x-show="tab === 'ingresos'" class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px] bg-white">
                                <th class="py-4 px-6">Hora</th>
                                <th class="py-4 px-6">Concepto / ID</th>
                                <th class="py-4 px-6">Tipo</th>
                                <th class="py-4 px-6">Método de Pago</th>
                                <th class="py-4 px-6 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($transacciones as $tx)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 px-6 font-medium text-gray-600">{{ $tx->created_at->format('h:i A') }}</td>
                                    <td class="py-4 px-6 font-bold text-gray-800">{{ $tx->concepto_id ?? '#' . $tx->id }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-1 rounded-md font-bold text-[9px] uppercase tracking-wide bg-slate-100 text-slate-600">
                                            {{ $tx->origen ?? 'Venta' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-lg font-bold text-[10px] {{ ($tx->payment_method ?? 'Efectivo') == 'Efectivo' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $tx->payment_method ?? 'Efectivo' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right font-extrabold text-emerald-600 text-sm">+ ${{ number_format($tx->monto, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="font-bold">Sin ingresos</p>
                                        <p class="text-[10px] mt-1">No hay ventas ni pagos registrados hoy.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABLA 2: EGRESOS -->
            <div x-show="tab === 'egresos'" class="p-0" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px] bg-white">
                                <th class="py-4 px-6">Hora</th>
                                <th class="py-4 px-6">Proveedor / Concepto</th>
                                <th class="py-4 px-6">Descripción</th>
                                <th class="py-4 px-6">Método de Pago</th>
                                <th class="py-4 px-6 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($egresos as $egreso)
                                <tr class="hover:bg-red-50/30 transition-colors">
                                    <td class="py-4 px-6 font-medium text-gray-600">{{ $egreso->created_at->format('h:i A') }}</td>
                                    <td class="py-4 px-6">
                                        @if($egreso->type === 'compra_proveedor')
                                            <span class="block font-bold text-blue-600">{{ $egreso->supplier->name ?? 'Compra General' }}</span>
                                        @else
                                            <span class="block font-bold text-orange-600">{{ $egreso->concept }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 truncate max-w-xs">{{ $egreso->description }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-lg font-bold text-[10px] bg-gray-100 text-gray-600">
                                            {{ $egreso->payment_method }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right font-extrabold text-red-500 text-sm">- ${{ number_format($egreso->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                                        <p class="font-bold">Día limpio</p>
                                        <p class="text-[10px] mt-1">No se ha registrado ninguna salida de dinero hoy.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>