<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => random_int(100, 120),
            'type' => random_int(1, count(config('custom.apartments.types'))),
            'description' => random_int(1, count(config('custom.apartments.descriptions'))),
        ];
    }
}
