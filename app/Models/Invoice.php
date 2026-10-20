<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'branch_id',
        'invoice_date',
        'due_date',
        'customer_id',
        'quotation_id',
        'job_card_id',
        'invoice_type',
        'subject',
        'reference',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'additional_charges',
        'grand_total',
        'amount_paid',
        'balance_amount',
        'status',
        'payment_method',
        'notes',
        'terms_conditions',
        'customer_snapshot',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'original_invoice_date',
        'date_changed_by',
        'date_changed_at',
        'date_change_reason',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'original_invoice_date' => 'date',
        'cancelled_at' => 'datetime',
        'date_changed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'customer_snapshot' => 'array',
    ];

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.date('Ym').'-';
        $lastInvoice = self::where('invoice_number', 'LIKE', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastSeq = (int) substr($lastInvoice->invoice_number, strlen($prefix));
            $newSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }

        return $prefix.$newSeq;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function jobCard()
    {
        return $this->belongsTo(JobCard::class);
    }

    public function directSale()
    {
        return $this->hasOne(DirectSale::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function dateChangedBy()
    {
        return $this->belongsTo(User::class, 'date_changed_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function linkedJobCard()
    {
        return $this->hasOne(JobCard::class, 'invoice_id');
    }

    public function supplementaryJobIssues()
    {
        return $this->hasMany(JobIssue::class, 'supplementary_invoice_id');
    }

    public function getDocumentStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }

        if ($this->status === 'draft') {
            return 'Draft';
        }

        return 'Issued';
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }

        $paid = (float) $this->amount_paid;
        $total = (float) $this->grand_total;
        $balance = (float) $this->balance_amount;

        if ($total > 0 && ($balance <= 0.0001 || $paid >= $total)) {
            return 'Paid in Full';
        }

        if ($paid > 0.0001) {
            return 'Partially Paid';
        }

        return 'Unpaid';
    }
}
