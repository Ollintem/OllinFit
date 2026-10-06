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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase">Estado Actual</span>
                    <div class="text-2xl font-extrabold text-emerald-600 mt-2 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span> ABIERTA
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2">Desde:
                        {{ \Carbon\Carbon::parse($cajaActiva->opened_at)->format('d/m/Y h:i A') }}</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase">Fondo Inicial (Cambio)</span>
                    <div class="text-2xl font-extrabold text-gray-800 mt-2">
                        ${{ number_format($cajaActiva->opening_amount, 2) }} <span
                            class="text-xs text-gray-400">MXN</span></div>
                    <p class="text-[11px] text-gray-400 mt-2">Dinero base en gaveta</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase">Ingresos Efectivo (Turno)</span>
                    <div class="text-2xl font-extrabold text-emerald-600 mt-2">
                        ${{ number_format($ingresosEfectivoTurno, 2) }} <span class="text-xs text-gray-400">MXN</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2">{{ $ventasTurnoCount }} transacciones en efectivo</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase">Ingresos con Tarjeta</span>
                    <div class="text-2xl font-extrabold text-blue-600 mt-2">
                        ${{ number_format($ingresosTarjetaTurno ?? 0, 2) }} <span
                            class="text-xs text-gray-400">MXN</span>
                    </div>
                    <p class="text-[11px] text-blue-500 mt-2">💳 Pagos electrónicos / POS</p>
                </div>


                <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase">Efectivo Esperado en Caja</span>
                        <div class="text-2xl font-extrabold text-emerald-400 mt-2">
                            ${{ number_format($cajaActiva->opening_amount + $ingresosEfectivoTurno, 2) }}</div>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-2 pt-2 border-t border-slate-800">
                        Fondo + Ingresos en Efectivo
                    </div>
                </div>
            </div>

            <!-- Transacciones en Efectivo de este Turno -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-800 mb-1">Entradas de Efectivo en el Turno Actual</h2>
                <p class="text-xs text-gray-400 mb-6">Listado de cobros físicos registrados desde la apertura de caja.
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-bold text-[10px]">
                                <th class="py-3 px-4">Hora</th>
                                <th class="py-3 px-4">Concepto / ID</th>
                                <th class="py-3 px-4">Tipo</th>
                                <th class="py-3 px-4 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($detalleTransacciones as $tx)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-3.5 px-4 font-medium text-gray-600">
                                        {{ \Carbon\Carbon::parse($tx->created_at)->format('h:i A') }}</td>
                                    <td class="py-3.5 px-4 font-bold text-gray-800">{{ $tx->concepto_id }}</td>
                                    <td class="py-3.5 px-4"><span
                                            class="px-2 py-0.5 rounded font-semibold text-[10px] bg-slate-100 text-slate-700">{{ $tx->origen }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-extrabold text-emerald-600">
                                        ${{ number_format($tx->monto, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400">Aún no hay cobros en
                                        efectivo registrados en este turno.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Aviso de Caja Cerrada -->
            <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm space-y-4">
                <div
                    class="w-16 h-16 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto text-2xl font-bold">
                    🔒</div>
                <h2 class="text-xl font-bold text-gray-800">La caja se encuentra cerrada</h2>
                <p class="text-xs text-gray-500 max-w-md mx-auto">Para comenzar a registrar cobros y llevar el control
                    diario de las operaciones en efectivo, es necesario abrir un nuevo turno indicando el fondo inicial.
                </p>
                <button @click="openModalApertura = true"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-md transition-colors cursor-pointer">
                    Abrir Caja Ahora
                </button>
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

                    <div class="bg-gray-50 p-4 rounded-xl space-y-1 text-xs">
                        <div class="flex justify-between"><span class="text-gray-500">Fondo Inicial:</span> <strong
                                class="text-gray-800">${{ number_format($cajaActiva->opening_amount, 2) }}</strong>
                        </div>
                        <div class="flex justify-between"><span class="text-gray-500">Ingresos Efectivo:</span>
                            <strong class="text-emerald-600">${{ number_format($ingresosEfectivoTurno, 2) }}</strong>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 font-bold text-sm"><span
                                class="text-gray-800">Total Esperado:</span> <strong
                                class="text-slate-900">${{ number_format($cajaActiva->opening_amount + $ingresosEfectivoTurno, 2) }}</strong>
                        </div>
                    </div>

                    <form action="{{ route('caja.close') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Efectivo Físico Contado
                                (MXN)</label>
                            <input type="number" step="0.01" name="closing_amount" required placeholder="0.00"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="openModalCierre = false"
                                class="px-4 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 rounded-xl">Cancelar</button>
                            <button type="submit"
                                class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm">Confirmar
                                Corte y Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
