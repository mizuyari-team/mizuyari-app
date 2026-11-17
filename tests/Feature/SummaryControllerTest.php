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

    public function test_summary_uses_days_since_plant_creation_when_shorter_than_period()
    {
        \Carbon\Carbon::setTestNow('2026-06-15 12:00:00');

        try {
            $user = User::factory()->create();

            // 5日前に登録した植物（基準量 200ml/日 × 5日 = 想定 1,000ml）
            $newPlant = Plant::factory()->create([
                'user_id' => $user->id,
                'name' => 'バジル',
                'daily_base_ml' => 200,
                'is_demo' => false,
            ]);
            $newPlant->forceFill(['created_at' => now()->subDays(5)])->save();

            WateringLog::factory()->create([
                'plant_id' => $newPlant->id,
                'watered_at' => now()->subDays(1),
                'amount_ml' => 300,
            ]);

            $response = $this->actingAs($user)->get(route('summary.index', ['days' => 30]));

            $response->assertStatus(200);

            $overallSaving = $response->viewData('overallSaving');
            $this->assertEquals(1000.0, $overallSaving['baseline_amount_ml']);
            $this->assertEquals(300.0, $overallSaving['actual_amount_ml']);
            $this->assertEquals(700.0, $overallSaving['saved_amount_ml']);
        } finally {
            \Carbon\Carbon::setTestNow();
        }
    }
}
