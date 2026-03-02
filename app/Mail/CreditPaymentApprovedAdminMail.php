<?php

namespace App\Mail;

use App\Models\CreditPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreditPaymentApprovedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CreditPayment $payment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago aprobado con Wompi ' . $this->payment->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.credit-payment-approved',
            with: [
                'payment' => $this->payment,
            ],
        );
    }
}
