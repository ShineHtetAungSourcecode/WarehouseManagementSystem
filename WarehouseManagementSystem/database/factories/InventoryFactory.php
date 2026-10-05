<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Item and warehouse are created for the same company.
            'item_id' => Item::factory(),
            'warehouse_id' => fn (array $attributes) => Warehouse::factory()->create([
                'company_id' => Item::find($attributes['item_id'])->company_id,
            ]),
            'quantity' => fake()->numberBetween(0, 200),
            'bin_location' => 'Aisle '.fake()->numberBetween(1, 20).', Shelf '.fake()->randomLetter(),
        ];
    }
}
