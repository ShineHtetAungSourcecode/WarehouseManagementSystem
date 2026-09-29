<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'slug', 'status'];
    public function users() { return $this->hasMany(User::class); }
    public function roles() { return $this->hasMany(Role::class); }
    public function warehouses() { return $this->hasMany(Warehouse::class); }
    public function items() { return $this->hasMany(Item::class); }
    public function orders() { return $this->hasMany(Order::class); }

}
