<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * An audit-log row: one change to an inventory's quantity.
 * Positive quantity_change = stock in, negative = stock out.
 */
class StockMovement extends Model
{
    protected $fillable = ['inventory_id', 'user_id', 'quantity_change', 'type', 'reference_type', 'reference_id', 'reason'];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'integer',
            'type' => StockMovementType::class,
        ];
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The document that caused this movement, e.g. an Order. */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
