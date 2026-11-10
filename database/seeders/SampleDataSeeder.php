<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $other = User::factory()->create([
            'name' => 'ほかのユーザー',
            'email' => 'other@example.com',
            'password' => 'password',
        ]);

        // days_ago は「何日前に水やりしたか」。古い順に並べ、間隔をばらつかせている
        $plants = [
            // 頻繁に水やり。間隔 2,4,2,3,2,3,2日。最終は1日前で予測日はこれから
            [
                'owner' => $user, 'name' => 'バジル', 'species' => 'バジル',
                'location' => '研究室の窓際', 'pot_size' => '小',
                'daily_base_ml' => 80, 'default_interval_days' => 3,
                'amount_ml' => 120, 'days_ago' => [19, 17, 13, 11, 8, 6, 3, 1],
            ],
            // 予測日を過ぎている（最終9日前、平均間隔5.5日 → 予測は約3.5日前）→ LINE通知の確認用
            [
                'owner' => $user, 'name' => 'ポトス', 'species' => 'ポトス',
                'location' => '教室の棚', 'pot_size' => '中',
                'daily_base_ml' => 50, 'default_interval_days' => 6,
                'amount_ml' => 180, 'days_ago' => [20, 14, 9],
            ],
            // 間隔が長い（多肉植物）。間隔 10,10日。最終は1日前で予測日はこれから
            [
                'owner' => $user, 'name' => 'サボテン', 'species' => '多肉植物',
                'location' => '研究室の窓際', 'pot_size' => '小',
                'daily_base_ml' => 30, 'default_interval_days' => 12,
                'amount_ml' => 100, 'days_ago' => [21, 11, 1],
            ],
            // 登録したばかり（記録3件未満）→ 既定値での予測の確認用
            [
                'owner' => $user, 'name' => 'モンステラ', 'species' => 'モンステラ',
                'location' => '自宅のリビング', 'pot_size' => '大',
                'daily_base_ml' => 100, 'default_interval_days' => 7,
                'amount_ml' => 250, 'days_ago' => [1],
            ],
            // 2人目のユーザーの植物（他人のデータが見えないことの確認用）
            [
                'owner' => $other, 'name' => 'ガジュマル', 'species' => 'ガジュマル',
                'location' => '自宅の玄関', 'pot_size' => '中',
                'daily_base_ml' => 60, 'default_interval_days' => 7,
                'amount_ml' => 150, 'days_ago' => [14, 7, 1],
            ],
        ];

        foreach ($plants as $data) {
            $plant = Plant::factory()->for($data['owner'])->create([
                'name' => $data['name'],
                'species' => $data['species'],
                'location' => $data['location'],
                'pot_size' => $data['pot_size'],
                'daily_base_ml' => $data['daily_base_ml'],
                'default_interval_days' => $data['default_interval_days'],
                // 最初の水やり記録より古くなるよう、作成日を合わせる
                'created_at' => now()->subDays(max($data['days_ago'])),
            ]);

            foreach ($data['days_ago'] as $i => $daysAgo) {
                WateringLog::factory()->for($plant)->create([
                    'watered_at' => now()->subDays($daysAgo)->setTime(8 + ($i % 3), 0),
                    'amount_ml' => $data['amount_ml'],
                ]);
            }
        }
    }
}
