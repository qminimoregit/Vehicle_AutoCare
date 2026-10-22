<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'bill_number',
        'branch_id',
        'invoice_id',
        'customer_id',
        'sale_date',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'amount_paid',
        'change_amount',
        'payment_method',
        'status',
        'notes',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'stock_reversed',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'cancelled_at' => 'datetime',
        'stock_reversed' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public static function generateSaleNumber(): string
    {
        $prefix = 'DS-'.date('Ym').'-';
        $lastSale = self::where('sale_number', 'LIKE', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            $lastSeq = (int) substr($lastSale->sale_number, strlen($prefix));
            $newSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }

        return $prefix.$newSeq;
    }

    public static function generateBillNumber(?string $saleNumber = null): string
    {
        if ($saleNumber) {
            return 'BILL-'.$saleNumber;
        }

        $prefix = 'BILL-DS-'.date('Ym').'-';
        $lastBill = self::where('bill_number', 'LIKE', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBill) {
            $lastSeq = (int) substr($lastBill->bill_number, strlen($prefix));
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(DirectSaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Total completed payments recorded for this direct sale bill.
     */
    public function getTotalPaidAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            $sum = (float) $this->payments->where('status', 'completed')->sum('amount');
            if ($sum > 0) {
                return $sum;
            }
        } else {
            $sum = (float) $this->payments()->where('status', 'completed')->sum('amount');
            if ($sum > 0) {
                return $sum;
            }
        }

        return (float) ($this->amount_paid ?? 0);
    }

    /**
     * Outstanding unpaid balance on this sales bill.
     */
    public function getBalanceAttribute(): float
    {
        return max(0, round((float) $this->grand_total - $this->total_paid, 2));
    }

    /**
     * Payment status label: Paid in Full or Partially Paid.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ((float) $this->grand_total <= 0 || $this->balance <= 0.005) {
            return 'Paid in Full';
        }

        return 'Partially Paid';
    }

    /**
     * Prepare formatted WhatsApp message text with actual transaction details.
     */
    public function getWhatsAppMessage(?CompanySetting $company = null): string
    {
        $company = $company ?? CompanySetting::getSettings();
        $companyName = $company->company_name ?? 'Viran Auto Care';

        $location = 'Mattegoda';
        if ($this->branch) {
            if (str_contains($this->branch->address ?? '', 'Mattegoda') || str_contains($this->branch->branch_name ?? '', 'Mattegoda')) {
                $location = 'Mattegoda';
            } else {
                $location = $this->branch->branch_name;
            }
        }
        $tel = $this->branch?->phone ?: ($company->phone ?: '077 620 80 59');

        $total = number_format((float) $this->grand_total, 2);
        $paid = number_format($this->total_paid, 2);
        $balance = number_format($this->balance, 2);
        $status = $this->payment_status;

        return "Thank you for choosing {$companyName}!\n\n"
            . "Your payment details:\n\n"
            . "Bill No: {$this->bill_number}\n"
            . "Total: LKR {$total}\n"
            . "Paid: LKR {$paid}\n"
            . "Balance: LKR {$balance}\n"
            . "Status: {$status}\n\n"
            . "Thank you for visiting {$companyName}, {$location}.\n\n"
            . "Tel: {$tel}";
    }
}

