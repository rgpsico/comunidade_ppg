<?php

namespace App\Mail;

use App\Models\Vaga;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MatchCurriculosMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Vaga $vaga,
        public Collection $curriculos,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎯 {$this->curriculos->count()} currículo(s) compatível(is) — {$this->vaga->titulo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.match_curriculos',
        );
    }
}
