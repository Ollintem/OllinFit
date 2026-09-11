<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4 mb-4 border-b border-gray-200 pb-4">
            <a href="{{ route('socios.show', $member->id) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Historial de Accesos</h1>
                <p class="text-sm text-gray-500">Socio: {{ $member->name }} {{ $member->last_name }} (#{{ $member->folio }})</p>
            </div>
        </div>

        <!-- Tabla Completa -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                            <th class="px-6 py-4">Fecha y Hora</th>
                            <th class="px-6 py-4">Método de Acceso</th>
                            <th class="px-6 py-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @forelse($accesos as $acceso)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="block font-medium text-gray-800">{{ $acceso->created_at->format('d/M/Y - h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                <!-- Si tienes una columna method en tu tabla AccessLog úsala, si no, lo dejamos estático por ahora -->
                                {{ $acceso->access_method ?? 'Escáner QR' }}
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
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">No hay accesos registrados para este socio todavía.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación de Laravel -->
            <div class="p-4 border-t border-gray-100 bg-gray-50/30">
                {{ $accesos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>