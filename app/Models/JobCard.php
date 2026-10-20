<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_number',
        'date',
        'customer_id',
        'vehicle_id',
        'vehicle_type_id',
        'current_mileage',
        'next_service_mileage',
        'status',
        'assigned_to',
        'service_total',
        'product_total',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'invoice_id',
        'customer_notes',
        'service_notes',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'started_at',
        'completed_at',
        'delivered_at',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'current_mileage' => 'integer',
        'next_service_mileage' => 'integer',
        'service_total' => 'decimal:2',
        'product_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public static function generateJobNumber(): string
    {
        $prefix = 'JOB-' . date('Ym') . '-';
        $lastJob = self::where('job_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastJob) {
            $lastSeq = (int) substr($lastJob->job_number, strlen($prefix));
            $newSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSeq = '0001';
        }

        return $prefix . $newSeq;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function services()
    {
        return $this->hasMany(JobCardService::class);
    }

    public function products()
    {
        return $this->hasMany(JobCardProduct::class);
    }

    public function recalculateTotals(): void
    {
        $this->service_total = (float) $this->services()->sum('line_total');
        $this->product_total = (float) $this->products()->sum('line_total');
        $subtotal = $this->service_total + $this->product_total;
        $tax = (float) $this->tax_amount;
        $discount = (float) $this->discount_amount;
        $this->grand_total = max(0, $subtotal - $discount + $tax);
        $this->save();
    }
}
