<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        
        <div class="flex items-center justify-between mb-4 border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Control de Acceso</h1>
                <p class="text-sm text-gray-500">Escanea el Gafete Digital del socio para registrar entrada o salida.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Columna Izquierda: Escáner QR -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <!-- Mensajes de Alerta Dinámicos -->
                    <div id="alert-box" class="hidden mb-4 p-4 rounded-lg font-bold text-sm text-center transition-all"></div>

                    <!-- Contenedor de la Cámara -->
                    <div id="reader" class="w-full rounded-xl overflow-hidden border-2 border-dashed border-gray-300"></div>
                </div>
            </div>

            <!-- Columna Derecha: Socios Adentro -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
                    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h2 class="font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Socios en Instalaciones
                        </h2>
                        <span class="bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $sociosAdentro->count() }}</span>
                    </div>

                    <div class="p-0 overflow-y-auto max-h-[500px]">
                        @forelse($sociosAdentro as $socio)
                            <div class="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $socio->name }} {{ $socio->last_name }}</p>
                                    <p class="text-xs text-gray-400">Folio: #{{ $socio->folio }}</p>
                                </div>
                                <!-- Botón de Emergencia (Liberar) -->
                                <form action="{{ route('acceso.release', $socio->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold text-red-500 hover:text-red-700 bg-red-50 px-2 py-1 rounded border border-red-100 transition-colors" title="Forzar Salida">
                                        Liberar
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400 text-sm">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                El gimnasio está vacío en este momento.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Librería HTML5 QR Code -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('alert-box');
            let isScanning = false; // Evita escaneos múltiples por accidente

            // Mostrar mensajes en pantalla
            function showAlert(message, type) {
                alertBox.textContent = message;
                alertBox.className = 'mb-4 p-4 rounded-lg font-bold text-sm text-center transition-all block';
                
                if (type === 'success') {
                    alertBox.classList.add('bg-emerald-100', 'text-emerald-800', 'border', 'border-emerald-200');
                } else if (type === 'error') {
                    alertBox.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-200');
                }

                // Ocultar la alerta después de 4 segundos
                setTimeout(() => {
                    alertBox.classList.add('hidden');
                    alertBox.className = 'hidden mb-4 p-4 rounded-lg font-bold text-sm text-center transition-all';
                }, 4000);
            }

            // Función que se ejecuta cuando la cámara detecta un QR
            function onScanSuccess(decodedText, decodedResult) {
                if(isScanning) return;
                isScanning = true;

                // Hacer la petición AJAX a nuestro AccessController
                fetch('{{ route("acceso.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Token de seguridad de Laravel
                    },
                    body: JSON.stringify({ folio: decodedText })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Recargar la página después de 2 segundos para actualizar la lista de "Socios Adentro"
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        showAlert(data.message, 'error');
                        setTimeout(() => isScanning = false, 3000); // Reactivar escáner
                    }
                })
                .catch(error => {
                    showAlert('Error de conexión. Intente nuevamente.', 'error');
                    setTimeout(() => isScanning = false, 3000);
                });
            }

            // Iniciar la Cámara
            const html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", 
                { fps: 10, qrbox: {width: 250, height: 250} },
                /* verbose= */ false
            );
            
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
</x-app-layout>