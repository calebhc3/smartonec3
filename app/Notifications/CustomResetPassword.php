<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recuperação de Senha')
            ->greeting('Olá!')
            ->line('Recebemos um pedido para redefinir sua senha.')
            ->action('Redefinir Senha', url(route('password.reset', $this->token, false)))
            ->line('Se você não solicitou uma redefinição de senha, ignore este e-mail.');
    }
}