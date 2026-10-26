<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PasswordResetCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Recuperación de contraseña con código de 6 dígitos.
 * El código se guarda hasheado en la tabla password_reset_tokens (una fila por email).
 */
class PasswordResetCodeService
{
    public const EXPIRES_MINUTES = 15;
    public const MAX_ATTEMPTS = 5;
    public const RESEND_SECONDS = 60;

    public const VALID = 'valid';
    public const INVALID = 'invalid';
    public const EXPIRED = 'expired';
    public const TOO_MANY = 'too_many';

    /**
     * Genera y envía un código. Si el correo no pertenece a ningún usuario no hace nada,
     * para no revelar qué cuentas existen.
     */
    public function issue(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return;
        }

        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        RateLimiter::clear($this->attemptsKey($email));
        RateLimiter::hit($this->resendKey($email), self::RESEND_SECONDS);

        $user->notify(new PasswordResetCode($code, self::EXPIRES_MINUTES));
    }

    public function verify(string $email, string $code): string
    {
        $key = $this->attemptsKey($email);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            return self::TOO_MANY;
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($record && Carbon::parse($record->created_at)->addMinutes(self::EXPIRES_MINUTES)->isPast()) {
            $this->forget($email);

            return self::EXPIRED;
        }

        if (! $record || ! Hash::check($code, $record->token)) {
            RateLimiter::hit($key, self::EXPIRES_MINUTES * 60);

            if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
                // Se agotaron los intentos: el código queda inutilizado
                $this->forget($email);

                return self::TOO_MANY;
            }

            return self::INVALID;
        }

        $this->forget($email);
        RateLimiter::clear($key);

        return self::VALID;
    }

    public function attemptsLeft(string $email): int
    {
        return RateLimiter::remaining($this->attemptsKey($email), self::MAX_ATTEMPTS);
    }

    /** Segundos que faltan para poder reenviar el código. */
    public function resendAvailableIn(string $email): int
    {
        return RateLimiter::tooManyAttempts($this->resendKey($email), 1)
            ? RateLimiter::availableIn($this->resendKey($email))
            : 0;
    }

    public function forget(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }

    private function attemptsKey(string $email): string
    {
        return 'password-code-attempts:'.strtolower($email);
    }

    private function resendKey(string $email): string
    {
        return 'password-code-resend:'.strtolower($email);
    }
}
