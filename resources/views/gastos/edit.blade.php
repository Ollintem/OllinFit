<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            
            <!-- Encabezado Oscuro -->
            <div class="bg-zinc-900 px-8 py-6 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Editar Registro #{{ $gasto->id }}
                </h2>
                <a href="{{ route('gastos.index') }}" class="text-xs font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg border border-zinc-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver
                </a>
            </div>

            <div class="p-8">
                <!-- Alerta de Seguridad Financiera -->
                <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <div>
                        <h4 class="text-sm font-bold text-blue-800">Bloqueo de Seguridad Activo</h4>
                        <p class="text-xs text-blue-600 mt-1 leading-relaxed">
                            Para proteger el flujo de caja y la auditoría de inventario, los montos, métodos de pago y productos vinculados a este registro <strong>no pueden modificarse</strong>. Si detectas un error en las cantidades financieras, debes cancelar (eliminar) este registro y crear uno nuevo.
                        </p>
                    </div>
                </div>

                <form action="{{ route('gastos.update', $gasto->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- CONDICIONAL: Compra a Proveedor vs Gasto General -->
                    @if($gasto->type === 'compra_proveedor')
                        <div class="bg-orange-50/40 border border-orange-100 rounded-xl p-6 shadow-sm">
                            <h4 class="text-xs font-bold text-orange-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Proveedor Registrado
                            </h4>
                            <input type="text" value="{{ $gasto->supplier->name ?? 'Compra General (Sin Proveedor)' }}" class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-500 cursor-not-allowed" readonly>
                        </div>
                    @else
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Concepto del Gasto <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </div>
                                <input type="text" name="concept" value="{{ old('concept', $gasto->concept) }}" required class="w-full pl-9 bg-white border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-800 focus:ring-orange-500 focus:border-orange-500 transition-colors">
                            </div>
                        </div>
                    @endif

                    <!-- SECCIÓN BLOQUEADA: Totales y Método -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-500">Total de la Operación</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none font-bold text-gray-400">$</div>
                                <input type="text" value="{{ number_format($gasto->amount, 2) }}" class="w-full pl-8 bg-gray-100 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-extrabold text-gray-500 cursor-not-allowed" readonly>
                            </div>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-500">Método de Pago</label>
                            <div class="relative">
                                <input type="text" value="{{ $gasto->payment_method }}" class="w-full bg-gray-100 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-gray-500 cursor-not-allowed" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN EDITABLE: Descripción -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Descripción Detallada <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm font-medium text-gray-800 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none">{{ old('description', $gasto->description) }}</textarea>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('gastos.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-colors text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 font-bold shadow-md shadow-orange-500/20 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Actualizar Registro
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>