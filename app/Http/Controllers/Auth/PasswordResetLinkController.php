<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Paso 1: formulario para pedir el código de recuperación.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Envía el código al correo y pasa al paso 2.
     * La respuesta es la misma exista o no la cuenta, para no revelar correos registrados.
     */
    public function store(Request $request, PasswordResetCodeService $codes): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = (string) $request->string('email')->trim();

        if ($codes->resendAvailableIn($email) === 0) {
            $codes->issue($email);
        }

        $request->session()->forget('password_reset');
        $request->session()->put('password_reset.email', $email);

        return redirect()->route('password.code')
            ->with('status', 'Si el correo está registrado, te enviamos un código de 6 dígitos.');
    }
}
