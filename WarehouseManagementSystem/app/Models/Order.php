<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['company_id', 'warehouse_id', 'order_number', 'status'];
    public function company() { return $this->belongsTo(Company::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->belongsToMany(Item::class, 'order_items')->withPivot('quantity_requested', 'price_at_purchase_cents')->withTimestamps(); }

}
