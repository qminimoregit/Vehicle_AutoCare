<?php

namespace App\Mail;

use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Services\NumberToWordsHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public $company
    ) {}

    public function envelope(): Envelope
    {
        $companyName = $this->company->company_name ?? 'AutoCare Service Center';

        return new Envelope(
            subject: "Invoice #{$this->invoice->invoice_number} from {$companyName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    public function attachments(): array
    {
        $invoice = $this->invoice;
        $company = $this->company;
        $amountInWords = NumberToWordsHelper::spell($invoice->grand_total, 'LKR');

        $pdf = Pdf::loadView('operations.invoices.pdf', compact('invoice', 'company', 'amountInWords'));
        $pdf->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(fn () => $pdf->output(), "Invoice_{$invoice->invoice_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
