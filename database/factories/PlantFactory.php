<?php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plant>
 */
class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['バジル', 'ポトス', 'モンステラ', 'サボテン']),
            'species' => fake()->randomElement(['ハーブ', '観葉植物', '多肉植物']),
            'location' => fake()->randomElement(['研究室の窓際', '教室の棚', '自宅のベランダ']),
            'pot_size' => fake()->randomElement(['小', '中', '大']),
            'daily_base_ml' => fake()->numberBetween(30, 100),
            'default_interval_days' => fake()->numberBetween(3, 14),
            'photo_path' => null,
            'is_demo' => false,
        ];
    }
}
