<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The only place that changes inventory quantities.
 *
 * Every change runs in a database transaction, locks the inventory row
 * (SELECT ... FOR UPDATE) so two people picking at the same moment can't
 * both take the last unit, refuses to go below zero, and writes a
 * StockMovement row as an audit log.
 */
class InventoryService
{
    /** Add stock, e.g. a delivery arriving at the warehouse. */
    public function receive(Inventory $inventory, int $quantity, ?User $user = null, ?Model $reference = null, ?string $reason = null): StockMovement
    {
        $this->assertPositive($quantity);

        return $this->move($inventory, $quantity, StockMovementType::Receive, $user, $reference, $reason);
    }

    /** Remove stock, e.g. picking items for an order. */
    public function pick(Inventory $inventory, int $quantity, ?User $user = null, ?Model $reference = null, ?string $reason = null): StockMovement
    {
        $this->assertPositive($quantity);

        return $this->move($inventory, -$quantity, StockMovementType::Pick, $user, $reference, $reason);
    }

    /** Correct the count after a stocktake. $change may be positive or negative. */
    public function adjust(Inventory $inventory, int $change, string $reason, ?User $user = null): StockMovement
    {
        if ($change === 0) {
            throw new InvalidArgumentException('Adjustment must change the quantity.');
        }

        return $this->move($inventory, $change, StockMovementType::Adjustment, $user, null, $reason);
    }

    /**
     * Move stock of one item between two warehouses, all-or-nothing.
     *
     * @return array{0: StockMovement, 1: StockMovement} [out of $from, into $to]
     */
    public function transfer(Inventory $from, Inventory $to, int $quantity, ?User $user = null, ?string $reason = null): array
    {
        $this->assertPositive($quantity);

        if ($from->is($to)) {
            throw new InvalidArgumentException('Cannot transfer stock to the same inventory.');
        }

        if ($from->item_id !== $to->item_id) {
            throw new InvalidArgumentException('Transfers must be between inventories of the same item.');
        }

        return DB::transaction(function () use ($from, $to, $quantity, $user, $reason) {
            // Lock both rows in a fixed order (by id) so two opposite
            // transfers running at once can't deadlock each other.
            Inventory::query()
                ->whereKey([$from->getKey(), $to->getKey()])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            return [
                $this->move($from, -$quantity, StockMovementType::Transfer, $user, null, $reason),
                $this->move($to, $quantity, StockMovementType::Transfer, $user, null, $reason),
            ];
        });
    }

    private function move(Inventory $inventory, int $change, StockMovementType $type, ?User $user, ?Model $reference, ?string $reason): StockMovement
    {
        $movement = DB::transaction(function () use ($inventory, $change, $type, $user, $reference, $reason) {
            // Re-read the row under a lock; the caller's copy may be stale.
            $locked = Inventory::query()->whereKey($inventory->getKey())->lockForUpdate()->firstOrFail();

            $newQuantity = $locked->quantity + $change;

            if ($newQuantity < 0) {
                throw new InsufficientStockException($locked, abs($change));
            }

            $locked->update(['quantity' => $newQuantity]);

            return $locked->movements()->create([
                'user_id' => $user?->getKey(),
                'quantity_change' => $change,
                'type' => $type,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'reason' => $reason,
            ]);
        });

        $inventory->refresh();

        return $movement;
    }

    private function assertPositive(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }
    }
}
