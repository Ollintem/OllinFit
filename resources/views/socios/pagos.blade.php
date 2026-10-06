<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4 mb-4 border-b border-gray-200 pb-4">
            <a href="{{ route('socios.show', $member->id) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Historial de Pagos</h1>
                <p class="text-sm text-gray-500">Socio: {{ $member->name }} {{ $member->last_name }} (#{{ $member->folio }})</p>
            </div>
        </div>

        <!-- Tabla Completa -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                                        <th class="px-6 py-4">Fecha / Folio</th>
                                        <th class="px-6 py-4">Hora</th> <!-- Encabezado nuevo -->
                                        <th class="px-6 py-4">Concepto</th>
                                        <th class="px-6 py-4">Método</th>
                                        <th class="px-6 py-4 text-right">Importe</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 text-sm">
                                    @forelse($pagos as $pago)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        
                                        <!-- 1. Columna FECHA / FOLIO -->
                                        <td class="px-6 py-4">
                                            <span class="block font-medium text-gray-800">{{ $pago->created_at->format('d/M/Y') }}</span>
                                            <span class="block text-[11px] text-gray-400 mt-0.5">{{ $pago->folio_pago }}</span>
                                        </td>
                                        
                                        <!-- 2. Columna HORA (Nueva) -->
                                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $pago->created_at->format('h:i A') }}</td>
                                        
                                        <!-- 3. Columna CONCEPTO -->
                                        <td class="px-6 py-4 text-gray-600 font-medium">{{ $pago->plan_name }}</td>
                                        
                                        <!-- 4. Columna MÉTODO -->
                                        <td class="px-6 py-4 text-gray-500">
                                            @if($pago->payment_method === 'efectivo')
                                                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg> Efectivo</span>
                                            @else
                                                <span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> Tarjeta</span>
                                            @endif
                                        </td>
                                        
                                        <!-- 5. Columna IMPORTE -->
                                        <td class="px-6 py-4 text-right font-bold text-gray-800">
                                            ${{ number_format($pago->amount, 2) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <!-- 5 columnas para la tabla vacía -->
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No hay pagos registrados para este socio.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
            </div>
            
            <!-- Paginación de Laravel -->
            <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                {{ $pagos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>