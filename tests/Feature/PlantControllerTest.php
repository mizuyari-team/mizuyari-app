<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_plants_sorted_by_next_predicted_date()
    {
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

        $plants = $response->viewData('plants');
        $this->assertEquals($plantSoon->id, $plants->first()->id);
    }

    public function test_show_displays_own_plant()
    {
        $user = User::factory()->create();
        $plant = Plant::factory()->create([
            'user_id' => $user->id,
            'is_demo' => false,
        ]);

        $response = $this->actingAs($user)->get(route('plants.show', $plant));

        $response->assertStatus(200);
        $response->assertSee($plant->name);
    }

    public function test_show_forbids_viewing_other_users_plant()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherPlant = Plant::factory()->create([
            'user_id' => $otherUser->id,
            'is_demo' => false,
        ]);

        $response = $this->actingAs($user)->get(route('plants.show', $otherPlant));

        $response->assertStatus(403);
    }
}
