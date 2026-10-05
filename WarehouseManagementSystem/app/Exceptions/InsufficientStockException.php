<?php

namespace App\Exceptions;

use App\Models\Inventory;
use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct(
        public readonly Inventory $inventory,
        public readonly int $requested,
    ) {
        parent::__construct(sprintf(
            'Not enough stock for item #%d in warehouse #%d: requested %d, available %d.',
            $inventory->item_id,
            $inventory->warehouse_id,
            $requested,
            $inventory->quantity,
        ));
    }
}
