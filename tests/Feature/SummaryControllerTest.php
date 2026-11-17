<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SummaryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_calculates_savings_and_excludes_demo_and_other_users_plants()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 1. 自分の通常植物（基準量 200ml/日 × 30日 = 想定 6,000ml）
        $myPlant = Plant::factory()->create([
            'user_id' => $user->id,
            'name' => 'ポトス',
            'daily_base_ml' => 200,
            'is_demo' => false,
        ]);
        WateringLog::factory()->create([
            'plant_id' => $myPlant->id,
            'watered_at' => now()->subDays(5),
            'amount_ml' => 500,
        ]);
        WateringLog::factory()->create([
            'plant_id' => $myPlant->id,
            'watered_at' => now()->subDays(2),
            'amount_ml' => 700,
        ]);

        // 2. 自分のデモ植物（集計から除外されるべき）
        $demoPlant = Plant::factory()->create([
            'user_id' => $user->id,
            'name' => 'デモ植木鉢',
            'daily_base_ml' => 1000,
            'is_demo' => true,
        ]);
        WateringLog::factory()->create([
            'plant_id' => $demoPlant->id,
            'watered_at' => now()->subDays(1),
            'amount_ml' => 9999,
        ]);

        // 3. 他人の植物（集計から除外されるべき）
        $otherPlant = Plant::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人のサボテン',
            'daily_base_ml' => 500,
            'is_demo' => false,
        ]);
        WateringLog::factory()->create([
            'plant_id' => $otherPlant->id,
            'watered_at' => now()->subDays(1),
            'amount_ml' => 8888,
        ]);

        $response = $this->actingAs($user)->get(route('summary.index', ['days' => 30]));

        $response->assertStatus(200);

        $overallSaving = $response->viewData('overallSaving');
        // 想定: 200ml * 30日 = 6000ml
        $this->assertEquals(6000.0, $overallSaving['baseline_amount_ml']);
        // 実際: 500 + 700 = 1200ml（デモの9999や他人の8888は含まれない）
        $this->assertEquals(1200.0, $overallSaving['actual_amount_ml']);
        // 節水量: 6000 - 1200 = 4800ml
        $this->assertEquals(4800.0, $overallSaving['saved_amount_ml']);
        // 節水率: 4800 / 6000 * 100 = 80.0%
        $this->assertEquals(80.0, $overallSaving['saving_rate']);

        $plantSummaries = $response->viewData('plantSummaries');
        $this->assertCount(1, $plantSummaries);
        $this->assertEquals('ポトス', $plantSummaries[0]['plant_name']);
    }
}
