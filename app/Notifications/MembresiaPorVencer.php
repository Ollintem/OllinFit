<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Aviso por email de que la membresía del socio está por vencer.
 * Se envía desde el comando `socios:recordar-vencimientos`.
 */
class MembresiaPorVencer extends Notification
{
    public function __construct(
        public int $diasRestantes,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $fecha = $notifiable->expiration_date?->format('d/m/Y');
        $plan = $notifiable->plan->name ?? 'tu plan';

        $cuando = $this->diasRestantes <= 0
            ? 'vence hoy'
            : ($this->diasRestantes === 1 ? 'vence mañana' : "vence en {$this->diasRestantes} días");

        return (new MailMessage)
            ->subject("Tu membresía {$cuando} — OllinFit")
            ->greeting("Hola, {$notifiable->name}")
            ->line("Tu membresía **{$plan}** {$cuando} (el {$fecha}).")
            ->line('Renueva a tiempo para no perder el acceso al gimnasio.')
            ->line('Si ya renovaste, ignora este mensaje.')
            ->salutation('¡Te esperamos! — Equipo OllinFit');
    }
}
