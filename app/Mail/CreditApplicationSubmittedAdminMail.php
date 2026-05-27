<?php

namespace App\Mail;

use App\Models\CreditApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreditApplicationSubmittedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CreditApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud de crédito enviada #' . $this->application->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.credit-application-submitted',
            with: [
                'application' => $this->application,
            ],
        );
    }
}
