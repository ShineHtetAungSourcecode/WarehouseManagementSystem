<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Receive = 'receive';
    case Pick = 'pick';
    case Adjustment = 'adjustment';
    case Transfer = 'transfer';
}
