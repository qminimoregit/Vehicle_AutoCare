<?php

namespace App\Mail;

use App\Models\CompanySetting;
use App\Models\DirectSale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalesBillMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;

    public function __construct(
        public DirectSale $directSale,
        $company = null
    ) {
        $this->company = $company ?? CompanySetting::getSettings();
    }

    public function envelope(): Envelope
    {
        $companyName = $this->company->company_name ?? 'Viran Auto Care';

        return new Envelope(
            subject: "Your Sales Bill – {$companyName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sales_bill',
        );
    }

    public function attachments(): array
    {
        $directSale = $this->directSale;
        $company = $this->company;

        $pdf = Pdf::loadView('operations.direct-sales.pdf', compact('directSale', 'company'));
        $pdf->setPaper('a4', 'portrait');

        return [
            Attachment::fromData(fn () => $pdf->output(), "SalesBill_{$directSale->bill_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
