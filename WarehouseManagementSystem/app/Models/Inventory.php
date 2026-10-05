<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Stock of one item in one warehouse.
 *
 * Change `quantity` only through App\Services\InventoryService, so every
 * change is locked, validated and recorded as a StockMovement.
 */
class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'item_id', 'quantity', 'bin_location'];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->item->reorder_level;
    }
}
