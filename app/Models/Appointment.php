<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'branch_id',
        'customer_id',
        'vehicle_id',
        'vehicle_type_id',
        'service_id',
        'customer_name',
        'mobile_number',
        'vehicle_number',
        'service_name',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'job_card_id',
        'created_by',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

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

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateAppointmentNumber(): string
    {
        $prefix = 'APT-'.date('Ymd').'-';
        $latest = self::where('appointment_number', 'LIKE', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/-(\d+)$/', $latest->appointment_number, $matches)) {
            $seq = (int) $matches[1] + 1;
        } else {
            $seq = 1;
        }

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('appointment_date', now()->toDateString());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('appointment_date', '>=', now()->toDateString());
    }

    public function scopeBranch(Builder $query, ?int $branchId): Builder
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }

        return $query;
    }
}
