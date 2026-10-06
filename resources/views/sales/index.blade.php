<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{
        cart: [],
        paymentMethod: 'Efectivo',
        cashReceived: '',
        cardReference: '',
        errorMessage: null,
        showTicketModal: false,
        ticketData: null,
    
        addItem(id, name, price) {
            let existing = this.cart.find(item => item.id === id);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({ id, name, price, quantity: 1 });
            }
        },
    
        get total() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
    
        get change() {
            let received = parseFloat(this.cashReceived) || 0;
            return received >= this.total ? received - this.total : 0;
        },
    
        checkout() {
            if (this.cart.length === 0) return;
    
            if (this.paymentMethod === 'Efectivo' && (parseFloat(this.cashReceived) || 0) < this.total) {
                this.errorMessage = 'El efectivo recibido es menor al total a cobrar.'; // <-- Reemplaza el alert por esto
    
                // Opcional: hace que desaparezca automáticamente después de 4 segundos
                setTimeout(() => { this.errorMessage = null; }, 4000);
                return;
            }
    
            fetch('<?php echo route('sales.store'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cart_data: this.cart,
                        payment_method: this.paymentMethod,
                        cash_received: this.cashReceived,
                        card_reference: this.cardReference
                    })
                })
                .then(async response => {
                    let text = await response.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error(text);
                        alert('Error crítico en el servidor (mira la consola F12): ' + text.substring(0, 150));
                        return;
                    }
    
                    if (response.ok) {
                        this.ticketData = {
                            id: data.sale_id || Math.floor(Math.random() * 90000) + 10000,
                            date: new Date().toLocaleString(),
                            items: [...this.cart],
                            total: this.total,
                            received: parseFloat(this.cashReceived) || this.total,
                            change: this.change,
                            method: this.paymentMethod
                        };
                        this.showTicketModal = true;
                    } else {
                        alert('Error al registrar: ' + (data.error || 'Ocurrió un problema en el servidor.'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error de conexión con el servidor.');
                });
        },
    
        resetPOS() {
            this.cart = [];
            this.cashReceived = '';
            this.showTicketModal = false;
            window.location.reload();
        }
    }">
        <!-- Alerta flotante moderna con Alpine.js -->
        <template x-if="errorMessage">
            <div
                class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 bg-red-50 text-red-800 border border-red-200 rounded-xl shadow-lg transition-all">
                <span class="text-lg">⚠️</span>
                <div class="text-sm font-medium" x-text="errorMessage"></div>
                <button @click="errorMessage = null" class="ml-4 text-red-400 hover:text-red-700 font-bold">×</button>
            </div>
        </template>
        <!-- Encabezado -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Punto de Venta (POS)</h1>
                <p class="text-sm text-gray-500 mt-0.5">Caja rápida para cobro de accesos, pases y productos.</p>
            </div>

            <a href="{{ route('productos.index') }}"
                class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-2.5 px-4 rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                Ver Inventario / Productos
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- PANEL IZQUIERDO -->
            <div class="lg:col-span-2 space-y-6">

                <!-- SECCIÓN 1: ACCESOS Y PASES -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                        <span class="text-orange-500 font-bold">🎫</span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Accesos y Pases</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php
                        $accesos = isset($productos) ? $productos->where('is_service', 1) : collect();
                        ?>
                        <?php if($accesos->count() > 0): ?>
                        <?php foreach($accesos as$acc): ?>
                        <button @click="addItem(<?php echo $acc->id; ?>, '<?php echo addslashes($acc->name); ?>', <?php echo $acc->price; ?>)"
                            class="bg-white hover:border-orange-400 border border-gray-200 p-5 rounded-2xl text-center transition-all cursor-pointer group flex flex-col items-center justify-between shadow-xs">
                            <div
                                class="w-12 h-12 mb-3 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center font-bold text-lg">
                                👤
                            </div>
                            <span
                                class="font-bold text-gray-800 group-hover:text-orange-600 text-sm"><?php echo htmlspecialchars($acc->name); ?></span>
                            <span class="text-sm font-extrabold text-orange-600 mt-2 block">$<?php echo number_format($acc->price, 2); ?></span>
                        </button>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-xs text-gray-400 col-span-2 text-center py-4">No hay accesos registrados.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SECCIÓN 2: EXTRAS (TIENDA) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                        <span class="text-emerald-600 font-bold">🛍️</span>
                        <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Extras (Tienda)</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php
                        $tienda = isset($productos) ? $productos->where('is_service', 0) : collect();
                        ?>
                        <?php if($tienda->count() > 0): ?>
                        <?php foreach($tienda as$prod): ?>
                        <button @click="addItem(<?php echo $prod->id; ?>, '<?php echo addslashes($prod->name); ?>', <?php echo $prod->price; ?>)"
                            class="bg-white hover:border-emerald-400 border border-gray-200 p-5 rounded-2xl text-center transition-all cursor-pointer group flex flex-col items-center justify-between shadow-xs">
                            <div
                                class="w-16 h-16 mb-3 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden flex items-center justify-center">
                                <?php if(!empty($prod->image_path)): ?>
                                <img src="<?php echo asset('storage/' . $prod->image_path); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                <span class="text-lg">📦</span>
                                <?php endif; ?>
                            </div>

                            <span
                                class="font-bold text-gray-800 group-hover:text-emerald-600 text-sm"><?php echo htmlspecialchars($prod->name); ?></span>

                            <div class="my-1">
                                <?php if($prod->stock <= ($prod->min_stock ?? 5)): ?>
                                <span class="text-[10px] font-bold text-rose-500">Stock: <?php echo $prod->stock; ?>
                                    (Bajo)</span>
                                <?php else: ?>
                                <span class="text-[10px] font-bold text-gray-400">Stock: <?php echo $prod->stock; ?></span>
                                <?php endif; ?>
                            </div>

                            <span class="text-sm font-extrabold text-emerald-600 block">$<?php echo number_format($prod->price, 2); ?></span>
                        </button>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-xs text-gray-400 col-span-2 text-center py-4">No hay productos en tienda
                            registrados.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- PANEL DERECHO: TICKET Y COBRO -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                        <h2 class="font-bold text-gray-800 text-sm">Ticket Actual</h2>
                        <button @click="cart = []"
                            class="text-xs text-rose-500 hover:text-rose-700 font-bold cursor-pointer"
                            x-show="cart.length > 0">Vaciar</button>
                    </div>

                    <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                        <template x-for="(item, index) in cart" :key="index">
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl text-xs">
                                <div>
                                    <span class="font-bold text-gray-800 block" x-text="item.name"></span>
                                    <span class="text-gray-400" x-text="`$${item.price} x ${item.quantity}`"></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-extrabold text-gray-800"
                                        x-text="`$${item.price * item.quantity}`"></span>
                                    <button @click="cart.splice(index, 1)"
                                        class="text-gray-400 hover:text-rose-600 font-bold cursor-pointer">×</button>
                                </div>
                            </div>
                        </template>
                        <div x-show="cart.length === 0" class="text-center py-12 text-gray-400 text-xs">
                            Ningún producto agregado.
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Método de Pago</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button @click="paymentMethod = 'Efectivo'"
                                :class="paymentMethod === 'Efectivo' ? 'bg-orange-500 text-white font-bold' :
                                    'bg-gray-100 text-gray-600'"
                                class="py-2 rounded-xl text-xs transition-colors cursor-pointer">Efectivo</button>
                            <button @click="paymentMethod = 'Tarjeta'"
                                :class="paymentMethod === 'Tarjeta' ? 'bg-orange-500 text-white font-bold' :
                                    'bg-gray-100 text-gray-600'"
                                class="py-2 rounded-xl text-xs transition-colors cursor-pointer">Tarjeta</button>
                        </div>
                    </div>

                    <template x-if="paymentMethod === 'Efectivo'">
                        <div class="space-y-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase">Efectivo
                                    Recibido</label>
                                <input type="number" step="0.01" x-model="cashReceived" placeholder="0.00"
                                    class="w-full mt-1 bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div class="flex justify-between items-center text-xs pt-1 border-t border-gray-200">
                                <span class="font-bold text-gray-500">Cambio a Devolver:</span>
                                <span class="font-extrabold text-emerald-600 text-sm"
                                    x-text="`$${change.toFixed(2)}`"></span>
                            </div>
                        </div>
                    </template><!-- Sección condicional para Tarjeta -->
                    <template x-if="paymentMethod === 'Tarjeta'">
                        <div class="space-y-2 mt-4">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Últimos 4 dígitos de la
                                Tarjeta</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">💳</span>
                                <input type="text" x-model="cardReference" maxlength="4" placeholder="Ej. 4589"
                                    class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2.5 text-xs font-bold tracking-widest focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <p class="text-[10px] text-gray-400">Requerido para control de caja.</p>
                        </div>
                    </template>


                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-600">Total a Pagar:</span>
                        <span class="text-2xl font-extrabold text-emerald-600" x-text="`$${total.toFixed(2)}`"></span>
                    </div>

                    <button @click="checkout()" :disabled="cart.length === 0"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 text-white font-bold text-xs py-3 rounded-xl shadow-md transition-colors cursor-pointer">
                        Cobrar y Registrar
                    </button>
                </div>
            </div>

        </div>
        <!-- MODAL DEL TICKET -->
        <div x-show="showTicketModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 print:p-0 print:bg-white print:fixed print:inset-0"
            style="display: none;">

            <style>
                @media print {
                    body * {
                        visibility: hidden !important;
                    }

                    #ticket-receipt,
                    #ticket-receipt * {
                        visibility: visible !important;
                    }

                    #ticket-receipt {
                        position: absolute !important;
                        left: 50% !important;
                        top: 20px !important;
                        transform: translateX(-50%) !important;
                        width: 80mm !important;
                        box-shadow: none !important;
                        border: none !important;
                        padding: 0 !important;
                        background: white !important;
                    }
                }
            </style>

            <div id="ticket-receipt"
                class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden flex flex-col p-8 space-y-5 border border-gray-100">
                <!-- Encabezado del Ticket -->
                <div class="flex justify-between items-start border-b border-gray-100 pb-4">
                    <div class="space-y-0.5">
                        <h3 class="font-black text-slate-900 text-lg uppercase tracking-wider">OllinFit</h3>
                        <p class="text-[11px] font-semibold text-orange-600 uppercase tracking-widest">Centro de Acceso
                            y Tienda</p>
                    </div>
                    <button @click="resetPOS()"
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-rose-500 hover:text-white text-gray-500 font-bold flex items-center justify-center transition-colors cursor-pointer print:hidden">✕</button>
                </div>

                <!-- Datos generales -->
                <div class="space-y-1.5 text-xs text-gray-500 border-b border-dashed border-gray-200 pb-4">
                    <div class="flex justify-between"><span>Folio:</span> <strong class="text-slate-800"
                            x-text="ticketData?.id"></strong></div>
                    <div class="flex justify-between"><span>Fecha:</span> <strong class="text-slate-800"
                            x-text="ticketData?.date"></strong></div>
                    <div class="flex justify-between"><span>Método de Pago:</span> <strong class="text-slate-800"
                            x-text="ticketData?.method"></strong></div>
                </div>

                <!-- Lista de productos -->
                <div class="border-b border-dashed border-gray-200 py-3 space-y-2.5 max-h-48 overflow-y-auto">
                    <template x-for="item in ticketData?.items">
                        <div class="flex justify-between items-center text-xs">
                            <div>
                                <span class="font-bold text-slate-800 block" x-text="item.name"></span>
                                <span class="text-[11px] text-gray-400"
                                    x-text="`${item.quantity} x $${item.price}`"></span>
                            </div>
                            <span class="font-extrabold text-slate-900"
                                x-text="`$${(item.price * item.quantity).toFixed(2)}`"></span>
                        </div>
                    </template>
                </div>

                <!-- Totales -->
                <div class="space-y-2 text-xs pt-1">
                    <div
                        class="flex justify-between font-black text-base text-slate-900 border-t border-gray-100 pt-3">
                        <span>TOTAL:</span>
                        <span class="text-emerald-600" x-text="`$${ticketData?.total.toFixed(2)}`"></span>
                    </div>
                    <template x-if="ticketData?.method === 'Efectivo'">
                        <div class="space-y-1 pt-1 text-gray-500 bg-gray-50 p-3 rounded-2xl">
                            <div class="flex justify-between">
                                <span>Efectivo Recibido:</span>
                                <strong class="text-slate-800"
                                    x-text="`$${ticketData?.received.toFixed(2)}`"></strong>
                            </div>
                            <div class="flex justify-between text-emerald-700 font-bold">
                                <span>Cambio Devuelto:</span>
                                <strong x-text="`$${ticketData?.change.toFixed(2)}`"></strong>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Pie de página -->
                <div class="text-center pt-2 border-t border-dashed border-gray-200 space-y-1">
                    <p class="text-xs font-bold text-slate-700">¡Gracias por tu visita!</p>
                    <p class="text-[10px] text-gray-400">Conserva este ticket para cualquier aclaración.</p>
                </div>

                <!-- Botones de acción (Ocultos al imprimir) -->
                <div class="flex gap-2 pt-2 print:hidden">
                    <button onclick="window.print()"
                        class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-2xl transition-colors cursor-pointer shadow-md flex items-center justify-center gap-2">
                        🖨️ Imprimir
                    </button>
                    <button @click="resetPOS()"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs py-3 rounded-2xl transition-colors cursor-pointer shadow-md flex items-center justify-center gap-2">
                        ✨ Nueva Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
