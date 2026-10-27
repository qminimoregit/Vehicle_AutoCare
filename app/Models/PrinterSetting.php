<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrinterSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'workstation_name',
        'bill_printer',
        'job_card_printer',
        'paper_size',
        'auto_route',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'auto_route' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Resolve printer setting for a specific branch and workstation context.
     * Fallback order:
     * 1. Match exact branch_id + workstation_name
     * 2. Match exact branch_id + 'Default-Workstation' (or any workstation in branch)
     * 3. Match global setting (branch_id null) + workstation_name
     * 4. Match global setting (branch_id null) + 'Default-Workstation'
     * 5. First active setting
     */
    public static function resolveFor(?int $branchId = null, ?string $workstationName = null): ?self
    {
        $workstationName = trim((string) $workstationName);

        // 1. Exact branch + workstation
        if ($branchId && $workstationName) {
            $setting = self::where('branch_id', $branchId)
                ->where('workstation_name', $workstationName)
                ->where('is_active', true)
                ->first();
            if ($setting) {
                return $setting;
            }
        }

        // 2. Branch + Default-Workstation
        if ($branchId) {
            $setting = self::where('branch_id', $branchId)
                ->where('is_active', true)
                ->orderByRaw("CASE WHEN workstation_name = 'Default-Workstation' THEN 0 ELSE 1 END")
                ->first();
            if ($setting) {
                return $setting;
            }
        }

        // 3. Global + workstation
        if ($workstationName) {
            $setting = self::whereNull('branch_id')
                ->where('workstation_name', $workstationName)
                ->where('is_active', true)
                ->first();
            if ($setting) {
                return $setting;
            }
        }

        // 4. Any global active setting
        $setting = self::whereNull('branch_id')
            ->where('is_active', true)
            ->first();
        if ($setting) {
            return $setting;
        }

        // 5. Any active setting in database
        return self::where('is_active', true)->first();
    }

    /**
     * Determine target printer name based on document type.
     */
    public function getPrinterForDocument(string $documentType): ?string
    {
        $documentType = strtolower(trim($documentType));

        // Bill Printer documents
        if (in_array($documentType, [
            'bill',
            'direct_sale',
            'direct_sale_receipt',
            'invoice_80mm',
            'sales_invoice_80mm',
            'payment_receipt',
            'partial_payment_receipt',
            'advance_receipt',
            'advance_payment_receipt',
        ])) {
            return $this->bill_printer;
        }

        // Job Card Printer documents
        if (in_array($documentType, [
            'job_card',
            'full_job_card',
            'job_card_full',
            'selected_services',
            'selected_products',
            'selected_items',
            'job_items',
            'workshop_instructions',
        ])) {
            return $this->job_card_printer;
        }

        return null;
    }
}
