<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 space-y-6">

        <div class="flex items-center gap-3 mb-2">
            <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Integraciones</h1>
                <p class="text-sm text-gray-500">Configura el correo y WhatsApp sin tocar el servidor.</p>
            </div>
        </div>

        {{-- Mensajes de éxito / error (incluye el resultado de la prueba de correo) --}}
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
                {{ session('success') }}
            </div>
        @endif
        <x-form-errors />

        <!-- ================= CORREO ================= -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8"
             x-data="{
                aplicarGmail() {
                    this.$refs.host.value = 'smtp.gmail.com';
                    this.$refs.port.value = '465';
                    this.$refs.scheme.value = 'smtps';
                }
             }">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <h2 class="text-base font-bold text-gray-800">Correo saliente</h2>
                </div>
                <button type="button" @click="aplicarGmail()" class="text-xs font-bold text-orange-500 hover:text-orange-600 border border-orange-200 rounded-lg px-3 py-1.5">
                    Usar Gmail
                </button>
            </div>
            <p class="text-[13px] text-gray-500 mb-6">Para Gmail: activa la verificación en 2 pasos y genera una <span class="font-semibold">Contraseña de aplicación</span> en <span class="font-mono text-[12px]">myaccount.google.com/apppasswords</span>. Pulsa “Usar Gmail” para autollenar lo técnico.</p>

            <form action="{{ route('integraciones.correo') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Correo remitente <span class="text-red-500">*</span></label>
                        <input type="email" name="mail_username" value="{{ old('mail_username', $correo['username']) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="tucorreo@gmail.com" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Contraseña de aplicación {{ $correo['password_set'] ? '' : '*' }}</label>
                        <input type="password" name="mail_password" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="{{ $correo['password_set'] ? '•••••• (guardada; deja en blanco para conservar)' : '16 letras, sin espacios' }}" {{ $correo['password_set'] ? '' : 'required' }}>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre remitente <span class="text-red-500">*</span></label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $correo['from_name']) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="OllinFit" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Correo "De" (From) <span class="text-red-500">*</span></label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $correo['from_address']) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="tucorreo@gmail.com" required>
                    </div>
                </div>

                <!-- Avanzado (lo autollena "Usar Gmail") -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Servidor SMTP</label>
                        <input type="text" name="mail_host" x-ref="host" value="{{ old('mail_host', $correo['host']) }}" class="w-full border-gray-200 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Puerto</label>
                        <input type="number" name="mail_port" x-ref="port" value="{{ old('mail_port', $correo['port']) }}" class="w-full border-gray-200 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Esquema</label>
                        <select name="mail_scheme" x-ref="scheme" class="w-full border-gray-200 rounded-lg text-sm">
                            <option value="smtps" {{ old('mail_scheme', $correo['scheme']) === 'smtps' ? 'selected' : '' }}>smtps (465)</option>
                            <option value="smtp" {{ old('mail_scheme', $correo['scheme']) === 'smtp' ? 'selected' : '' }}>smtp / STARTTLS (587)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        Guardar correo
                    </button>
                </div>
            </form>

            <!-- Enviar correo de prueba -->
            <form action="{{ route('integraciones.correo.prueba') }}" method="POST" class="mt-6 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-end gap-3">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Enviar un correo de prueba a</label>
                    <input type="email" name="correo_prueba" value="{{ old('correo_prueba', $correo['from_address']) }}" class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="correo@donde-quieres-probar.com" required>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap">
                    Enviar prueba
                </button>
            </form>
            <p class="text-[12px] text-gray-400 mt-2">Guarda primero la configuración y luego envía la prueba para verificar que el correo funciona.</p>
        </div>

        <!-- ================= WHATSAPP ================= -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"></path></svg>
                <h2 class="text-base font-bold text-gray-800">WhatsApp</h2>
            </div>
            <p class="text-[13px] text-gray-500 mb-6">Vincula el número del gimnasio escaneando el código con el teléfono. El envío usa un microservicio que debe estar corriendo en el servidor.</p>

            <!-- Estado + QR en vivo -->
            <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-6 mb-6 text-center"
                 x-data="{ reachable:true, connected:false, qr:null, cargando:true,
                    async poll(){ try { const r = await fetch('{{ route('integraciones.whatsapp.estado') }}', {headers:{'Accept':'application/json'}}); const d = await r.json(); this.reachable=d.reachable; this.connected=d.connected; this.qr=d.qr; } catch(e){ this.reachable=false; } this.cargando=false; } }"
                 x-init="poll(); setInterval(() => poll(), 4000)">

                <template x-if="cargando">
                    <p class="text-sm text-gray-400">Consultando estado…</p>
                </template>

                <template x-if="!cargando && !reachable">
                    <div class="text-amber-700">
                        <p class="text-sm font-bold">⚠️ El microservicio no responde</p>
                        <p class="text-[13px] mt-1">Arráncalo en el servidor (<span class="font-mono">cd whatsapp-service &amp;&amp; npm start</span>) y revisa la URL de abajo.</p>
                    </div>
                </template>

                <template x-if="!cargando && reachable && connected">
                    <div class="text-emerald-600">
                        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold">WhatsApp vinculado y listo</p>
                        <form action="{{ route('integraciones.whatsapp.desvincular') }}" method="POST" class="mt-3"
                              onsubmit="return confirm('¿Desvincular el WhatsApp? Tendrás que volver a escanear el QR.')">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-600 border border-red-200 rounded-lg px-3 py-1.5">Desvincular</button>
                        </form>
                    </div>
                </template>

                <template x-if="!cargando && reachable && !connected && qr">
                    <div>
                        <p class="text-sm font-bold text-gray-700 mb-3">Escanea este código</p>
                        <img :src="qr" alt="QR de WhatsApp" class="mx-auto w-56 h-56 rounded-lg border border-gray-200 bg-white p-2">
                        <p class="text-[13px] text-gray-500 mt-3">En el teléfono: WhatsApp → <span class="font-semibold">Dispositivos vinculados</span> → Vincular un dispositivo.</p>
                    </div>
                </template>

                <template x-if="!cargando && reachable && !connected && !qr">
                    <p class="text-sm text-gray-500">Esperando el código QR del microservicio… (si no aparece en unos segundos, reinícialo).</p>
                </template>
            </div>

            <!-- Ajustes del microservicio -->
            <form action="{{ route('integraciones.whatsapp') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="whatsapp_enabled" value="0">
                    <input type="checkbox" name="whatsapp_enabled" value="1" class="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500" {{ $whatsapp['enabled'] ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Enviar avisos por WhatsApp (bienvenida y recordatorios)</span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 mb-1">URL del microservicio</label>
                        <input type="url" name="whatsapp_url" value="{{ old('whatsapp_url', $whatsapp['url']) }}" class="w-full border-gray-200 rounded-lg text-sm" placeholder="http://127.0.0.1:3000" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Lada del país</label>
                        <input type="text" name="whatsapp_country_code" value="{{ old('whatsapp_country_code', $whatsapp['country_code']) }}" class="w-full border-gray-200 rounded-lg text-sm" placeholder="52" required>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-500 mb-1">Token del microservicio</label>
                        <input type="password" name="whatsapp_token" class="w-full border-gray-200 rounded-lg text-sm" placeholder="{{ $whatsapp['token_set'] ? '•••••• (guardado; deja en blanco para conservar)' : 'mismo token del servicio (API_TOKEN)' }}">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        Guardar WhatsApp
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
