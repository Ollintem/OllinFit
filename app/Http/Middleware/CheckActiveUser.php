<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está logueado pero su cuenta NO está activa (is_active = false)
        if (Auth::check() && !Auth::user()->is_active) {
            
            Auth::logout(); // Cerramos su sesión inmediatamente
            
            // Destruimos las cookies y sesiones de seguridad
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Lo regresamos al login con un mensaje de alerta rojo
            return redirect()->route('login')->withErrors([
                'email' => 'Tu acceso ha sido revocado. Contacta al administrador.',
            ]);
        }

        // Si todo está bien, lo dejamos pasar
        return $next($request);
    }
}