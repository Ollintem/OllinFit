<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        
        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Socio</h1>
                <p class="text-sm text-gray-500 mt-1">Actualizando los datos de: <span class="font-bold">{{ $member->name }} {{ $member->last_name }}</span></p>
            </div>
            <a href="{{ route('socios.index') }}" class="text-gray-500 hover:text-orange-500 transition-colors text-sm font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a la lista
            </a>
        </div>

        <!-- Tarjeta del Formulario -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            
            <!-- Fíjate en el método POST y la directiva @method('PUT') -->
            <form action="{{ route('socios.update', $member->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                            class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}" required
                            class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" 
                            class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $member->email) }}" 
                            class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                    </div>

                    <!-- Plan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Asignado</label>
                        <select name="plan_id" class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                            <option value="">Sin plan asignado</option>
                            @foreach($planes as $plan)
                                <!-- Se selecciona automáticamente el plan que ya tiene el socio -->
                                <option value="{{ $plan->id }}" {{ old('plan_id', $member->plan_id) == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-100 mt-8">
                    <a href="{{ route('socios.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm text-center">
                        Cancelar
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-bold shadow-sm transition-colors text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>