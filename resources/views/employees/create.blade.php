<x-app-layout>
    <!-- Contenedor centrado para que no ocupe todo el ancho en pantallas gigantes -->
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        
        <!-- Header con botón de regreso -->
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('empleados.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Registrar Nuevo Empleado</h1>
        </div>

        <!-- FORMULARIO -->
        <!-- Nota: enctype="multipart/form-data" es OBLIGATORIO para subir imágenes -->
        <form action="{{ route('empleados.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- SECCIÓN 1: Información del Empleado -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    Información del Empleado
                </h2>

                <!-- Subir Foto -->
                <div class="flex items-center gap-6 mb-8">
                    
                    <!-- Envolvemos todo en un contenedor rígido para que nada lo deforme -->
                    <div class="relative w-24 h-24 shrink-0">
                        
                        <!-- El círculo real -->
                        <div id="avatarContainer" class="w-full h-full rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 bg-gray-50 overflow-hidden">
                            
                            <!-- Ícono SVG por defecto -->
                            <svg id="defaultIcon" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            
                            <!-- Usamos object-contain para que la foto completa se haga pequeña y quepa en el círculo -->
                            <img id="photoPreview" src="" alt="Previsualización" class="hidden w-full h-full object-contain p-1 rounded-full">
                            
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Foto de perfil</p>
                        <p class="text-xs text-gray-500 mb-2">Formato JPG, PNG o WEBP.</p>
                        
                        <label class="text-orange-500 text-sm font-medium hover:text-orange-600 cursor-pointer transition-colors">
                            Subir imagen
                            <input type="file" name="profile_photo_path" class="hidden" accept="image/png, image/jpeg, image/webp" onchange="previewImage(event)">
                        </label>
                    </div>
                </div>

                <!-- Campos de texto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" placeholder="Ej. Juan" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" placeholder="Ej. Pérez" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono de contacto</label>
                        <input type="text" name="phone" placeholder="10 dígitos" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Contratación</label>
                        <input type="date" name="hire_date" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 text-gray-500">
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: Puesto y Accesos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Puesto y Accesos
                </h2>

                <!-- Selector de Rol (Dinámico desde la BD) -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-medium text-gray-700">Rol asignado <span class="text-red-500">*</span></label>
                        
                        <!-- Atajo a crear rol -->
                        <a href="{{ route('roles.create') }}" class="text-xs font-bold text-orange-500 hover:text-orange-600 flex items-center gap-1 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Añadir nuevo rol
                        </a>
                    </div>
                    <select name="role" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 text-gray-600" required>
                        <option value="" disabled selected>Selecciona un puesto de trabajo...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Toggle (Interruptor) de Acceso -->
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg bg-gray-50 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Acceso al panel</p>
                            <p class="text-xs text-gray-500">Habilita esta opción si el empleado necesita iniciar sesión en Ollinfit.</p>
                        </div>
                    </div>
                    <!-- CSS nativo para el switch -->
                    <label class="flex items-center cursor-pointer relative">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        <span class="ml-3 text-sm font-bold text-gray-800">Habilitado</span>
                    </label>
                </div>

                <!-- Credenciales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico (Usuario) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" name="email" placeholder="empleado@ollinfit.com" class="pl-10 w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña Temporal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <!-- Ícono de candado -->
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            
                            <!-- Input con ID agregado -->
                            <input type="password" id="passwordInput" name="password" placeholder="Min. 8 caracteres" class="pl-10 w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" required minlength="8">
                            
                            <!-- Ícono de ojito con evento onclick -->
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword()">
                                <svg class="h-4 w-4 text-gray-400 hover:text-gray-600 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">El empleado podrá cambiarla después.</p>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="flex items-center justify-end gap-4 mt-6">
                <a href="{{ route('empleados.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Guardar Empleado
                </button>
            </div>
            
        </form>
    </div>
    
    <!-- Scripts de la vista -->
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
                    // 1. Creamos un objeto de imagen en memoria
                    const img = new Image();
                    
                    img.onload = function() {
                        // 2. Definimos el tamaño máximo deseado 
                        const MAX_SIZE = 120; // Puedes ajustar este valor según tus necesidades
                        let width = img.width;
                        let height = img.height;

                        // 3. Calculamos la nueva proporción para no deformarla
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

                        // 4. Creamos un "lienzo" (canvas) virtual y dibujamos la foto pequeña ahí
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        // 5. Convertimos el lienzo a una imagen ligera (formato WebP a 80% de calidad)
                        const resizedDataUrl = canvas.toDataURL('image/webp', 0.8);

                        // 6. Mostramos la imagen redimensionada en la previsualización
                        const preview = document.getElementById('photoPreview');
                        const icon = document.getElementById('defaultIcon');
                        const container = document.getElementById('avatarContainer');
                        
                        preview.src = resizedDataUrl;
                        // Regresamos a object-cover para que rellene el círculo perfectamente
                        preview.classList.replace('object-contain', 'object-cover'); 
                        preview.classList.remove('hidden');
                        icon.classList.add('hidden');
                        container.classList.remove('border-dashed', 'bg-gray-50');
                        container.classList.add('border-solid', 'border-gray-200', 'bg-white');

                        // 7. MAGIA: Reemplazamos el archivo pesado del <input> por nuestra versión ligera
                        fetch(resizedDataUrl)
                            .then(res => res.blob())
                            .then(blob => {
                                // Creamos un nuevo archivo ligero
                                const newFile = new File([blob], file.name.split('.')[0] + '.webp', { type: 'image/webp' });
                                
                                // Lo inyectamos en el input para que Laravel reciba este y no el gigante
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(newFile);
                                input.files = dataTransfer.files;
                            });
                    };
                    
                    // Cargamos la imagen original en memoria
                    img.src = e.target.result;
                };
                
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>