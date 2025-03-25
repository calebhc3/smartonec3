<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacaoShopeeRetorno extends Mailable
{
    use Queueable, SerializesModels;

    public $quantidade;

    public function __construct($quantidade)
    {
        $this->quantidade = $quantidade;
    }

    public function build()
    {
        return $this->subject('Notificação Shopee Retorno')
                    ->view('emails.notificacao_shopee')
                    ->with([
                        'quantidade' => $this->quantidade,
                    ]);
    }

}
