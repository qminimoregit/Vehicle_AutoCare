<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'vehicle_id',
        'quotation_number',
        'quotation_date',
        'expiry_date',
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
        'status',
        'notes',
        'terms_conditions',
        'converted_invoice_id',
        'converted_job_card_id',
        'customer_snapshot',
        'vehicle_snapshot',
        'company_snapshot',
        'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'customer_snapshot' => 'array',
        'vehicle_snapshot' => 'array',
        'company_snapshot' => 'array',
    ];

    public static function generateQuotationNumber(?int $branchId = null): string
    {
        $prefix = 'QT-' . date('Ym') . '-';
        $lastQuotation = self::where('quotation_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastQuotation) {
            $lastSeq = (int) substr($lastQuotation->quotation_number, strlen($prefix));
            $newSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }

        return $prefix . $newSeq;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function convertedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'converted_invoice_id');
    }

    public function convertedJobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class, 'converted_job_card_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'sent']);
    }

    public function canConvert(): bool
    {
        return in_array($this->status, ['draft', 'sent', 'accepted']) && !$this->converted_invoice_id;
    }

    public function canCreateJobCard(): bool
    {
        return in_array($this->status, ['draft', 'sent', 'accepted']) && !$this->converted_job_card_id;
    }

    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('line_total');

        $discountAmount = 0.0;
        if ($this->discount_type === 'percentage') {
            $discountAmount = round($subtotal * ((float) $this->discount_value / 100), 2);
        } elseif ($this->discount_type === 'fixed') {
            $discountAmount = min($subtotal, (float) $this->discount_value);
        }

        $taxableAmount = max(0, $subtotal - $discountAmount);
        $taxAmount = 0.0;
        if ((float) $this->tax_percentage > 0) {
            $taxAmount = round($taxableAmount * ((float) $this->tax_percentage / 100), 2);
        }

        $grandTotal = max(0, $taxableAmount + $taxAmount + (float) $this->additional_charges);

        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
        ]);
    }
}
