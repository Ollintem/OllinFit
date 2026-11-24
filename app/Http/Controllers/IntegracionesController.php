<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\WhatsappSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class IntegracionesController extends Controller
{
    public function index()
    {
        $correo = [
            'from_address' => Setting::get('mail_from_address', Setting::get('mail_username')),
            'password_set' => (bool) Setting::get('mail_password'),
        ];

        $whatsapp = [
            'enabled' => (bool) config('services.whatsapp.enabled'),
        ];

        return view('integraciones.index', compact('correo', 'whatsapp'));
    }

    /**
     * Guarda el correo saliente. El usuario solo escribe su correo y la
     * contraseña de aplicación; la parte técnica de Gmail la fijamos aquí.
     */
    public function guardarCorreo(Request $request)
    {
        $datos = $request->validate([
            'mail_from_address' => 'required|email|max:255',
            'mail_password' => 'nullable|string|max:255', // en blanco = conservar la actual
        ]);

        // Configuración técnica de Gmail (oculta para el usuario final).
        Setting::put('mail_host', 'smtp.gmail.com');
        Setting::put('mail_port', '465');
        Setting::put('mail_scheme', 'smtps');
        Setting::put('mail_username', $datos['mail_from_address']);
        Setting::put('mail_from_address', $datos['mail_from_address']);
        // Nombre del remitente: conservamos el que haya o usamos el del sistema.
        Setting::put('mail_from_name', Setting::get('mail_from_name', config('app.name', 'OllinFit')));

        // La contraseña (App Password) se cifra; si viene en blanco, se conserva la anterior.
        if ($request->filled('mail_password')) {
            // Quitamos espacios: las App Passwords de Google se muestran con espacios.
            Setting::put('mail_password', str_replace(' ', '', $datos['mail_password']), true);
        }

        return redirect()->route('integraciones.index')
            ->with('success', 'Correo guardado. Envía una prueba aquí abajo para confirmar que funciona.');
    }

    public function probarCorreo(Request $request)
    {
        $request->validate(['correo_prueba' => 'required|email']);

        try {
            Mail::raw('Correo de prueba de OllinFit. Si lees esto, el envío quedó bien configurado. 💪', function ($m) use ($request) {
                $m->to($request->correo_prueba)->subject('Prueba de correo — OllinFit');
            });

            return redirect()->route('integraciones.index')
                ->with('success', 'Correo de prueba enviado a ' . $request->correo_prueba . '. Revisa la bandeja (y spam).');
        } catch (\Throwable $e) {
            return redirect()->route('integraciones.index')
                ->with('error', 'No se pudo enviar: ' . $e->getMessage());
        }
    }

    /** Estado + QR del microservicio (lo consulta el panel cada pocos segundos). */
    public function estadoWhatsapp()
    {
        try {
            $resp = Http::withToken((string) config('services.whatsapp.token'))
                ->timeout(8)
                ->acceptJson()
                ->get(rtrim((string) config('services.whatsapp.url'), '/') . '/qr');

            if ($resp->failed()) {
                return response()->json(['reachable' => true, 'connected' => false, 'qr' => null]);
            }

            $data = $resp->json();
            return response()->json([
                'reachable' => true,
                'connected' => (bool) ($data['connected'] ?? false),
                'qr' => $data['qr'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // El microservicio no responde (apagado / no arrancado).
            return response()->json(['reachable' => false, 'connected' => false, 'qr' => null]);
        }
    }

    /**
     * "Conectar WhatsApp": habilita el envío y deja lista la configuración
     * técnica por defecto (sin que el usuario la vea). Tras esto, el panel
     * muestra el QR automáticamente para vincular el teléfono.
     */
    public function activarWhatsapp()
    {
        Setting::put('whatsapp_enabled', '1');

        if (! Setting::get('whatsapp_url')) {
            Setting::put('whatsapp_url', config('services.whatsapp.url', 'http://127.0.0.1:3000'));
        }
        if (! Setting::get('whatsapp_country_code')) {
            Setting::put('whatsapp_country_code', config('services.whatsapp.country_code', '52'));
        }

        return redirect()->route('integraciones.index')
            ->with('success', 'WhatsApp activado. Escanea el código con el teléfono del gimnasio para vincularlo.');
    }

    public function desvincularWhatsapp()
    {
        try {
            Http::withToken((string) config('services.whatsapp.token'))
                ->timeout(8)
                ->post(rtrim((string) config('services.whatsapp.url'), '/') . '/unlink');

            return redirect()->route('integraciones.index')->with('success', 'WhatsApp desvinculado. Vuelve a escanear el código para reconectar.');
        } catch (\Throwable $e) {
            return redirect()->route('integraciones.index')->with('error', 'No se pudo desvincular: ' . $e->getMessage());
        }
    }

    /** Envía un mensaje de WhatsApp de prueba a un número para verificar el envío. */
    public function probarWhatsapp(Request $request, WhatsappSender $whatsapp)
    {
        $request->validate(['telefono_prueba' => 'required|string|max:20']);

        if (! $whatsapp->habilitado()) {
            return redirect()->route('integraciones.index')
                ->with('error', 'Primero activa WhatsApp y vincula el teléfono escaneando el código.');
        }

        $ok = $whatsapp->enviarTexto(
            $request->telefono_prueba,
            '✅ Mensaje de prueba de OllinFit. Si lees esto, WhatsApp quedó bien configurado. 💪'
        );

        if ($ok) {
            return redirect()->route('integraciones.index')
                ->with('success', 'Mensaje de WhatsApp enviado a ' . $request->telefono_prueba . '. Revisa el teléfono.');
        }

        return redirect()->route('integraciones.index')
            ->with('error', 'No se pudo enviar. Verifica que el código esté escaneado (vinculado) y que el número tenga WhatsApp.');
    }
}
