<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Reportes y Finanzas</h1>
                <p class="text-sm text-gray-500 mt-0.5">Consulta de ingresos, transacciones y corte diario.</p>
            </div>

            <!-- Selector de Fecha y Botón Exportar -->
            <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-3">
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 shadow-sm">
                    <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <input type="date" name="fecha" value="{{ $fechaSeleccionada }}" onchange="this.form.submit()" 
                           class="bg-transparent border-none text-xs font-bold text-gray-700 focus:ring-0 p-0 cursor-pointer">
                </div>

                <a href="{{ route('reports.export', ['fecha' => $fechaSeleccionada]) }}"  class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Exportar PDF
                </a>
            </form>
        </div>

        <!-- Tarjetas de Resumen Financiero -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Total Ingresos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Total Ingresos</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-800">${{ number_format($totalIngresos, 2) }} <span class="text-xs font-normal text-gray-400">MXN</span></div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-50 flex items-center justify-between text-xs">
                    <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full">Corte Cuadrado</span>
                    <span class="text-gray-400">Efectivo: ${{ number_format($efectivo, 2) }} | Tarjeta: ${{ number_format($tarjeta, 2) }}</span>
                </div>
            </div>

            <!-- Ventas Realizadas -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Transacciones del Día</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-800">{{ $totalVentas }} <span class="text-sm font-bold text-gray-500">tickets</span></div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-50 text-xs text-gray-400">
                    Registros correspondientes a la fecha seleccionada.
                </div>
            </div>

            <!-- Alerta de Vencimientos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-full -z-0"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold tracking-wider text-gray-400 uppercase">Renovaciones Pendientes</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">!</div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-800">{{ $renovacionesPendientesCount }} <span class="text-sm font-bold text-gray-500">socios</span></div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-50 text-xs text-amber-600 font-medium">
                    Vencen en próximos 5 días
                </div>
            </div>

        </div>

        <!-- Historial de Pagos / Transacciones unificadas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-800 mb-1">Historial de Ingresos</h2>
            <p class="text-xs text-gray-400 mb-6">Transacciones de tienda y membresías registradas en la fecha: {{ $fechaSeleccionada }}</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3 px-4">Hora</th>
                            <th class="py-3 px-4">Concepto / ID</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Método de Pago</th>
                            <th class="py-3 px-4 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transacciones as $tx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-gray-600">{{ $tx->created_at->format('h:i A') }}</td>
                                <td class="py-3.5 px-4 font-bold text-gray-800">{{ $tx->concepto_id ?? '#' . $tx->id }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-md font-semibold text-[10px] bg-slate-100 text-slate-700">
                                        {{ $tx->origen ?? 'Venta' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-lg font-bold text-[10px] {{ ($tx->payment_method ?? 'Efectivo') == 'Efectivo' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                        {{ $tx->payment_method ?? 'Efectivo' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-emerald-600 text-sm">${{ number_format($tx->monto, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">
                                    No hay ingresos registrados para esta fecha.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @if(isset($imprimir) && $imprimir)
<script>
    window.addEventListener('DOMContentLoaded', () => {
        window.print();
    });
</script>
@endif
</x-app-layout>