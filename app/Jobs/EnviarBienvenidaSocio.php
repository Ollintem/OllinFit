<?php

namespace App\Jobs;

use App\Models\Member;
use App\Notifications\BienvenidaSocio;
use App\Services\WhatsappSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Envía (en segundo plano) la bienvenida al socio recién creado por DOS canales:
 *  - Correo: si el socio tiene email (y el mailer está configurado para enviar).
 *  - WhatsApp: mensaje + QR, si WHATSAPP_ENABLED y el socio tiene teléfono.
 * Va encolado para que el alta del socio nunca se bloquee ni falle por un aviso.
 */
class EnviarBienvenidaSocio implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $memberId) {}

    public function handle(WhatsappSender $whatsapp): void
    {
        $socio = Member::with('plan')->find($this->memberId);
        if (! $socio) {
            return;
        }

        // 1. Correo de bienvenida (nativo de Laravel).
        if ($socio->email) {
            try {
                $socio->notify(new BienvenidaSocio());
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Bienvenida: no se pudo enviar el correo.', ['error' => $e->getMessage()]);
            }
        }

        // 2. WhatsApp con el QR (si está habilitado y hay teléfono).
        if ($whatsapp->habilitado() && $socio->phone) {
            $plan = $socio->plan->name ?? 'tu membresía';
            $vigencia = $socio->expiration_date?->format('d/m/Y');

            $mensaje = "¡Hola {$socio->name}! 👋 Bienvenido(a) a *OllinFit*.\n\n"
                . "Tu membresía *{$plan}* quedó activa"
                . ($vigencia ? " con vigencia hasta el {$vigencia}" : '') . ".\n"
                . "Tu folio es *{$socio->folio}*.\n\n"
                . "Te compartimos tu código QR de acceso: muéstralo en recepción para entrar al gimnasio. 💪";

            $whatsapp->enviarConQr($socio->phone, $mensaje, $socio->folio);
        }
    }
}
