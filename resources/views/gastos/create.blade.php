<x-app-layout>
    <div class="max-w-3xl mx-auto p-6" x-data="{ 
        tab: 'gasto_general',
        amount: '',
        items: [],
        newProduct: '',
        newQty: 1,
        newCost: '',
        
        get cartTotal() {
            return this.items.reduce((sum, item) => sum + (item.qty * item.cost), 0);
        },
        
        addItem() {
            if(!this.newProduct || this.newQty <= 0 || this.newCost <= 0) return;
            let select = document.getElementById('product-select');
            let name = select.options[select.selectedIndex].text;
            
            this.items.push({
                id: this.newProduct,
                name: name,
                qty: parseFloat(this.newQty),
                cost: parseFloat(this.newCost)
            });
            
            this.newProduct = '';
            this.newQty = 1;
            this.newCost = '';
            this.updateTotal();
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
            this.updateTotal();
        },
        
        updateTotal() {
            if(this.tab === 'compra_proveedor') {
                this.amount = this.cartTotal.toFixed(2);
            }
        }
    }" x-init="$watch('tab', value => { if(value === 'compra_proveedor') updateTotal(); else amount = ''; })">
        
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            
            <!-- Encabezado Oscuro -->
            <div class="bg-zinc-900 px-8 py-6 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Registrar Compra / Gasto
                </h2>
                <a href="{{ route('gastos.index') }}" class="text-xs font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg border border-zinc-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver
                </a>
            </div>

            <div class="p-8">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Detalles del Registro</h3>
                
                <!-- Pestañas (Tabs) -->
                <div class="flex gap-2 mb-8 bg-gray-50 p-1.5 rounded-xl border border-gray-100 shadow-inner">
                    <button type="button" @click="tab = 'compra_proveedor'" 
                        :class="tab === 'compra_proveedor' ? 'bg-white shadow-sm text-orange-600 font-bold border border-gray-200' : 'text-gray-500 hover:text-gray-800 font-semibold'" 
                        class="flex-1 py-3 rounded-lg flex items-center justify-center gap-2 text-sm transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        COMPRA A PROVEEDOR
                    </button>
                    <button type="button" @click="tab = 'gasto_general'" 
                        :class="tab === 'gasto_general' ? 'bg-white shadow-sm text-orange-600 font-bold border border-gray-200' : 'text-gray-500 hover:text-gray-800 font-semibold'" 
                        class="flex-1 py-3 rounded-lg flex items-center justify-center gap-2 text-sm transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        GASTO GENERAL
                    </button>
                </div>

                <form action="{{ route('gastos.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <!-- Inputs ocultos dinámicos -->
                    <input type="hidden" name="type" x-model="tab">
                    
                    <input type="hidden" name="items_json" :value="JSON.stringify(items)">

                    <!-- SECCIÓN DINÁMICA: Compra a Proveedor -->
                    <div x-show="tab === 'compra_proveedor'" x-transition class="space-y-6">
                        
                        <!-- Contenedor del Proveedor Mejorado (Tonos Naranjas) -->
                        <div class="bg-orange-50/40 border border-orange-100 rounded-xl p-6 shadow-sm">
                            <!-- Encabezado y Botón -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-orange-100/50 pb-4 mb-4">
                                <h4 class="text-xs font-bold text-orange-800 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    Proveedor Autorizado
                                </h4>
                                
                                <a href="{{ route('proveedores.index') }}" target="_blank" class="bg-white border border-orange-200 hover:bg-orange-50 hover:border-orange-300 text-orange-700 text-xs font-bold py-2 px-4 rounded-lg transition-all shadow-sm flex items-center gap-2" title="Abrir directorio para registrar nuevo proveedor">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Registrar Nuevo Proveedor
                                </a>
                            </div>
                            
                            <!-- Input Amplio -->
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide">Selecciona a quién le estás comprando</label>
                                <select name="supplier_id" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:ring-orange-500 focus:border-orange-500 shadow-sm transition-colors">
                                    <option value="">🏢 Sin proveedor específico (Compra Genérica)...</option>
                                    @forelse ($suppliers as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @empty
                                        <option value="" disabled>No hay proveedores registrados en el directorio</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <!-- (Aquí debajo sigue tu Mini Carrito Azul que ya pusimos) -->
                        <!-- Mini Carrito de Entrada Mejorado -->
                        <div class="bg-blue-50/40 border border-blue-100 rounded-xl p-6 space-y-5 shadow-inner">
                            
                            <!-- Encabezado y Botón de Nuevo Producto -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-blue-100/50 pb-3">
                                <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Agregar Productos Entrantes
                                </h4>
                                
                                <a href="{{ url('productos/create') }}" target="_blank" class="bg-white border border-blue-200 hover:bg-blue-50 hover:border-blue-300 text-blue-700 text-xs font-bold py-2 px-4 rounded-lg transition-all shadow-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    + Crear Nuevo en Inventario
                                </a>
                            </div>
                            
                            <!-- Controles de Formulario Más Amplios -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-6 space-y-1.5">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide">Buscar Producto</label>
                                    <select id="product-select" x-model="newProduct" class="w-full border-gray-200 rounded-xl text-sm bg-white py-2.5 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-colors">
                                        <option value="">Selecciona un producto del catálogo...</option>
                                        @forelse($products as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                        @empty
                                            <option value="" disabled>No hay productos registrados en inventario</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide">Cantidad</label>
                                    <input type="number" x-model="newQty" min="1" placeholder="Ej: 10" class="w-full border-gray-200 rounded-xl text-sm text-center bg-white py-2.5 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-colors">
                                </div>
                                <div class="md:col-span-2 space-y-1.5">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide">Costo Unit.</label>
                                    <input type="number" step="0.01" x-model="newCost" placeholder="$ 0.00" class="w-full border-gray-200 rounded-xl text-sm text-right bg-white py-2.5 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-colors">
                                </div>
                                <div class="md:col-span-2">
                                    <button @click="addItem()" type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm py-2.5 rounded-xl shadow-md shadow-blue-500/20 transition-all flex justify-center items-center gap-1.5">
                                        Añadir
                                    </button>
                                </div>
                            </div>

                            <!-- Lista de Items Agregados -->
                            <div x-show="items.length > 0" class="bg-white rounded-lg border border-blue-100 overflow-hidden mt-4">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-blue-50/50 border-b border-blue-100 text-[10px] font-bold text-blue-800 uppercase">
                                        <tr>
                                            <th class="px-4 py-2">Producto</th>
                                            <th class="px-4 py-2 text-center">Cant.</th>
                                            <th class="px-4 py-2 text-right">Costo U.</th>
                                            <th class="px-4 py-2 text-right">Subtotal</th>
                                            <th class="px-4 py-2 text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-2 font-medium text-gray-700" x-text="item.name"></td>
                                                <td class="px-4 py-2 text-center text-gray-600" x-text="item.qty"></td>
                                                <td class="px-4 py-2 text-right text-gray-600" x-text="'$' + item.cost.toFixed(2)"></td>
                                                <td class="px-4 py-2 text-right font-bold text-blue-600" x-text="'$' + (item.qty * item.cost).toFixed(2)"></td>
                                                <td class="px-4 py-2 text-center">
                                                    <button @click="removeItem(index)" type="button" class="text-red-400 hover:text-red-600 transition-colors">
                                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN DINÁMICA: Gasto General -->
                    <div x-show="tab === 'gasto_general'" x-transition class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Concepto del Gasto</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </div>
                            <input type="text" name="concept" placeholder="Ej: Pago de luz, Mantenimiento..." class="w-full pl-9 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-800 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                        </div>
                    </div>

                    <!-- SECCIÓN COMÚN: Totales y Descripción -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Total de la Operación</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none font-bold text-emerald-500">$</div>
                                <input type="number" step="0.01" name="amount" x-model="amount" required placeholder="0.00" 
                                    :readonly="tab === 'compra_proveedor'"
                                    :class="tab === 'compra_proveedor' ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50 focus:ring-emerald-500 focus:border-emerald-500'"
                                    class="w-full pl-8 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-extrabold text-gray-800 transition-colors">
                            </div>
                            <p x-show="tab === 'compra_proveedor'" class="text-[9px] text-gray-400 mt-1">Calculado automáticamente del carrito.</p>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Método de Pago</label>
                            <select name="payment_method" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                                <option value="Efectivo">💵 Efectivo (Resta de Caja)</option>
                                <option value="Transferencia">📱 Transferencia</option>
                                <option value="Tarjeta">💳 Tarjeta Bancaria</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Descripción Detallada <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="3" required placeholder="Escribe detalles del gasto o número de factura..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none"></textarea>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('gastos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-colors text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 font-bold shadow-md shadow-orange-500/20 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Guardar Registro
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>