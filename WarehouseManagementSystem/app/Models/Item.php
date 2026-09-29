<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['company_id', 'sku', 'name', 'description', 'price_cents'];
    public function company() { return $this->belongsTo(Company::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }

}
