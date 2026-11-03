<?php

namespace App\Jobs;

use App\Models\Member;
use App\Services\WhatsappSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Envía (en segundo plano) el mensaje de bienvenida + QR al socio recién creado.
 * Va encolado para que el alta del socio nunca se bloquee ni falle por WhatsApp.
 */
class EnviarBienvenidaWhatsapp implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $memberId) {}

    public function handle(WhatsappSender $whatsapp): void
    {
        if (! $whatsapp->habilitado()) {
            return;
        }

        $socio = Member::with('plan')->find($this->memberId);
        if (! $socio || ! $socio->phone) {
            return;
        }

        $plan = $socio->plan->name ?? 'tu membresía';
        $vigencia = $socio->expiration_date?->format('d/m/Y');

        $mensaje = "¡Hola {$socio->name}! 👋 Bienvenido(a) a *OllinFit*.\n\n"
            . "Tu membresía *{$plan}* quedó activa"
            . ($vigencia ? " con vigencia hasta el {$vigencia}" : '') . ".\n"
            . "Tu folio es *{$socio->folio}*.\n\n"
            . "Te compartimos tu código QR de acceso: muéstralo en recepción para entrar al gimnasio. 💪";

        // El QR codifica el folio (igual que el gafete); el microservicio Node lo dibuja.
        $whatsapp->enviarConQr($socio->phone, $mensaje, $socio->folio);
    }
}
