<x-app-layout>
    <!-- Contenedor principal con la lógica del Modal del Ticket -->
    <div x-data="{
            showTicketModal: false,
            ticketData: null,
            async loadTicket(id) {
                try {
                    const response = await fetch('/punto-de-venta/' + id + '/datos-ticket');
                    this.ticketData = await response.json();
                    this.showTicketModal = true;
                } catch (error) {
                    console.error('Error al cargar el ticket', error);
                }
            }
        }" 
        class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Encabezado de la página -->
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Historial de Ventas</h2>
                <p class="text-sm text-gray-500 mt-1">Consulta, filtra y reimprime tickets de transacciones pasadas.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Nueva Venta
            </a>
        </div>

        <!-- Tarjeta de Filtros -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('sales.history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                
                <!-- Búsqueda por Folio -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Folio</label>
                    <input type="text" name="folio" value="{{ request('folio') }}" placeholder="#MX-..." 
                        class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-2.5 transition-colors">
                </div>

                <!-- Método de Pago -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Método de Pago</label>
                    <select name="payment_method" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-2.5 transition-colors">
                        <option value="">Todos los métodos</option>
                        <option value="Efectivo" {{ request('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="Tarjeta" {{ request('payment_method') == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                        <option value="Transferencia" {{ request('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                        class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-2.5 transition-colors">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                        class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-2.5 transition-colors">
                </div>

                <!-- Botones de Acción -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm py-2.5 rounded-xl transition-colors shadow-sm">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['folio', 'payment_method', 'date_from', 'date_to']))
                        <a href="{{ route('sales.history') }}" class="px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl flex items-center justify-center transition-colors" title="Limpiar Filtros">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de Resultados -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Folio</th>
                            <th class="px-6 py-4 font-semibold">Fecha y Hora</th>
                            <th class="px-6 py-4 font-semibold">Método</th>
                            <th class="px-6 py-4 font-semibold">Total</th>
                            <th class="px-6 py-4 font-semibold text-center">Ticket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if(count($ventas) > 0)
                            @foreach($ventas as $venta)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800">#MX-{{ $venta->id }}</td>
                                    <td class="px-6 py-4 text-gray-500 font-medium">{{ $venta->created_at->format('d M, Y - h:i A') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-zinc-100 text-zinc-600 rounded-lg text-xs font-semibold border border-zinc-200">
                                            {{ $venta->payment_method ?? 'Efectivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-emerald-600 font-black">${{ number_format($venta->total, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button" @click="loadTicket({{ $venta->id }})" class="inline-flex items-center justify-center text-gray-400 hover:text-orange-500 hover:bg-orange-50 transition-colors p-2 rounded-xl" title="Ver Comprobante">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-sm font-medium">No se encontraron ventas con los filtros aplicados.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación de Laravel -->
            @if($ventas->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $ventas->links() }}
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- MODAL DEL TICKET (MISMO DISEÑO DEL DASHBOARD)-->
        <!-- ========================================== -->
        <div x-show="showTicketModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 print:p-0 print:bg-white print:fixed print:inset-0"
            style="display: none;">

            <style>
                @media print {
                    body * { visibility: hidden !important; }
                    #ticket-receipt, #ticket-receipt * { visibility: visible !important; }
                    #ticket-receipt {
                        position: absolute !important; left: 50% !important; top: 20px !important; transform: translateX(-50%) !important;
                        width: 80mm !important; box-shadow: none !important; border: none !important; padding: 0 !important; background: white !important;
                    }
                }
            </style>

            <div id="ticket-receipt" @click.away="showTicketModal = false"
                class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden flex flex-col p-8 space-y-5 border border-gray-100">
                
                <!-- Encabezado -->
                <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                    <div class="space-y-0.5">
                        <h3 class="font-black text-slate-900 text-lg uppercase tracking-wider">OllinFit</h3>
                        <p class="text-[11px] font-semibold text-orange-600 uppercase tracking-widest">Centro de Acceso y Tienda</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">* REIMPRESIÓN *</p>
                    </div>
                    <button @click="showTicketModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-rose-500 hover:text-white text-gray-500 font-bold flex items-center justify-center transition-colors cursor-pointer print:hidden">✕</button>
                </div>

                <!-- Datos -->
                <div class="space-y-1.5 text-xs text-gray-500 border-b border-dashed border-gray-200 pb-4">
                    <div class="flex justify-between"><span>Folio:</span> <strong class="text-slate-800">#MX-<span x-text="ticketData?.id"></span></strong></div>
                    <div class="flex justify-between"><span>Fecha:</span> <strong class="text-slate-800" x-text="ticketData?.date"></strong></div>
                    <div class="flex justify-between"><span>Método:</span> <strong class="text-slate-800" x-text="ticketData?.method"></strong></div>
                </div>

                <!-- Productos -->
                <div class="border-b border-dashed border-gray-200 py-3 space-y-2.5 max-h-48 overflow-y-auto">
                    <template x-for="item in ticketData?.items">
                        <div class="flex justify-between items-center text-xs">
                            <div>
                                <span class="font-bold text-slate-800 block" x-text="item.name"></span>
                                <span class="text-[11px] text-gray-400" x-text="`${item.quantity} x $${Number(item.price).toFixed(2)}`"></span>
                            </div>
                            <span class="font-extrabold text-slate-900" x-text="`$${(item.price * item.quantity).toFixed(2)}`"></span>
                        </div>
                    </template>
                </div>

                <!-- Totales -->
                <div class="space-y-2 text-xs pt-1">
                    <div class="flex justify-between font-black text-base text-slate-900 border-t border-gray-100 pt-3">
                        <span>TOTAL:</span>
                        <span class="text-emerald-600" x-text="`$${Number(ticketData?.total).toFixed(2)}`"></span>
                    </div>
                </div>

                <!-- Pie -->
                <div class="text-center pt-2 border-t border-dashed border-gray-200 space-y-1">
                    <p class="text-xs font-bold text-slate-700">¡Gracias por tu visita!</p>
                </div>

                <!-- Botones -->
                <div class="flex gap-2 pt-2 print:hidden">
                    <button onclick="window.print()" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-2xl shadow-md flex items-center justify-center gap-2">
                        🖨️ Imprimir
                    </button>
                    <button @click="showTicketModal = false" class="flex-1 bg-gray-200 hover:bg-gray-300 text-slate-700 font-bold text-xs py-3 rounded-2xl shadow-md flex items-center justify-center gap-2">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>