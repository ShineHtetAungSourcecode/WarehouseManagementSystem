<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];

    protected function casts(): array
    {
        return [
            'status' => CompanyStatus::class,
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
