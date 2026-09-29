<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['company_id', 'name', 'code', 'location'];
    public function company() { return $this->belongsTo(Company::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }

}
