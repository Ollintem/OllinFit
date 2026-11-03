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

        <!-- ================= WHATSAPP (Etapa 2) ================= -->
        <div class="bg-white rounded-xl shadow-sm border border-dashed border-gray-300 p-8 text-center text-gray-400">
            <p class="text-sm font-semibold">WhatsApp</p>
            <p class="text-[13px]">La vinculación por QR desde esta pantalla se habilita en la siguiente etapa.</p>
        </div>

    </div>
</x-app-layout>
