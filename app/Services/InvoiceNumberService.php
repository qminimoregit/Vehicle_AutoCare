<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceNumberService
{
    /**
     * Generate the next unique, sequential, concurrency-safe invoice number.
     * Format: INV-YYYYMM-NNNN (e.g., INV-202610-0035).
     *
     * Uses atomic sequence rows and pessimistic locking (SELECT ... FOR UPDATE)
     * to guarantee that concurrent requests will never receive the same number.
     */
    public static function generateNextInvoiceNumber(?string $date = null): string
    {
        $carbonDate = $date ? Carbon::parse($date) : Carbon::now();
        $series = $carbonDate->format('Ym');
        $prefix = "INV-{$series}-";

        return DB::transaction(function () use ($series, $prefix) {
            // 1. Acquire pessimistic lock on sequence row for this series
            $sequence = DB::table('invoice_sequences')
                ->where('series', $series)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                // Initialize sequence from actual maximum numeric suffix in invoices table
                $maxNumericSeq = self::detectMaxExistingSequenceForSeries($series);

                DB::table('invoice_sequences')->insert([
                    'series' => $series,
                    'current_number' => $maxNumericSeq,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sequence = DB::table('invoice_sequences')
                    ->where('series', $series)
                    ->lockForUpdate()
                    ->first();
            }

            $currentNumber = (int) $sequence->current_number;
            $nextNumber = $currentNumber + 1;

            // 2. Safety check: ensure number does not exist in invoices table
            // (protects against historical manual entries or non-sequential inserts)
            $candidate = $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            while (Invoice::where('invoice_number', $candidate)->exists()) {
                $nextNumber++;
                $candidate = $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            }

            // 3. Update sequence
            DB::table('invoice_sequences')
                ->where('series', $series)
                ->update([
                    'current_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return $candidate;
        });
    }

    /**
     * Scan existing invoices for the given YYYYMM series and extract the highest pure numeric suffix.
     * Prevents issues with alphanumeric suffixes (e.g. TEST999, AG01) that evaluate to 0 in PHP integer casting.
     */
    public static function detectMaxExistingSequenceForSeries(string $series): int
    {
        $prefix = "INV-{$series}-";
        $invoices = Invoice::where('invoice_number', 'LIKE', "{$prefix}%")
            ->pluck('invoice_number');

        $maxSeq = 0;
        $regex = '/^INV-'.preg_quote($series, '/').'-(\d+)$/';

        foreach ($invoices as $invNum) {
            if (preg_match($regex, $invNum, $matches)) {
                $seqVal = (int) $matches[1];
                if ($seqVal > $maxSeq) {
                    $maxSeq = $seqVal;
                }
            }
        }

        return $maxSeq;
    }

    /**
     * Helper to execute invoice creation with automatic retry on sequence conflict.
     */
    public static function executeWithRetry(callable $action, int $maxRetries = 3)
    {
        $attempts = 0;

        while ($attempts < $maxRetries) {
            $attempts++;
            try {
                return $action();
            } catch (QueryException $e) {
                // MySQL Error 1062: Duplicate entry
                if ($e->errorInfo[1] === 1062 && $attempts < $maxRetries) {
                    Log::warning("Invoice sequence collision on attempt {$attempts}. Retrying...");
                    usleep(50000 * $attempts); // backoff 50ms, 100ms

                    continue;
                }

                throw $e;
            }
        }
    }
}
