<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- 1. Encabezado de Bienvenida -->
        <div class="mb-6 flex justify-between items-center bg-white p-6 rounded-xl shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Principal</h1>
                <p class="text-sm text-gray-500">Bienvenido, {{ Auth::user()->name }}. Tu cargo actual es: 
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">
                        {{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}
                    </span>
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition">
                    Ir al Punto de Venta
                </a>
                <a href="{{ route('socios.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-900 transition">
                    Gestionar Socios
                </a>
            </div>
        </div>

        <!-- 2. Resumen de Ingresos (Tarjetas Superiores) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ingresos Esta Semana</p>
                    <h3 class="text-3xl font-extrabold text-gray-900 mt-1">${{ number_format($ingresosSemana ?? 0, 2) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 text-amber-600 rounded-lg text-xl">📊</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ingresos Este Mes</p>
                    <h3 class="text-3xl font-extrabold text-gray-900 mt-1">${{ number_format($ingresosMes ?? 0, 2) }}</h3>
                </div>
                <div class="p-3 bg-amber-50 text-amber-600 rounded-lg text-xl">📅</div>
            </div>
        </div>

        <!-- 3. Sección Principal: Últimos Pagos y Accesos Rápidos -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Últimos Pagos Registrados (Ocupa 2 espacios) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-md font-bold text-gray-800">Últimos Pagos Registrados</h3>
                        <a href="{{ route('socios.index') }}" class="text-xs text-amber-600 font-semibold hover:underline">Ver todos →</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-400">
                                <tr>
                                    <th class="px-4 py-2">Folio</th>
                                    <th class="px-4 py-2">Método</th>
                                    <th class="px-4 py-2">Total</th>
                                    <th class="px-4 py-2">Fecha / Hora</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($ultimosPagos ?? [] as $pago)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-800">#MX-{{ $pago->id }}</td>
                                        <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs">{{ $pago->payment_method ?? 'Efectivo' }}</span></td>
                                        <td class="px-4 py-3 text-emerald-600 font-bold">${{ number_format($pago->total, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $pago->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-gray-400 text-xs">No hay pagos recientes registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Panel de Accesos Rápidos (Ocupa 1 espacio) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <h3 class="text-md font-bold text-gray-800 mb-4 tracking-wide uppercase text-sm">Accesos Rápidos</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('caja.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">💰</span>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-amber-600">Flujo de Caja y Corte</span>
                            </div>
                            <span class="text-gray-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>

                        <a href="{{ route('productos.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">📦</span>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-amber-600">Inventario y Artículos</span>
                            </div>
                            <span class="text-gray-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>

                        <a href="{{ route('reports.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">📊</span>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-amber-600">Reportes Generales</span>
                            </div>
                            <span class="text-gray-400 group-hover:translate-x-1 transition-transform">→</span>
                        </a>
                    </div>
                </div>

                <div class="mt-6 bg-[#0f172a] text-white p-4 rounded-xl text-center shadow-inner">
                    <h4 class="text-xs font-bold text-amber-500 uppercase tracking-wider">Sistema OllinFit</h4>
                    <p class="text-xs text-gray-300 mt-1">Control de Accesos y Punto de Venta Activo.</p>
                </div>
            </div>

        </div>

        <!-- 4. Sección de Análisis de Negocio (Gráfica Anual y Top Productos) -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
            <div class="flex items-center gap-2 mb-6">
                <span class="text-amber-600 font-bold">🟠</span>
                <h3 class="text-md font-bold text-gray-800">Análisis de Negocio (Este Año)</h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Comportamiento de Ventas (Ocupa 2 espacios) -->
                <div class="lg:col-span-2 border border-gray-100 p-4 rounded-xl">
                    <h4 class="text-sm font-bold text-gray-700 mb-4">Comportamiento de Ventas</h4>
                    
                    <!-- Aquí puedes integrar tu canvas de Chart.js o la estructura visual de barras -->
                    <div class="h-64 flex items-end justify-between gap-2 pt-6 px-2 border-b border-l border-gray-200">
                        @php
                            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                        @endphp
                        @foreach($meses as $index => $mes)
                            @php 
                                $montoMes = $ventasPorMes[$index + 1] ?? 0;
                                // Altura simulada proporcional (máximo 200px)
                                $altura = min($montoMes > 0 ? ($montoMes / 10) : 4, 200); 
                            @endphp
                            <div class="flex flex-col items-center flex-1">
                                <div style="height: {{ $altura }}px;" class="w-full bg-amber-600 rounded-t hover:bg-amber-700 transition cursor-pointer" title="${{ $montoMes }}"></div>
                                <span class="text-[10px] text-gray-500 mt-2">{{ $mes }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-center text-xs text-amber-600 font-medium mt-4">🔥 Haz clic en una barra para filtrar los productos vendidos en ese mes.</p>
                </div>

                <!-- Top 5 Productos Vendidos (Ocupa 1 espacio) -->
                <div class="border border-gray-100 p-4 rounded-xl flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-bold text-gray-700">Top 5 Productos Vendidos</h4>
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-semibold">Mes: Oct</span>
                        </div>

                        <!-- Espacio de gráfica o lista de productos -->
                        <div class="h-44 flex items-center justify-center border border-dashed border-gray-200 rounded-lg text-gray-400 text-xs">
                            Gráfica Top Productos
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-100 text-center">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Total Piezas</p>
                            <p class="text-lg font-extrabold text-gray-800">0</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Más Vendido</p>
                            <p class="text-sm font-bold text-gray-600">N/A</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>