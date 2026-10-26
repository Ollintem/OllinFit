<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetCode extends Notification
{
    public function __construct(
        public string $code,
        public int $expiresMinutes,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu código de recuperación: '.$this->code)
            ->markdown('mail.password-reset-code', [
                'name' => $notifiable->name,
                'code' => $this->code,
                'expiresMinutes' => $this->expiresMinutes,
            ]);
    }
}
