<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('socios.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Registrar Nuevo Socio</h1>
        </div>

        <form action="{{ route('socios.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Tarjeta 1: Datos del Socio -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                
                <div class="flex items-center gap-2 mb-6">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    <h2 class="text-base font-bold text-gray-800">Datos del Socio</h2>
                </div>
                <!-- Input Foto (Cámara en vivo) -->
                <div class="flex items-center gap-6 mb-8 border-b border-gray-100 pb-8">
                    <!-- Contenedor Interactivo -->
                    <div id="camera-container" class="w-24 h-24 rounded-full border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden relative cursor-pointer group shadow-sm transition-all hover:border-orange-500">
                        
                        <!-- Ícono inicial -->
                        <div id="photo-placeholder" class="text-gray-400 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>

                        <!-- Video en vivo (Oculto al inicio) -->
                        <video id="camera-stream" class="absolute inset-0 w-full h-full object-cover hidden" autoplay playsinline></video>
                        
                        <!-- Previsualización de la foto capturada (Oculta al inicio) -->
                        <img id="photo-preview" class="absolute inset-0 w-full h-full object-cover hidden">
                        
                        <!-- Input real (Oculto) -->
                        <input type="file" name="photo" id="photo-input" class="hidden" accept="image/png, image/jpeg">
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Fotografía del Socio</h3>
                        <p class="text-[12px] text-gray-400 mt-0.5 mb-2" id="camera-instructions">Haz clic en el círculo para encender la cámara.</p>
                        
                        <!-- Controles de la cámara -->
                        <div id="camera-controls" class="hidden flex items-center gap-2 mt-1">
                            <button type="button" id="btn-capture" class="bg-orange-500 hover:bg-orange-600 text-white text-[11px] font-bold px-3 py-1.5 rounded transition-colors shadow-sm">Tomar Foto</button>
                            <button type="button" id="btn-cancel" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-[11px] font-bold px-3 py-1.5 rounded transition-colors">Cancelar</button>
                        </div>
                        <button type="button" id="btn-retake" class="hidden text-[12px] font-semibold text-orange-500 hover:text-orange-600 mt-1">Volver a tomar foto</button>
                    </div>
                </div>

                <!-- Grid de Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Folio (Lectura) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Folio (Autogenerado)</label>
                        <input type="text" name="folio" value="{{ $folio }}" class="w-full bg-gray-50 border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly>
                    </div>
                    <!-- Fecha (Lectura) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Fecha de Registro</label>
                        <input type="text" value="{{ $fechaRegistro }}" class="w-full bg-gray-50 border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly>
                    </div>

                    <!-- Nombres -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nombre(s) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. Carlos" required>
                    </div>
                    <!-- Apellidos -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. Méndez" required>
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Teléfono Celular <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="10 dígitos" required>
                    </div>
                    <!-- Correo -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="email" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="correo@ejemplo.com">
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2: Asignación de Membresía -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <h2 class="text-base font-bold text-gray-800">Asignación de Membresía</h2>
                </div>
                <p class="text-[13px] text-gray-500 mb-6">Selecciona el plan inicial para este socio. El pago se registrará en el corte de caja de hoy.</p>

                <!-- Grid de Planes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @foreach($planes as $plan)
                        <label class="plan-card relative flex flex-col p-5 border border-gray-200 rounded-xl cursor-pointer hover:border-orange-300 transition-all">
                            <!-- Input Radio Oculto -->
                            <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only" data-price="{{ $plan->price }}" data-days="{{ $plan->duration_days }}">
                            
                            <!-- Círculo de selección -->
                            <div class="absolute top-5 right-5 w-4 h-4 rounded-full border border-gray-300 peer-checked:border-orange-500 peer-checked:border-[4px] transition-all bg-white"></div>
                            
                            <!-- Info del Plan -->
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-gray-800 text-sm">{{ $plan->name }}</span>
                                @if($plan->highlight_text)
                                    <span class="bg-emerald-100 text-emerald-700 text-[9px] uppercase font-bold px-1.5 py-0.5 rounded tracking-wide">AHORRO</span>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <span class="text-xl font-extrabold text-orange-500">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-xs font-bold text-gray-400">MXN</span>
                            </div>

                            <p class="text-[12px] text-gray-500 flex-1 mb-4 leading-relaxed">{{ $plan->description }}</p>

                            <div class="flex items-center gap-1.5 text-[11px] text-gray-400 font-medium border-t border-gray-100 pt-3">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Vigencia: {{ $plan->duration_days }} días
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Caja de Resumen interactiva -->
                <div class="bg-gray-50/80 rounded-xl p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-gray-100">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 mb-1">Total a cobrar hoy:</span>
                        <span id="summary-total" class="text-xl font-extrabold text-gray-800">$0.00 MXN</span>
                    </div>
                    <div class="text-left md:text-right">
                        <span class="block text-xs font-semibold text-gray-500 mb-1">Próximo vencimiento:</span>
                        <span id="summary-date" class="text-sm font-bold text-emerald-600">- Selecciona un plan -</span>
                    </div>
                </div>

            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-4 pb-10">
                <a href="{{ route('socios.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Registrar y Cobrar
                </button>
            </div>
        </form>
    </div>

    <!-- Script para cálculos dinámicos de los planes -->
    <script>

        // ==========================================
            // LÓGICA DE LA CÁMARA (WebRTC)
            // ==========================================
            const cameraContainer = document.getElementById('camera-container');
            const videoStream = document.getElementById('camera-stream');
            const photoPreview = document.getElementById('photo-preview');
            const photoPlaceholder = document.getElementById('photo-placeholder');
            const photoInput = document.getElementById('photo-input');
            const cameraControls = document.getElementById('camera-controls');
            const btnCapture = document.getElementById('btn-capture');
            const btnCancel = document.getElementById('btn-cancel');
            const btnRetake = document.getElementById('btn-retake');
            const instructions = document.getElementById('camera-instructions');
            
            let stream = null;

            // 1. Encender la cámara al hacer clic en el círculo
            cameraContainer.addEventListener('click', async function() {
                // Solo si no hay video activo y no hay foto tomada
                if (videoStream.classList.contains('hidden') && photoPreview.classList.contains('hidden')) {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                        videoStream.srcObject = stream;
                        
                        // Cambiar UI
                        photoPlaceholder.classList.add('hidden');
                        videoStream.classList.remove('hidden');
                        cameraControls.classList.remove('hidden');
                        cameraContainer.classList.remove('cursor-pointer');
                        instructions.textContent = 'Encuadra el rostro y haz clic en "Tomar Foto".';
                    } catch (err) {
                        alert("Error al acceder a la cámara. Asegúrate de dar los permisos en tu navegador.");
                    }
                }
            });

            // 2. Apagar cámara (Función auxiliar)
            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            }

            // 3. Botón "Cancelar"
            btnCancel.addEventListener('click', function(e) {
                e.stopPropagation();
                stopCamera();
                videoStream.classList.add('hidden');
                cameraControls.classList.add('hidden');
                photoPlaceholder.classList.remove('hidden');
                cameraContainer.classList.add('cursor-pointer');
                instructions.textContent = 'Haz clic en el círculo para encender la cámara.';
            });

            // 4. Botón "Tomar Foto"
            btnCapture.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Dibujar el fotograma en un canvas invisible
                const canvas = document.createElement('canvas');
                canvas.width = videoStream.videoWidth;
                canvas.height = videoStream.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(videoStream, 0, 0, canvas.width, canvas.height);
                
                // El navegador convierte la foto a WebP al 80% de calidad
                const dataURL = canvas.toDataURL('image/webp', 0.8);
                
                // Mostrar previsualización
                photoPreview.src = dataURL;
                photoPreview.classList.remove('hidden');
                
                // Apagar video y ocultar controles
                stopCamera();
                videoStream.classList.add('hidden');
                cameraControls.classList.add('hidden');
                btnRetake.classList.remove('hidden');
                instructions.textContent = 'Fotografía capturada correctamente.';

                // Empaquetar el archivo WebP para mandarlo al servidor
                let arr = dataURL.split(','), mime = arr[0].match(/:(.*?);/)[1];
                let bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                while(n--) { u8arr[n] = bstr.charCodeAt(n); }
                
                // Ya le decimos que es un archivo .webp
                let file = new File([u8arr], "captura_webcam.webp", {type: "image/webp"});
                
                // Inyectar el archivo en el input oculto
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                photoInput.files = dataTransfer.files;
            });

            // 5. Botón "Volver a tomar foto"
            btnRetake.addEventListener('click', function(e) {
                e.stopPropagation();
                photoPreview.classList.add('hidden');
                photoPreview.src = '';
                photoInput.value = ''; // Limpiar input
                btnRetake.classList.add('hidden');
                
                // Simular clic en el contenedor para reabrir cámara
                cameraContainer.click();
            });
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('input[name="plan_id"]');
            const summaryTotal = document.getElementById('summary-total');
            const summaryDate = document.getElementById('summary-date');
            
            // Opciones para formatear la fecha a español
            const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    
                    // 1. Efecto visual de la tarjeta seleccionada
                    document.querySelectorAll('.plan-card').forEach(card => {
                        card.classList.remove('border-orange-500', 'bg-orange-50/30', 'ring-1', 'ring-orange-500');
                    });
                    
                    if(this.checked) {
                        this.closest('.plan-card').classList.add('border-orange-500', 'bg-orange-50/30', 'ring-1', 'ring-orange-500');
                        
                        // 2. Actualizar el precio en el resumen
                        const price = parseFloat(this.dataset.price).toLocaleString('es-MX', {minimumFractionDigits: 2});
                        summaryTotal.textContent = '$' + price + ' MXN';

                        // 3. Calcular la fecha de vencimiento sumando los días a HOY
                        const days = parseInt(this.dataset.days);
                        const expirationDate = new Date();
                        expirationDate.setDate(expirationDate.getDate() + days);
                        
                        summaryDate.textContent = expirationDate.toLocaleDateString('es-MX', dateOptions);
                    }
                });
            });
        });
    </script>
</x-app-layout>