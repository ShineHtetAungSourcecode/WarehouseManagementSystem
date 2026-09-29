<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['warehouse_id', 'item_id', 'quantity', 'bin_location'];
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function item() { return $this->belongsTo(Item::class); }
    public function movements() { return $this->hasMany(StockMovement::class); }

}
