<?php

namespace App\Services\Inventory;

use App\Models\GoodsReceivedNote;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * Confirm a Goods Received Note and receive stock batches.
     *
     * @throws RuntimeException
     */
    public function confirmGrn(GoodsReceivedNote $grn, User $user): GoodsReceivedNote
    {
        return DB::transaction(function () use ($grn, $user) {
            // Lock GRN row for update to prevent concurrent double posting
            $freshGrn = GoodsReceivedNote::where('id', $grn->id)->lockForUpdate()->firstOrFail();

            if ($freshGrn->isConfirmed()) {
                throw new RuntimeException("GRN #{$freshGrn->grn_number} is already confirmed.");
            }

            if ($freshGrn->items()->count() === 0) {
                throw new RuntimeException('Cannot confirm a GRN with no products.');
            }

            foreach ($freshGrn->items as $index => $item) {
                $physicalQuantity = (float) $item->purchase_quantity + (float) $item->free_quantity;

                if ($physicalQuantity <= 0) {
                    continue;
                }

                $inventoryUnitCost = $physicalQuantity > 0
                    ? round((float) $item->line_total / $physicalQuantity, 4)
                    : (float) $item->purchase_unit_price;

                // Unique batch number: B{Year}{Month}-{GRN_ID}-{Item_ID}
                $batchNumber = sprintf(
                    'B%s-%04d-%03d',
                    date('y', strtotime($freshGrn->received_date)),
                    $freshGrn->id,
                    $index + 1
                );

                // Ensure unique internal batch number
                $counter = 1;
                while (StockBatch::where('batch_number', $batchNumber)->exists()) {
                    $batchNumber = sprintf(
                        'B%s-%04d-%03d-%d',
                        date('y', strtotime($freshGrn->received_date)),
                        $freshGrn->id,
                        $index + 1,
                        $counter++
                    );
                }

                // Create the Stock Batch
                $batch = StockBatch::create([
                    'batch_number' => $batchNumber,
                    'grn_id' => $freshGrn->id,
                    'grn_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'supplier_id' => $freshGrn->supplier_id,
                    'purchase_date' => $freshGrn->received_date,
                    'received_quantity' => $physicalQuantity,
                    'free_quantity' => $item->free_quantity,
                    'remaining_quantity' => $physicalQuantity,
                    'original_purchase_unit_price' => $item->purchase_unit_price,
                    'inventory_unit_cost' => $inventoryUnitCost,
                    'selling_price_at_receipt' => $item->selling_price_snapshot,
                    'status' => 'active',
                ]);

                // Create Stock Movement ledger entry
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'stock_batch_id' => $batch->id,
                    'movement_type' => 'grn_receipt',
                    'quantity' => $physicalQuantity,
                    'unit_cost' => $inventoryUnitCost,
                    'reference_type' => GoodsReceivedNote::class,
                    'reference_id' => $freshGrn->id,
                    'notes' => "Stock receipt from GRN #{$freshGrn->grn_number} (Batch {$batchNumber})",
                    'created_by' => $user->id,
                ]);
            }

            $freshGrn->update([
                'status' => 'confirmed',
                'posted_at' => now(),
                'posted_by' => $user->id,
            ]);

            return $freshGrn;
        });
    }

    /**
     * Deduct product stock using First-In, First-Out (FIFO) logic.
     *
     * @return array{total_quantity: float, total_cost: float, batches: array}
     *
     * @throws RuntimeException
     */
    public function issueStockFifo(
        Product|int $product,
        float $quantityToIssue,
        string $movementType = 'fifo_sale',
        ?Model $reference = null,
        ?User $user = null
    ): array {
        if ($quantityToIssue <= 0) {
            throw new RuntimeException('Quantity to issue must be greater than zero.');
        }

        $productId = $product instanceof Product ? $product->id : $product;

        return DB::transaction(function () use ($productId, $quantityToIssue, $movementType, $reference, $user) {
            // Lock all active batches of the product in FIFO order (oldest purchase_date first, then oldest id)
            $batches = StockBatch::where('product_id', $productId)
                ->where('status', 'active')
                ->where('remaining_quantity', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            $totalAvailable = (float) $batches->sum('remaining_quantity');

            if ($totalAvailable < $quantityToIssue) {
                throw new RuntimeException(
                    "Insufficient stock. Requested: {$quantityToIssue}, Available: {$totalAvailable}."
                );
            }

            $remainingNeeded = $quantityToIssue;
            $allocatedBatches = [];
            $totalCost = 0.0;

            foreach ($batches as $batch) {
                if ($remainingNeeded <= 0) {
                    break;
                }

                $availableInBatch = (float) $batch->remaining_quantity;
                $deductFromThisBatch = min($availableInBatch, $remainingNeeded);
                $batchCost = round($deductFromThisBatch * (float) $batch->inventory_unit_cost, 4);

                $newRemaining = $availableInBatch - $deductFromThisBatch;
                $newStatus = $newRemaining <= 0 ? 'exhausted' : 'active';

                $batch->update([
                    'remaining_quantity' => $newRemaining,
                    'status' => $newStatus,
                ]);

                // Record Stock Movement ledger entry
                StockMovement::create([
                    'product_id' => $productId,
                    'stock_batch_id' => $batch->id,
                    'movement_type' => $movementType,
                    'quantity' => -1 * $deductFromThisBatch,
                    'unit_cost' => $batch->inventory_unit_cost,
                    'reference_type' => $reference ? get_class($reference) : null,
                    'reference_id' => $reference ? $reference->getKey() : null,
                    'notes' => "FIFO issue from Batch {$batch->batch_number}",
                    'created_by' => $user?->id,
                ]);

                $allocatedBatches[] = [
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'quantity' => $deductFromThisBatch,
                    'unit_cost' => (float) $batch->inventory_unit_cost,
                    'line_cost' => $batchCost,
                    'remaining_in_batch' => $newRemaining,
                ];

                $totalCost += $batchCost;
                $remainingNeeded -= $deductFromThisBatch;
            }

            return [
                'total_quantity' => $quantityToIssue,
                'total_cost' => round($totalCost, 2),
                'batches' => $allocatedBatches,
            ];
        });
    }

    /**
     * Reverse previously allocated FIFO stock batches back into inventory.
     *
     * @param array<int, array{batch_id: int, quantity: float, unit_cost?: float}> $allocations
     * @throws RuntimeException
     */
    public function reverseStockFifo(
        array $allocations,
        ?Model $reference = null,
        ?User $user = null,
        string $reason = 'Job cancellation'
    ): void {
        DB::transaction(function () use ($allocations, $reference, $user, $reason) {
            foreach ($allocations as $alloc) {
                $batchId = $alloc['batch_id'] ?? null;
                $qty = (float) ($alloc['quantity'] ?? 0);

                if (!$batchId || $qty <= 0) {
                    continue;
                }

                $batch = StockBatch::where('id', $batchId)->lockForUpdate()->first();
                if (!$batch) {
                    continue;
                }

                $newRemaining = (float) $batch->remaining_quantity + $qty;
                $batch->update([
                    'remaining_quantity' => $newRemaining,
                    'status' => 'active',
                ]);

                // Record reversal movement (positive quantity restoring stock)
                StockMovement::create([
                    'product_id' => $batch->product_id,
                    'stock_batch_id' => $batch->id,
                    'movement_type' => 'issue_reversal',
                    'quantity' => $qty,
                    'unit_cost' => $batch->inventory_unit_cost,
                    'reference_type' => $reference ? get_class($reference) : null,
                    'reference_id' => $reference ? $reference->getKey() : null,
                    'notes' => "Stock reversal: {$reason} (Restored to Batch {$batch->batch_number})",
                    'created_by' => $user?->id,
                ]);
            }
        });
    }

    /**
     * Get real available stock for a product across all active batches.
     */
    public function getAvailableStock(Product|int $product): float
    {
        $productId = $product instanceof Product ? $product->id : $product;

        return (float) StockBatch::where('product_id', $productId)
            ->where('status', 'active')
            ->sum('remaining_quantity');
    }
}

