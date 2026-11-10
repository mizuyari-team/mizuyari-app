<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $plants = [
            ['name' => 'モンステラ', 'interval' => 7],
            ['name' => 'サボテン', 'interval' => 21],
            ['name' => 'ポトス', 'interval' => 5],
        ];

        foreach ($plants as $p) {
            $plant = $user->plants()->create([
                'name' => $p['name'],
                'watering_interval_days' => $p['interval'],
            ]);

            // 過去5回分の水やり記録
            for ($i = 1; $i <= 5; $i++) {
                $plant->wateringLogs()->create([
                    'watered_at' => now()->subDays($p['interval'] * $i),
                ]);
            }
        }
    }
}