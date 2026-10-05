<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    protected $fillable = ['company_id', 'warehouse_id', 'order_number', 'status'];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** The order's line rows (quantity and locked-in price per item). */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** The catalog items on this order, with line details on ->pivot. */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'order_items')
            ->withPivot('quantity_requested', 'price_at_purchase_cents')
            ->withTimestamps();
    }

    /** Stock movements created while fulfilling this order. */
    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function totalCents(): int
    {
        return (int) $this->orderItems->sum(
            fn (OrderItem $line) => $line->quantity_requested * $line->price_at_purchase_cents,
        );
    }
}
