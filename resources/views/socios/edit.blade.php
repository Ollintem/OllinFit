<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">

        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            <!-- Encabezado Oscuro -->
            <div class="bg-zinc-900 px-8 py-6 text-white flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    Editar Socio
                </h2>
                <a href="{{ route('socios.index') }}" class="text-xs font-bold bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg border border-zinc-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Volver
                </a>
            </div>

            <div class="p-8">
                <p class="text-[13px] text-gray-500 mb-6">Actualizando los datos de: <span class="font-bold text-orange-500">{{ $member->name }} {{ $member->last_name }}</span></p>

                <form action="{{ route('socios.update', $member->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <x-form-errors />
                    @method('PUT')

                    <!-- SECCIÓN 1: Fotografía y Contexto -->
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 border-b border-gray-100 pb-8">

                        <!-- Módulo de Cámara Interactiva -->
                        <div class="flex flex-col items-center sm:items-start gap-4">
                            <div id="camera-container" class="w-28 h-28 rounded-full border-4 border-gray-100 bg-gray-50 flex items-center justify-center overflow-hidden relative cursor-pointer group shadow-sm transition-all hover:border-orange-500">

                                <!-- Foto actual o Placeholder -->
                                <div id="photo-placeholder" class="w-full h-full relative">
                                    @if($member->profile_photo_path)
                                        <img src="{{ asset('storage/' . $member->profile_photo_path) }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center backdrop-blur-sm transition-all">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400 group-hover:scale-110 transition-transform">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Video en vivo (Oculto) -->
                                <video id="camera-stream" class="absolute inset-0 w-full h-full object-cover hidden" autoplay playsinline></video>

                                <!-- Previsualización de la nueva foto (Oculta) -->
                                <img id="photo-preview" class="absolute inset-0 w-full h-full object-cover hidden">

                                <!-- Input real (Oculto) -->
                                <input type="file" name="photo" id="photo-input" class="hidden" accept="image/png, image/jpeg, image/webp">
                            </div>

                            <div class="text-center sm:text-left">
                                <p class="text-[12px] font-bold text-gray-500 uppercase tracking-wide">Fotografía</p>
                                <p class="text-[11px] text-gray-400 mt-0.5" id="camera-instructions">Clic en la imagen para actualizar.</p>

                                <!-- Controles -->
                                <div id="camera-controls" class="hidden flex items-center justify-center sm:justify-start gap-2 mt-2">
                                    <button type="button" id="btn-capture" class="bg-orange-500 hover:bg-orange-600 text-white text-[11px] font-bold px-3 py-1.5 rounded transition-colors shadow-sm">Capturar</button>
                                    <button type="button" id="btn-cancel" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-[11px] font-bold px-3 py-1.5 rounded transition-colors">Cancelar</button>
                                </div>
                                <button type="button" id="btn-retake" class="hidden text-[11px] font-bold text-orange-500 hover:text-orange-600 mt-2">Tomar otra vez</button>
                            </div>
                        </div>

                        <!-- Datos de Contexto (Solo lectura) -->
                        <div class="flex-1 bg-gray-50 rounded-xl p-5 border border-gray-100 w-full">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Información de Sistema</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Folio</span>
                                    <span class="text-sm font-bold text-gray-800 bg-white px-3 py-1.5 rounded border border-gray-200 block shadow-sm">#{{ $member->folio }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Estado de Membresía</span>
                                    <div class="mt-1.5">
                                        @if($member->status === 'Activo')
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-lg border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-lg border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> {{ $member->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Datos Personales -->
                    <div>
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Datos Personales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Nombre -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Nombre(s) <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                                    class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                            </div>

                            <!-- Apellidos -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Apellidos <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}" required
                                    class="w-full border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Teléfono Celular</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                                        class="w-full pl-9 border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">Correo Electrónico</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email', $member->email) }}"
                                        class="w-full pl-9 border-gray-200 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('socios.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-colors text-sm text-center">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl hover:bg-orange-600 font-bold shadow-md shadow-orange-500/20 transition-all text-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script de la Cámara -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            cameraContainer.addEventListener('click', async function() {
                if (videoStream.classList.contains('hidden') && photoPreview.classList.contains('hidden')) {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                        videoStream.srcObject = stream;

                        photoPlaceholder.classList.add('hidden');
                        videoStream.classList.remove('hidden');
                        cameraControls.classList.remove('hidden');
                        cameraContainer.classList.remove('cursor-pointer');
                        instructions.textContent = 'Encuadra y haz clic en Capturar.';
                    } catch (err) {
                        alert("Error al acceder a la cámara. Asegúrate de dar permisos.");
                    }
                }
            });

            function stopCamera() {
                if (stream) { stream.getTracks().forEach(track => track.stop()); }
            }

            btnCancel.addEventListener('click', function(e) {
                e.stopPropagation();
                stopCamera();
                videoStream.classList.add('hidden');
                cameraControls.classList.add('hidden');
                photoPlaceholder.classList.remove('hidden');
                cameraContainer.classList.add('cursor-pointer');
                instructions.textContent = 'Clic en la imagen para actualizar.';
            });

            btnCapture.addEventListener('click', function(e) {
                e.stopPropagation();

                const canvas = document.createElement('canvas');
                canvas.width = videoStream.videoWidth;
                canvas.height = videoStream.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(videoStream, 0, 0, canvas.width, canvas.height);

                const dataURL = canvas.toDataURL('image/webp', 0.8);

                photoPreview.src = dataURL;
                photoPreview.classList.remove('hidden');

                stopCamera();
                videoStream.classList.add('hidden');
                cameraControls.classList.add('hidden');
                btnRetake.classList.remove('hidden');
                instructions.textContent = 'Nueva fotografía lista.';

                let arr = dataURL.split(','), mime = arr[0].match(/:(.*?);/)[1];
                let bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                while(n--) { u8arr[n] = bstr.charCodeAt(n); }

                let file = new File([u8arr], "captura_webcam.webp", {type: "image/webp"});
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                photoInput.files = dataTransfer.files;
            });

            btnRetake.addEventListener('click', function(e) {
                e.stopPropagation();
                photoPreview.classList.add('hidden');
                photoPreview.src = '';
                photoInput.value = '';
                btnRetake.classList.add('hidden');
                cameraContainer.click();
            });
        });
    </script>
</x-app-layout>
