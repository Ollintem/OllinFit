<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Config de correo/WhatsApp desde la BD (panel de Integraciones). Tiene
        // prioridad sobre el .env; si no hay nada en BD, el .env queda de respaldo.
        $this->aplicarSettings();

        // Evaluación de permisos con denegaciones por empleado:
        // 1) El Administrador pasa todo (nunca se auto-bloquea).
        // 2) Si el permiso está DENEGADO para el empleado, se bloquea aunque su rol lo otorgue.
        // 3) El resto cae en la evaluación normal de Spatie (rol + permisos directos).
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Administrador')) {
                return true;
            }

            if ($user->hasDeniedPermission($ability)) {
                return false;
            }

            return null;
        });
    }

    /**
     * Sobrescribe la config de correo y WhatsApp con lo guardado en la tabla
     * settings (si existe y tiene valores). No rompe antes de migrar.
     */
    private function aplicarSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            // --- Correo ---
            if ($host = Setting::get('mail_host')) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => $host,
                    'mail.mailers.smtp.port' => (int) Setting::get('mail_port', 465),
                    'mail.mailers.smtp.scheme' => Setting::get('mail_scheme', 'smtps'),
                    'mail.mailers.smtp.username' => Setting::get('mail_username'),
                    'mail.mailers.smtp.password' => Setting::get('mail_password'),
                    'mail.from.address' => Setting::get('mail_from_address', config('mail.from.address')),
                    'mail.from.name' => Setting::get('mail_from_name', config('mail.from.name')),
                ]);
            }

            // --- WhatsApp ---
            $waEnabled = Setting::get('whatsapp_enabled');
            if ($waEnabled !== null) {
                config(['services.whatsapp.enabled' => filter_var($waEnabled, FILTER_VALIDATE_BOOLEAN)]);
            }
            if ($url = Setting::get('whatsapp_url')) {
                config(['services.whatsapp.url' => $url]);
            }
            if ($token = Setting::get('whatsapp_token')) {
                config(['services.whatsapp.token' => $token]);
            }
            if ($cc = Setting::get('whatsapp_country_code')) {
                config(['services.whatsapp.country_code' => $cc]);
            }
        } catch (\Throwable $e) {
            // Ante cualquier problema (p. ej. durante migraciones), usamos el .env.
        }
    }
}
