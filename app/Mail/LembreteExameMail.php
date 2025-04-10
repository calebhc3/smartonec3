<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LembreteExameMail extends Mailable
{
    use Queueable, SerializesModels;

    public $agendamento;

    public function __construct($agendamento)
    {
        $this->agendamento = $agendamento;
    }

    public function build()
    {
        return $this->subject('Lembrete: Exame Agendado')
                    ->view('emails.lembrete_exame') // Certifique-se que essa view exista
                    ->with(['agendamento' => $this->agendamento]);
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete Exame Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lembrete_exame',  // Aqui, indicamos o caminho correto da view
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
