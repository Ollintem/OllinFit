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
                        <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
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
                                <span class="block text-[11px] text-gray-400">{{ $pago->folio_pago }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">{{ $pago->plan_name }}</td>
                            <td class="px-6 py-4 text-gray-500 uppercase text-[11px] font-bold">{{ $pago->payment_method }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">${{ number_format($pago->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No hay pagos registrados.</td>
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