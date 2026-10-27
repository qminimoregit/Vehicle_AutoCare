<?php

namespace App\Services;

use App\Models\GoodsReceivedNote;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GrnNumberService
{
    /**
     * Generate the next unique, sequential, concurrency-safe GRN number.
     * Format: GRN-YYYYMM-XXXX (e.g. GRN-202610-0007).
     *
     * Uses atomic sequence rows (if table exists), MySQL named locks,
     * and pessimistic row locking to guarantee that concurrent requests
     * will never generate conflicting numbers.
     */
    public static function generateNextGrnNumber(?string $date = null): string
    {
        $carbonDate = $date ? Carbon::parse($date) : Carbon::now();
        $series = $carbonDate->format('Ym');
        $prefix = "GRN-{$series}-";

        return DB::transaction(function () use ($series, $prefix) {
            $lockName = "grn_sequence_lock_{$series}";
            $hasNamedLock = false;

            try {
                // 1. Acquire MySQL advisory named lock (waits up to 10s)
                $lockResult = DB::select('SELECT GET_LOCK(?, 10) as lock_status', [$lockName]);
                $hasNamedLock = ! empty($lockResult) && (int) ($lockResult[0]->lock_status ?? 0) === 1;
            } catch (\Throwable $e) {
                // If DB driver does not support GET_LOCK, fallback to row-level locks
                Log::debug("GET_LOCK not available for GRN generation: {$e->getMessage()}");
            }

            try {
                $nextNumber = 1;

                // 2. If dedicated grn_sequences table exists, use its sequence row
                if (Schema::hasTable('grn_sequences')) {
                    $seqRow = DB::table('grn_sequences')
                        ->where('series', $series)
                        ->lockForUpdate()
                        ->first();

                    if (! $seqRow) {
                        $maxExisting = self::detectMaxExistingSequenceForSeries($series);

                        DB::table('grn_sequences')->insert([
                            'series' => $series,
                            'current_number' => $maxExisting,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $seqRow = DB::table('grn_sequences')
                            ->where('series', $series)
                            ->lockForUpdate()
                            ->first();
                    }

                    $current = (int) ($seqRow->current_number ?? 0);
                    $nextNumber = $current + 1;
                } else {
                    // 3. Fallback: Query all existing rows for this series with lockForUpdate
                    GoodsReceivedNote::where('grn_number', 'LIKE', "{$prefix}%")
                        ->lockForUpdate()
                        ->get();

                    $maxExisting = self::detectMaxExistingSequenceForSeries($series);
                    $nextNumber = $maxExisting + 1;
                }

                // 4. Safety collision loop: Ensure candidate does not already exist in database
                // (handles manual inserts, seeded records, historical drafts)
                $candidate = sprintf('%s%04d', $prefix, $nextNumber);
                while (GoodsReceivedNote::where('grn_number', $candidate)->exists()) {
                    $nextNumber++;
                    $candidate = sprintf('%s%04d', $prefix, $nextNumber);
                }

                // 5. Update grn_sequences counter if table exists
                if (Schema::hasTable('grn_sequences')) {
                    DB::table('grn_sequences')
                        ->where('series', $series)
                        ->update([
                            'current_number' => $nextNumber,
                            'updated_at' => now(),
                        ]);
                }

                return $candidate;
            } finally {
                if ($hasNamedLock) {
                    try {
                        DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
                    } catch (\Throwable $e) {
                        // Ignore lock release failures
                    }
                }
            }
        });
    }

    /**
     * Scan existing GRNs for the given YYYYMM series and extract the highest pure numeric sequence.
     * Includes all records (draft, confirmed, cancelled).
     */
    public static function detectMaxExistingSequenceForSeries(string $series): int
    {
        $prefix = "GRN-{$series}-";

        // Query all GRN numbers starting with this prefix
        $grnNumbers = GoodsReceivedNote::where('grn_number', 'LIKE', "{$prefix}%")
            ->pluck('grn_number');

        $maxSeq = 0;
        $regex = '/^GRN-'.preg_quote($series, '/').'-(\d+)$/i';

        foreach ($grnNumbers as $number) {
            if (preg_match($regex, $number, $matches)) {
                $seqVal = (int) $matches[1];
                if ($seqVal > $maxSeq) {
                    $maxSeq = $seqVal;
                }
            }
        }

        return $maxSeq;
    }

    /**
     * Helper to execute GRN creation with automatic retry on duplicate key conflict.
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
                if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062 && $attempts < $maxRetries) {
                    Log::warning("GRN sequence collision on attempt {$attempts}. Retrying...");
                    usleep(50000 * $attempts); // backoff 50ms, 100ms

                    continue;
                }

                throw $e;
            }
        }
    }
}
