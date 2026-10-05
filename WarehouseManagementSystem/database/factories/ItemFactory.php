<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'sku' => fake()->unique()->bothify('SKU-####-??'),
            'name' => ucfirst(fake()->words(3, true)),
            'description' => fake()->sentence(),
            'price_cents' => fake()->numberBetween(500, 50_000),
            'reorder_level' => fake()->numberBetween(5, 20),
        ];
    }
}
