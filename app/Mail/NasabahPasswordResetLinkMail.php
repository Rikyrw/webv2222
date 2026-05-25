<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NasabahPasswordResetLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Akun Nasabah GreenPoint',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nasabah-password-reset-link',
            text: 'emails.nasabah-password-reset-link-text',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
