<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{ 
        openModal: false, 
        openEditModal: false, 
        editData: { id: '', name: '', contact_name: '', phone: '', email: '' },
        openDeleteModal: false,
        deleteUrl: '',
        deleteName: ''
    }">
        
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Directorio de Proveedores
                </h1>
                <p class="text-sm text-gray-500 mt-1">Gestiona las empresas o personas que te surten inventario.</p>
            </div>
            <button @click="openModal = true" class="bg-orange-500 hover:bg-orange-600 text-white font-bold text-sm px-5 py-2.5 rounded-lg shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Proveedor
            </button>
        </div>

        <!-- Alertas de Éxito y Error -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-4">Empresa / Marca</th>
                            <th class="px-6 py-4">Contacto (Vendedor)</th>
                            <th class="px-6 py-4">Teléfono</th>
                            <th class="px-6 py-4">Correo</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @if($proveedores->isEmpty())
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <p class="text-base font-bold text-gray-500">No hay proveedores registrados</p>
                                    <p class="text-sm mt-1">Da clic en "Nuevo Proveedor" para agregarlos al catálogo.</p>
                                </td>
                            </tr>
                        @else
                            @foreach($proveedores as $prov)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $prov->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $prov->contact_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $prov->phone ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $prov->email ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            <!-- Botón Editar Seguro -->
                                            <button @click="
                                                editData.id = '{{ $prov->id }}';
                                                editData.name = '{{ addslashes($prov->name) }}';
                                                editData.contact_name = '{{ addslashes($prov->contact_name) }}';
                                                editData.phone = '{{ addslashes($prov->phone) }}';
                                                editData.email = '{{ addslashes($prov->email) }}';
                                                openEditModal = true;
                                            " class="p-1.5 text-orange-500 bg-orange-50 hover:bg-orange-100 rounded transition-colors" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <!-- Botón Eliminar (Abre el Modal Personalizado) -->
                                            <button type="button" @click="
                                                deleteUrl = '{{ route('proveedores.destroy', $prov->id) }}';
                                                deleteName = '{{ addslashes($prov->name) }}';
                                                openDeleteModal = true;
                                            " class="p-1.5 text-red-500 bg-red-50 hover:bg-red-100 rounded transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL AGREGAR PROVEEDOR -->
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;">
            <div @click.away="openModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 space-y-4">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Registrar Proveedor
                </h3>
                
                <form action="{{ route('proveedores.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Empresa o Marca *</label>
                        <input type="text" name="name" required placeholder="Ej: Coca Cola, Optimum Nutrition..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nombre del Vendedor (Opcional)</label>
                        <input type="text" name="contact_name" placeholder="Ej: Juan Pérez" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Teléfono (Opcional)</label>
                            <input type="text" name="phone" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Correo (Opcional)</label>
                            <input type="email" name="email" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">Cancelar</button>
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">Guardar Proveedor</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDITAR PROVEEDOR -->
        <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;">
            <div @click.away="openEditModal = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 space-y-4">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Editar Proveedor
                </h3>
                
                <form :action="`/proveedores/${editData.id}`" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Empresa o Marca *</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nombre del Vendedor</label>
                        <input type="text" name="contact_name" x-model="editData.contact_name" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Teléfono</label>
                            <input type="text" name="phone" x-model="editData.phone" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Correo</label>
                            <input type="email" name="email" x-model="editData.email" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-800 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">Cancelar</button>
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">Actualizar Datos</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL SEGURIDAD ELIMINAR PROVEEDOR -->
        <div x-show="openDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" style="display: none;">
            <div @click.away="openDeleteModal = false" class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-8 text-center space-y-6">
                
                <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto border-8 border-red-50/50">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Eliminar Proveedor</h3>
                    <p class="text-gray-500 text-sm">Vas a borrar a: <strong class="text-gray-900" x-text="deleteName"></strong>.</p>
                    <p class="text-red-500 text-xs font-bold mt-2">Esta acción no se puede deshacer.</p>
                </div>

                <form :action="deleteUrl" method="POST" class="flex items-center justify-center gap-3 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDeleteModal = false" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-bold transition-colors text-sm w-full cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 font-bold shadow-sm transition-colors text-sm w-full flex items-center justify-center gap-2 cursor-pointer">
                        Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>