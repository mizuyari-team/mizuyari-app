<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\WateringLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WateringLog>
 */
class WateringLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plant_id' => Plant::factory(),
            'watered_at' => fake()->dateTimeBetween('-3 weeks', 'now'),
            'amount_ml' => fake()->numberBetween(100, 250),
            'memo' => null,
            'photo_path' => null,
        ];
    }
}
