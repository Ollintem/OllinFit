<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Encabezado -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Historial de Cortes de Caja</h1>
                <p class="text-sm text-gray-500 mt-0.5">Auditoría, turnos cerrados y conciliación histórica de efectivo.</p>
            </div>
            <a href="{{ route('caja.index') }}" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                ← Volver al Flujo de Caja Actual
            </a>
        </div>

        <!-- Tabla de Historial -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
            <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b border-gray-100 pb-3">Registros de Cierres de Turno</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100 pb-2">
                            <th class="pb-3 font-bold">Cierre / Fecha</th>
                            <th class="pb-3 font-bold">Fondo Inicial</th>
                            <th class="pb-3 font-bold">Esperado Sistema</th>
                            <th class="pb-3 font-bold">Efectivo Contado</th>
                            <th class="pb-3 font-bold">Diferencia</th>
                            <th class="pb-3 font-bold text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($historialCajas ?? [] as $caja)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($caja->closed_at)->format('d/m/Y h:i A') }}
                                </td>
                                <td class="py-3.5 text-gray-600">${{ number_format($caja->opening_amount, 2) }}</td>
                                <td class="py-3.5 text-gray-600">${{ number_format($caja->expected_amount, 2) }}</td>
                                <td class="py-3.5 font-bold text-emerald-600">${{ number_format($caja->closing_amount, 2) }}</td>
                                <td class="py-3.5 font-extrabold {{ $caja->difference < 0 ? 'text-rose-600' : ($caja->difference > 0 ? 'text-blue-600' : 'text-gray-600') }}">
                                    {{ $caja->difference > 0 ? '+' : '' }}{{ number_format($caja->difference, 2) }}
                                </td>
                                <td class="py-3.5 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                        CERRADA
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-gray-400">No hay cortes de caja registrados en el historial.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if(isset($historialCajas) && method_exists($historialCajas, 'links'))
                <div class="pt-4 border-t border-gray-100">
                    {{ $historialCajas->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>