<?php

namespace App\Mail;

use App\Models\Payment;
use App\Services\NumberToWordsHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment,
        public $company
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->company->company_name ?? 'AutoCare Service Center';

        return new Envelope(
            subject: "Payment Receipt {$this->payment->payment_number} from {$companyName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_receipt',
        );
    }

    public function attachments(): array
    {
        $payment = $this->payment;
        $company = $this->company;
        $amountInWords = NumberToWordsHelper::spell($payment->amount, 'LKR');

        // Render PDF receipt
        $pdf = Pdf::loadView('billing.payments.receipt_pdf', compact('payment', 'company', 'amountInWords'));
        $pdf->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(fn () => $pdf->output(), "Receipt_{$payment->payment_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
