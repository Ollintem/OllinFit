<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        
        <!-- Header con botón de regreso -->
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Editar Empleado</h1>
        </div>

        <!-- FORMULARIO DE EDICIÓN -->
        <form action="{{ route('empleados.update', $empleado->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT') <!-- Directiva obligatoria para actualizar en Laravel -->

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                
                <!-- Subir Foto (Pre-cargamos la foto actual si existe) -->
                <div class="flex items-center gap-6 mb-8 pb-8 border-b border-gray-100">
                    <div class="relative w-24 h-24 shrink-0">
                        <div id="avatarContainer" class="w-full h-full rounded-full border-2 {{ $empleado->profile_photo_path ? 'border-solid border-gray-200 bg-white' : 'border-dashed border-gray-300 bg-gray-50' }} flex items-center justify-center text-gray-400 overflow-hidden">
                            
                            <!-- Ícono (Oculto si hay foto) -->
                            <svg id="defaultIcon" class="w-8 h-8 {{ $empleado->profile_photo_path ? 'hidden' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            
                            <!-- Imagen (Visible si hay foto) -->
                            <img id="photoPreview" 
                                 src="{{ $empleado->profile_photo_path ? asset('storage/' . $empleado->profile_photo_path) : '' }}" 
                                 class="{{ $empleado->profile_photo_path ? '' : 'hidden' }} w-full h-full object-cover p-1 rounded-full">
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Foto de perfil</p>
                        <p class="text-xs text-gray-500 mb-2">Selecciona una nueva imagen solo si deseas cambiarla.</p>
                        <label class="text-orange-500 text-sm font-medium hover:text-orange-600 cursor-pointer transition-colors">
                            Cambiar imagen
                            <input type="file" name="profile_photo_path" class="hidden" accept="image/png, image/jpeg, image/webp" onchange="previewImage(event)">
                        </label>
                    </div>
                </div>

                <!-- Datos del empleado -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $empleado->name) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $empleado->last_name) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $empleado->email) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone', $empleado->phone) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">Rol asignado <span class="text-red-500">*</span></label>
                            <a href="{{ route('roles.create') }}" class="text-xs font-bold text-orange-500 hover:text-orange-600 flex items-center gap-1">
                                Añadir nuevo
                            </a>
                        </div>
                        <select name="role" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 text-gray-600" required>
                            @php 
                                // Extraemos el nombre del rol actual del usuario
                                $currentRole = $empleado->getRoleNames()->first(); 
                            @endphp
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $currentRole) == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de contratación</label>
                        <input type="date" name="hire_date" value="{{ old('hire_date', $empleado->hire_date) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <!-- Contraseña: Aviso de que es opcional -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actualizar Contraseña</label>
                        <p class="text-xs text-gray-500 mb-3">Deja este campo en blanco si deseas mantener la contraseña actual.</p>
                        <div class="relative">
                            <input type="password" id="passwordInput" name="password" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 pr-10">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Estado Activo/Inactivo -->
                    <div class="md:col-span-2 flex items-center gap-3 pt-4 border-t border-gray-100">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <!-- Input oculto por si desmarcan el checkbox -->
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $empleado->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                        <span class="text-sm font-medium text-gray-700">Cuenta Activa (Permitir acceso al sistema)</span>
                    </div>

                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-4 mt-6">
                <a href="{{ route('empleados.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    Actualizar Empleado
                </button>
            </div>
        </form>
    </div>

    <!-- Súper Script de Imágenes (Redimensionar a WebP) -->
    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function previewImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const MAX_SIZE = 500;
                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > MAX_SIZE) {
                                height *= MAX_SIZE / width;
                                width = MAX_SIZE;
                            }
                        } else {
                            if (height > MAX_SIZE) {
                                width *= MAX_SIZE / height;
                                height = MAX_SIZE;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        const resizedDataUrl = canvas.toDataURL('image/webp', 0.8);

                        const preview = document.getElementById('photoPreview');
                        const icon = document.getElementById('defaultIcon');
                        const container = document.getElementById('avatarContainer');
                        
                        preview.src = resizedDataUrl;
                        preview.classList.remove('hidden');
                        icon.classList.add('hidden');
                        container.classList.remove('border-dashed', 'bg-gray-50');
                        container.classList.add('border-solid', 'border-gray-200', 'bg-white');

                        fetch(resizedDataUrl)
                            .then(res => res.blob())
                            .then(blob => {
                                const newFile = new File([blob], file.name.split('.')[0] + '.webp', { type: 'image/webp' });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(newFile);
                                input.files = dataTransfer.files;
                            });
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>