<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        $city = fake()->city();

        return [
            'company_id' => Company::factory(),
            'name' => "{$city} Warehouse",
            'code' => 'WH-'.fake()->unique()->bothify('???-##'),
            'location' => $city,
        ];
    }
}
