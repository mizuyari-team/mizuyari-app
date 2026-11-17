<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PlantControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_plants_sorted_by_next_predicted_date()
    {
        // テスト用の一時フォルダに仮のBladeファイルを置く（本番コードは汚さない）
        $tempViewPath = storage_path('framework/testing/views');
        File::ensureDirectoryExists($tempViewPath . '/plants');
        File::put($tempViewPath . '/plants/index.blade.php', 'dummy');
        View::addLocation($tempViewPath);

        $user = User::factory()->create();

        // 予測日が遠い植物（水やり直後）
        $plantFar = Plant::factory()->create([
            'user_id' => $user->id,
            'default_interval_days' => 7,
            'is_demo' => false,
        ]);
        WateringLog::factory()->create([
            'plant_id' => $plantFar->id,
            'watered_at' => now(),
        ]);

        // 予測日が近い植物（3日前に水やり）
        $plantSoon = Plant::factory()->create([
            'user_id' => $user->id,
            'default_interval_days' => 5,
            'is_demo' => false,
        ]);
        WateringLog::factory()->create([
            'plant_id' => $plantSoon->id,
            'watered_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($user)->get(route('plants.index'));

        $response->assertStatus(200);

        // View に渡された plants の並び順を検証（予測日が近い $plantSoon が先頭）
        $plants = $response->viewData('plants');
        $this->assertEquals($plantSoon->id, $plants->first()->id);
    }
}
