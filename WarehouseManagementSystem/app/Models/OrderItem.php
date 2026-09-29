<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'item_id', 'quantity_requested', 'price_at_purchase_cents'];

}
