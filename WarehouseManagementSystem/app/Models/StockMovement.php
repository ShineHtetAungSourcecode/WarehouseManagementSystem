<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['inventory_id', 'user_id', 'quantity_change', 'type', 'reference_type', 'reference_id', 'reason'];
    public function inventory() { return $this->belongsTo(Inventory::class); }
    public function user() { return $this->belongsTo(User::class); }

}
