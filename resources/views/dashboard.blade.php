<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div x-data="{
        showTicketModal: false,
        ticketData: null,
        filtroGrafica: 'semana',
        openTicket(pago) {
            this.ticketData = {
                id: pago.id,
                date: pago.ticket_date,
                method: pago.payment_method || 'Efectivo',
                total: pago.total,
                items: pago.ticket_items
            };
            this.showTicketModal = true;
        }
    }" 
    class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Encabezado de Bienvenida y Estatus de Caja -->
        <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            @if($estadoCaja === 'ABIERTA')
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
            @endif

            <div class="relative z-10">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Principal</h1>
                @php
                    $hora = now()->timezone('America/Mexico_City')->format('H');$saludo = 'Buenas noches';
                    if ($hora >= 5 && $hora < 12)$saludo = 'Buenos días';
                    elseif ($hora >= 12 && $hora < 19)$saludo = 'Buenas tardes';
                @endphp
                <p class="text-sm text-gray-500 mt-1">{{ $saludo }}, <span class="font-semibold text-gray-700">{{ Auth::user()->name }}</span>.</p>
            </div>
            
            <div class="mt-4 md:mt-0 flex flex-col sm:flex-row items-center gap-4 relative z-10">
                <div class="flex items-center gap-3 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl shadow-inner">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Estatus de Caja</span>
                        @if($estadoCaja === 'ABIERTA')
                            <span class="text-sm font-black text-emerald-600 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> ABIERTA</span>
                        @else
                            <span class="text-sm font-black text-red-500 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> CERRADA</span>
                        @endif
                    </div>
                    @if($estadoCaja === 'ABIERTA')
                        <div class="h-8 w-px bg-gray-200 mx-1"></div>
                        <div class="flex flex-col text-right">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Efectivo Disp.</span>
                            <span class="text-sm font-black text-emerald-600">${{ number_format($dineroEnCaja, 2) }}</span>
                        </div>
                    @endif
                </div>

                <a href="{{ route('sales.index') }}" class="w-full sm:w-auto flex justify-center items-center gap-2 px-5 py-3 bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-xl text-sm font-bold hover:shadow-lg hover:shadow-orange-500/30 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Punto de Venta
                </a>
            </div>
        </div>

        <!-- KPIs Financieros -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            
            <div class="bg-slate-900 p-6 rounded-2xl shadow-md border border-slate-800 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-slate-800 rounded-full blur-xl opacity-50 group-hover:bg-emerald-900 transition-colors"></div>
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Utilidad Mensual</p>
                        <h3 class="text-2xl font-black {{ $utilidadMes >= 0 ? 'text-emerald-400' : 'text-red-400' }}">${{ number_format($utilidadMes ?? 0, 2) }}</h3>
                        @if($crecimientoUtilidad >= 0)
                            <span class="text-[10px] font-bold text-emerald-400 mt-1 block">▲ +{{ number_format($crecimientoUtilidad, 1) }}% vs mes ant.</span>
                        @else
                            <span class="text-[10px] font-bold text-red-400 mt-1 block">▼ {{ number_format($crecimientoUtilidad, 1) }}% vs mes ant.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-blue-200 transition-colors">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Accesos Hoy</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $accesosHoy ?? 0 }}</h3>
                    @if($crecimientoAccesos >= 0)
                        <span class="text-[10px] font-bold text-emerald-500 mt-1 block">▲ +{{ number_format($crecimientoAccesos, 1) }}% vs ayer</span>
                    @else
                        <span class="text-[10px] font-bold text-red-500 mt-1 block">▼ {{ number_format($crecimientoAccesos, 1) }}% vs ayer</span>
                    @endif
                </div>
            </div>

            <a href="{{ route('socios.index') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-amber-300 hover:shadow-md hover:shadow-amber-100 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-amber-600 transition-colors">Socios Vencen (5 días)</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $sociosPorVencer ?? 0 }} <span class="text-[10px] text-gray-400 font-medium">de {{ $sociosActivos }}</span></h3>
                </div>
            </a>

            <a href="{{ route('productos.index') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:border-rose-300 hover:shadow-md hover:shadow-rose-100 transition-all cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-rose-500 transition-colors">Agotándose</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ $productosBajos ?? 0 }} <span class="text-[10px] text-gray-400 font-medium">alertas stock</span></h3>
                </div>
            </a>
        </div>

        <!-- GRÁFICAS INTERACTIVAS CON FILTROS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Gráfica Principal (Barras Dinámicas) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <h3 class="text-sm font-bold text-gray-900">Análisis de Ingresos</h3>
                    
                    <!-- Botones de Filtro -->
                    <div class="flex bg-gray-50 p-1.5 rounded-lg border border-gray-100 shadow-inner w-full sm:w-auto">
                        <button @click="filtroGrafica = 'semana'; updateChart('semana')" :class="filtroGrafica === 'semana' ? 'bg-white shadow-sm text-orange-600 border border-gray-200' : 'text-gray-500 hover:text-gray-700'" class="flex-1 sm:flex-none px-4 py-1.5 text-[10px] font-bold uppercase rounded-md transition-all">Semana</button>
                        <button @click="filtroGrafica = 'mes'; updateChart('mes')" :class="filtroGrafica === 'mes' ? 'bg-white shadow-sm text-orange-600 border border-gray-200' : 'text-gray-500 hover:text-gray-700'" class="flex-1 sm:flex-none px-4 py-1.5 text-[10px] font-bold uppercase rounded-md transition-all">Mes</button>
                        <button @click="filtroGrafica = 'anio'; updateChart('anio')" :class="filtroGrafica === 'anio' ? 'bg-white shadow-sm text-orange-600 border border-gray-200' : 'text-gray-500 hover:text-gray-700'" class="flex-1 sm:flex-none px-4 py-1.5 text-[10px] font-bold uppercase rounded-md transition-all">Año</button>
                    </div>
                </div>
                
                <div class="h-64 w-full relative">
                    <canvas id="barrasChart"></canvas>
                </div>
            </div>

            <!-- Gráfica de Dona (Desglose de Ingresos) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">Distribución de Ingresos</h3>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-4">Mes Actual</p>
                </div>
                <div class="h-48 w-full relative mb-4">
                    <canvas id="donaChart"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2 pt-4 border-t border-gray-50">
                    <div class="text-center">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Tienda (POS)</p>
                        <p class="text-sm font-black text-orange-500">${{ number_format($ventasTienda, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Membresías</p>
                        <p class="text-sm font-black text-slate-800">${{ number_format($ventasMembresias, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secciones Inferiores -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-gray-900">Últimos Registros del Punto de Venta</h3>
                    <a href="{{ route('reports.index') }}" class="text-xs text-orange-600 font-semibold hover:underline">Ver reporte →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 tracking-wider">
                            <tr>
                                <th class="px-4 py-3 font-semibold rounded-l-lg">Folio</th>
                                <th class="px-4 py-3 font-semibold">Método</th>
                                <th class="px-4 py-3 font-semibold">Total</th>
                                <th class="px-4 py-3 font-semibold">Hora</th>
                                <th class="px-4 py-3 font-semibold rounded-r-lg text-center">Ticket</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($ultimosPagos ?? [] as $pago)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-gray-800">#MX-{{ $pago->id }}</td>
                                    <td class="px-4 py-3"><span class="px-2 py-1 bg-zinc-100 text-zinc-600 rounded text-[10px] font-bold border border-zinc-200">{{ $pago->payment_method ?? 'Efectivo' }}</span></td>
                                    <td class="px-4 py-3 text-emerald-600 font-bold">${{ number_format($pago->total, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-400 text-[11px]">{{ $pago->created_at->diffForHumans() }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <!-- SOLUCIÓN AL TICKET: Pasamos los datos directo en el HTML con json_encode -->
                                        <button @click='openTicket(@json($pago))' class="text-gray-400 hover:text-orange-500 transition-colors cursor-pointer" title="Ver Ticket">
                                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400 text-sm">No hay pagos recientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-sm font-bold text-gray-800">Top 5 Artículos</h4>
                        <span class="px-2 py-1 bg-gray-50 border border-gray-200 text-gray-600 rounded-md text-[9px] font-bold uppercase">Mes: {{ \Carbon\Carbon::now()->translatedFormat('M') }}</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($topProductos ?? [] as $index =>$producto)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-gray-400 w-3">{{ $index + 1 }}</span>
                                    <p class="text-xs font-semibold text-gray-700 truncate max-w-[140px]">{{ $producto->nombre }}</p>
                                </div>
                                <span class="text-xs font-black text-gray-900">{{ $producto->total_vendido }} <span class="font-normal text-gray-400">pz</span></span>
                            </div>
                        @empty
                            <div class="h-20 flex items-center justify-center text-gray-400 text-xs font-medium">Sin ventas este mes</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- SCRIPT PARA CHART.JS -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const graficasData = {!! $graficasDataJson !!};
                const distribucionDatos = {!! $distribucionIngresosJson !!};
                
                const ctxBarras = document.getElementById('barrasChart').getContext('2d');
                window.barrasChart = new Chart(ctxBarras, {
                    type: 'bar',
                    data: {
                        labels: graficasData['semana'].labels,
                        datasets: [{
                            label: 'Ingresos Totales ($)',
                            data: graficasData['semana'].data,
                            backgroundColor: '#f97316',
                            borderRadius: 6,
                            hoverBackgroundColor: '#ea580c'
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false }, 
                            tooltip: { 
                                backgroundColor: '#1e293b', padding: 12, cornerRadius: 8, titleFont: { size: 13 }, bodyFont: { size: 14, weight: 'bold' },
                                callbacks: { label: function(context) { return ' Ingresos: $' + context.raw.toFixed(2); } }
                            } 
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [4, 4] }, border: { display: false } },
                            x: { 
                                grid: { display: false }, border: { display: false },
                                // SOLUCIÓN 1: Limitamos la cantidad de textos para que no se amontonen
                                ticks: { maxTicksLimit: 12, maxRotation: 0 } 
                            }
                        }
                    }
                });

                window.updateChart = function(filtro) {
                    window.barrasChart.data.labels = graficasData[filtro].labels;
                    window.barrasChart.data.datasets[0].data = graficasData[filtro].data;
                    window.barrasChart.update();
                };

                const ctxDona = document.getElementById('donaChart').getContext('2d');
                new Chart(ctxDona, {
                    type: 'doughnut',
                    data: { labels: ['Tienda (POS)', 'Membresías'], datasets: [{ data: distribucionDatos, backgroundColor: ['#f97316', '#1e293b'], borderWidth: 0, hoverOffset: 4 }] },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false }, tooltip: { cornerRadius: 8, padding: 10 } } }
                });
            });
        </script>

        <!-- MODAL DEL TICKET (AHORA CON DATOS DIRECTOS) -->
        <div x-show="showTicketModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 print:p-0 print:bg-white print:fixed print:inset-0" style="display: none;">
            <style>
                @media print {
                    body * { visibility: hidden !important; }
                    #ticket-receipt, #ticket-receipt * { visibility: visible !important; }
                    #ticket-receipt { position: absolute !important; left: 50% !important; top: 20px !important; transform: translateX(-50%) !important; width: 80mm !important; box-shadow: none !important; border: none !important; padding: 0 !important; background: white !important; }
                }
            </style>
            <div id="ticket-receipt" @click.away="showTicketModal = false" class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden flex flex-col p-8 space-y-5 border border-gray-100">
                <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                    <div class="space-y-0.5">
                        <h3 class="font-black text-slate-900 text-lg uppercase tracking-wider">OllinFit</h3>
                        <p class="text-[11px] font-semibold text-orange-600 uppercase tracking-widest">Centro de Acceso y Tienda</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">* REIMPRESIÓN *</p>
                    </div>
                    <button @click="showTicketModal = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-rose-500 hover:text-white text-gray-500 font-bold flex items-center justify-center transition-colors cursor-pointer print:hidden">✕</button>
                </div>
                <div class="space-y-1.5 text-xs text-gray-500 border-b border-dashed border-gray-200 pb-4">
                    <div class="flex justify-between"><span>Folio:</span> <strong class="text-slate-800">#MX-<span x-text="ticketData?.id"></span></strong></div>
                    <div class="flex justify-between"><span>Fecha:</span> <strong class="text-slate-800" x-text="ticketData?.date"></strong></div>
                    <div class="flex justify-between"><span>Método de Pago:</span> <strong class="text-slate-800" x-text="ticketData?.method"></strong></div>
                </div>
                <div class="border-b border-dashed border-gray-200 py-3 space-y-2.5 max-h-48 overflow-y-auto">
                    <template x-for="item in ticketData?.items">
                        <div class="flex justify-between items-center text-xs">
                            <div><span class="font-bold text-slate-800 block" x-text="item.name"></span><span class="text-[11px] text-gray-400" x-text="`${item.quantity} x $${Number(item.price).toFixed(2)}`"></span></div>
                            <span class="font-extrabold text-slate-900" x-text="`$${(item.price * item.quantity).toFixed(2)}`"></span>
                        </div>
                    </template>
                </div>
                <div class="space-y-2 text-xs pt-1">
                    <div class="flex justify-between font-black text-base text-slate-900 border-t border-gray-100 pt-3"><span>TOTAL:</span><span class="text-emerald-600" x-text="`$${Number(ticketData?.total).toFixed(2)}`"></span></div>
                </div>
                <div class="flex gap-2 pt-2 print:hidden">
                    <button onclick="window.print()" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-2xl transition-colors cursor-pointer shadow-md">🖨️ Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>