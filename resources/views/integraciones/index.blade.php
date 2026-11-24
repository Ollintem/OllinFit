<x-app-layout>
    <div class="max-w-2xl mx-auto p-6 space-y-6">

        <div class="flex items-center gap-3 mb-2">
            <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Integraciones</h1>
                <p class="text-sm text-gray-500">Conecta el correo y el WhatsApp del gimnasio para enviar avisos a tus socios.</p>
            </div>
        </div>

        {{-- Mensajes de éxito / error (incluye el resultado de las pruebas) --}}
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        <x-form-errors />

        <!-- ================= CORREO ================= -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <h2 class="text-base font-bold text-gray-800">Correo</h2>
            </div>
            <p class="text-[13px] text-gray-500 mb-6">Escribe el correo de <strong>Gmail</strong> desde el que se enviarán los mensajes a tus socios.</p>

            <!-- Paso 1: el correo que envía -->
            <form action="{{ route('integraciones.correo') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Correo que enviará los mensajes <span class="text-red-500">*</span></label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $correo['from_address']) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="elgimnasio@gmail.com" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Contraseña de aplicación {{ $correo['password_set'] ? '' : '*' }}</label>
                    <input type="password" name="mail_password" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="{{ $correo['password_set'] ? '•••••••• (ya guardada; déjala en blanco para conservarla)' : 'Pega aquí la clave de 16 letras' }}" {{ $correo['password_set'] ? '' : 'required' }}>
                    <p class="text-[12px] text-gray-400 mt-1.5 leading-relaxed">
                        No es tu contraseña normal de Gmail. Es una <strong>Contraseña de aplicación</strong> que generas una vez en
                        <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-orange-500 font-semibold hover:underline">myaccount.google.com/apppasswords</a>
                        (requiere tener activada la verificación en 2 pasos).
                    </p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm">
                        Guardar correo
                    </button>
                </div>
            </form>

            <!-- Paso 2: enviar un correo de prueba a otro correo -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-1">Probar el envío</h3>
                <p class="text-[12px] text-gray-400 mb-3">Manda un correo de prueba a donde quieras para confirmar que todo funciona. (Guarda primero el correo de arriba.)</p>
                <form action="{{ route('integraciones.correo.prueba') }}" method="POST" class="flex flex-col sm:flex-row items-end gap-3">
                    @csrf
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Enviar prueba a este correo</label>
                        <input type="email" name="correo_prueba" value="{{ old('correo_prueba') }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="correo-para-probar@ejemplo.com" required>
                    </div>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap">
                        Enviar prueba
                    </button>
                </form>
            </div>
        </div>

        <!-- ================= WHATSAPP ================= -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"></path></svg>
                <h2 class="text-base font-bold text-gray-800">WhatsApp</h2>
            </div>
            <p class="text-[13px] text-gray-500 mb-6">Vincula el WhatsApp del gimnasio para enviar la bienvenida y los recordatorios.</p>

            @if (! $whatsapp['enabled'])
                <!-- Estado inicial: aún no se ha activado -->
                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50/60 p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-emerald-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"></path></svg>
                    <p class="text-sm text-gray-600 mb-4">Pulsa el botón para encender WhatsApp y mostrar el código para vincular el teléfono.</p>
                    <form action="{{ route('integraciones.whatsapp') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-xl text-sm font-bold transition-colors shadow-md shadow-emerald-500/20 inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Conectar WhatsApp
                        </button>
                    </form>
                </div>
            @else
                <!-- Ya activado: mostramos estado + QR en vivo -->
                <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-6 text-center"
                     x-data="{ reachable:true, connected:false, qr:null, cargando:true,
                        async poll(){ try { const r = await fetch('{{ route('integraciones.whatsapp.estado') }}', {headers:{'Accept':'application/json'}}); const d = await r.json(); this.reachable=d.reachable; this.connected=d.connected; this.qr=d.qr; } catch(e){ this.reachable=false; } this.cargando=false; } }"
                     x-init="poll(); setInterval(() => poll(), 4000)">

                    <template x-if="cargando">
                        <p class="text-sm text-gray-400">Encendiendo WhatsApp…</p>
                    </template>

                    <template x-if="!cargando && !reachable">
                        <div class="text-amber-700">
                            <p class="text-sm font-bold">El servicio de WhatsApp no está disponible</p>
                            <p class="text-[13px] mt-1 text-amber-600">Espera unos segundos a que encienda. Si no aparece, contacta a soporte técnico.</p>
                        </div>
                    </template>

                    <template x-if="!cargando && reachable && connected">
                        <div class="text-emerald-600">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-base font-bold">WhatsApp vinculado y listo ✅</p>
                            <p class="text-[13px] text-gray-500 mt-1">Ya se enviarán los mensajes desde el WhatsApp del gimnasio.</p>
                        </div>
                    </template>

                    <template x-if="!cargando && reachable && !connected && qr">
                        <div>
                            <p class="text-sm font-bold text-gray-700 mb-3">Escanea este código con el teléfono del gimnasio</p>
                            <img :src="qr" alt="Código QR de WhatsApp" class="mx-auto w-56 h-56 rounded-lg border border-gray-200 bg-white p-2">
                            <p class="text-[13px] text-gray-500 mt-3">En el teléfono: WhatsApp → <span class="font-semibold">Dispositivos vinculados</span> → <span class="font-semibold">Vincular un dispositivo</span>.</p>
                        </div>
                    </template>

                    <template x-if="!cargando && reachable && !connected && !qr">
                        <p class="text-sm text-gray-500">Preparando el código para vincular… aparecerá en unos segundos.</p>
                    </template>
                </div>

                <!-- Probar WhatsApp + Desvincular -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 mb-1">Probar el envío</h3>
                    <p class="text-[12px] text-gray-400 mb-3">Manda un WhatsApp de prueba a un número (a 10 dígitos) para confirmar que funciona. Debe estar vinculado (código escaneado).</p>
                    <form action="{{ route('integraciones.whatsapp.prueba') }}" method="POST" class="flex flex-col sm:flex-row items-end gap-3">
                        @csrf
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-gray-600 mb-1">Enviar prueba a este número</label>
                            <input type="text" name="telefono_prueba" value="{{ old('telefono_prueba') }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="10 dígitos" required>
                        </div>
                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap">
                            Enviar prueba
                        </button>
                    </form>

                    <form action="{{ route('integraciones.whatsapp.desvincular') }}" method="POST" class="mt-4 text-right"
                          onsubmit="return confirm('¿Desvincular el WhatsApp? Tendrás que volver a escanear el código para reconectar.')">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-600">Desvincular este WhatsApp</button>
                    </form>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
