<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'phone',
        'email',
        'customer_type',
        'company_name',
        'registration_number',
        'tax_number',
        'address',
        'notes',
        'sms_consent',
        'status',
    ];

    protected $casts = [
        'sms_consent' => 'boolean',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function creditAccount()
    {
        return $this->hasOne(CustomerCreditAccount::class);
    }

    public function hasCreditAccount(): bool
    {
        return $this->creditAccount()->where('status', 'active')->exists();
    }

    public function getOutstandingCreditAttribute(): float
    {
        return $this->creditAccount ? $this->creditAccount->outstanding_balance : 0.0;
    }

    public function getAvailableCreditAttribute(): float
    {
        return $this->creditAccount ? $this->creditAccount->available_credit : 0.0;
    }

    public function isCompany(): bool
    {
        return $this->customer_type === 'company';
    }

    public function getDisplayNameAttribute(): string
    {
        return ($this->customer_type === 'company' && $this->company_name)
            ? "{$this->company_name} ({$this->customer_name})"
            : $this->customer_name;
    }
}
