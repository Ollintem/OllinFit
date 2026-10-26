<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetCodeService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Paso 3: formulario de nueva contraseña (solo tras verificar el código).
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (! $this->verifiedEmail($request)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Tu verificación expiró. Solicita un código nuevo.']);
        }

        return view('auth.reset-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $email = $this->verifiedEmail($request);

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Tu verificación expiró. Solicita un código nuevo.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }

        $request->session()->forget('password_reset');

        return redirect()->route('login')
            ->with('status', '¡Listo! Tu contraseña se actualizó. Ya puedes iniciar sesión.');
    }

    /**
     * Correo verificado en el paso 2, válido mientras no pase el tiempo de expiración.
     */
    private function verifiedEmail(Request $request): ?string
    {
        $verifiedAt = $request->session()->get('password_reset.verified_at');

        if (! $verifiedAt || now()->timestamp - $verifiedAt > PasswordResetCodeService::EXPIRES_MINUTES * 60) {
            return null;
        }

        return $request->session()->get('password_reset.email');
    }
}
