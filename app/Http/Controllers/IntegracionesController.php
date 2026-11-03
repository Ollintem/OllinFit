<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class IntegracionesController extends Controller
{
    public function index()
    {
        $correo = [
            'host' => Setting::get('mail_host', config('mail.mailers.smtp.host')),
            'port' => Setting::get('mail_port', config('mail.mailers.smtp.port')),
            'scheme' => Setting::get('mail_scheme', config('mail.mailers.smtp.scheme') ?? 'smtps'),
            'username' => Setting::get('mail_username', config('mail.mailers.smtp.username')),
            'from_address' => Setting::get('mail_from_address', config('mail.from.address')),
            'from_name' => Setting::get('mail_from_name', config('mail.from.name')),
            'password_set' => (bool) Setting::get('mail_password'),
        ];

        return view('integraciones.index', compact('correo'));
    }

    public function guardarCorreo(Request $request)
    {
        $datos = $request->validate([
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_scheme' => 'required|in:smtp,smtps',
            'mail_username' => 'required|string|max:255',
            'mail_password' => 'nullable|string|max:255', // en blanco = conservar la actual
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        Setting::put('mail_host', $datos['mail_host']);
        Setting::put('mail_port', $datos['mail_port']);
        Setting::put('mail_scheme', $datos['mail_scheme']);
        Setting::put('mail_username', $datos['mail_username']);
        Setting::put('mail_from_address', $datos['mail_from_address']);
        Setting::put('mail_from_name', $datos['mail_from_name']);

        // La contraseña (App Password) se cifra; si viene en blanco, se conserva la anterior.
        if ($request->filled('mail_password')) {
            // Quitamos espacios: las App Passwords de Google se muestran con espacios.
            Setting::put('mail_password', str_replace(' ', '', $datos['mail_password']), true);
        }

        return redirect()->route('integraciones.index')->with('success', 'Configuración de correo guardada.');
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
}
