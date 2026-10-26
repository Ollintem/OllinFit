<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetCodeController extends Controller
{
    public function __construct(private PasswordResetCodeService $codes) {}

    /**
     * Paso 2: pantalla para escribir el código recibido.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset.email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-code', [
            'email' => $email,
            'resendIn' => $this->codes->resendAvailableIn($email),
            'expiresMinutes' => PasswordResetCodeService::EXPIRES_MINUTES,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset.email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Escribe el código de 6 dígitos.',
            'code.digits' => 'El código debe tener exactamente 6 dígitos.',
        ]);

        $result = $this->codes->verify($email, $request->input('code'));

        if ($result === PasswordResetCodeService::VALID) {
            $request->session()->put('password_reset.verified_at', now()->timestamp);

            return redirect()->route('password.reset');
        }

        $message = match ($result) {
            PasswordResetCodeService::EXPIRED => 'El código venció. Solicita uno nuevo.',
            PasswordResetCodeService::TOO_MANY => 'Demasiados intentos fallidos. Solicita un código nuevo.',
            default => 'Código incorrecto. Te quedan '.$this->codes->attemptsLeft($email).' intentos.',
        };

        return back()->withErrors(['code' => $message]);
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset.email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        if ($this->codes->resendAvailableIn($email) > 0) {
            return back()->withErrors(['code' => 'Espera unos segundos antes de pedir otro código.']);
        }

        $this->codes->issue($email);

        return back()->with('status', 'Te enviamos un código nuevo. Revisa tu bandeja de entrada.');
    }
}
