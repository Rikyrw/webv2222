<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NasabahVerificationLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $verificationUrl,
        public int $expiresInMinutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi Email Akun Nasabah GreenPoint',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nasabah-verification-link',
            text: 'emails.nasabah-verification-link-text',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
