<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Vaga;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidaturaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Vaga $vaga,
        public User $candidato,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Candidatura: {$this->vaga->titulo} — {$this->candidato->name}",
            replyTo: [new \Illuminate\Mail\Mailables\Address(
                $this->candidato->email,
                $this->candidato->name,
            )],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidatura',
        );
    }
}
