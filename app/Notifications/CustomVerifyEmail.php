<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirme seu e-mail')
            ->greeting('Olá!')
            ->line('Clique no botão abaixo para verificar seu endereço de e-mail.')
            ->action('Verificar E-mail', $this->verificationUrl($notifiable))
            ->line('Se você não criou esta conta, ignore este e-mail.');
    }
}
