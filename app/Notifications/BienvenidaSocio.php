<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Correo de bienvenida al crear un socio (el QR se envía por WhatsApp).
 */
class BienvenidaSocio extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $notifiable->plan->name ?? 'tu membresía';
        $vigencia = $notifiable->expiration_date?->format('d/m/Y');

        return (new MailMessage)
            ->subject('¡Bienvenido a OllinFit! 💪')
            ->greeting("¡Hola, {$notifiable->name}!")
            ->line("Te damos la bienvenida a **OllinFit**. Tu membresía **{$plan}** quedó activa"
                . ($vigencia ? " con vigencia hasta el {$vigencia}" : '') . '.')
            ->line("Tu folio de socio es **{$notifiable->folio}**.")
            ->line('Tu código QR de acceso te lo enviamos por WhatsApp; muéstralo en recepción para entrar al gimnasio.')
            ->salutation('¡Nos vemos en el gym! — Equipo OllinFit');
    }
}
