<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WateringLogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_watering_logs(): void
    {
        $user = User::factory()->create();

        $plant = $this->createPlantFor($user, [
            'name' => 'test_user',
        ]);

        $wateringLog = $this->createWateringLogFor($plant, [
            'amount_ml' => 250,
            'memo' => 'test_mizuyari',
        ]);

        $response = $this->actingAs($user)
            ->get(route('plants.watering-logs.index', $plant));

        $response->assertOk();
        $response->assertSee('test_user');
        $response->assertSee('250 ml');
        $response->assertSee('test_mizuyari');
    }

    public function test_user_cannot_view_another_users_watering_logs(): void
    {
        [$user, $otherPlant] = $this->otherUsersPlant();

        $this->actingAs($user)
            ->get(route('plants.watering-logs.index', $otherPlant))
            ->assertForbidden();
    }

    public function test_user_cannot_open_edit_page_for_another_users_watering_log(): void
    {
        [$user, $otherPlant, $otherWateringLog] = $this->otherUsersPlantWithLog();

        $this->actingAs($user)
            ->get(route('plants.watering-logs.edit', [$otherPlant, $otherWateringLog]))
            ->assertForbidden();
    }

    public function test_user_cannot_update_another_users_watering_log(): void
    {
        [$user, $otherPlant, $otherWateringLog] = $this->otherUsersPlantWithLog();

        $this->actingAs($user)
            ->put(
                route('plants.watering-logs.update', [$otherPlant, $otherWateringLog]),
                [
                    'watered_at' => now()->subHour()->format('Y-m-d H:i:s'),
                    'amount_ml' => 500,
                    'memo' => 'wrong_update',
                ],
            )
            ->assertForbidden();

        $this->assertDatabaseMissing('watering_logs', [
            'id' => $otherWateringLog->id,
            'amount_ml' => 500,
        ]);
    }

    public function test_user_cannot_delete_another_users_watering_log(): void
    {
        [$user, $otherPlant, $otherWateringLog] = $this->otherUsersPlantWithLog();

        $this->actingAs($user)
            ->delete(
                route('plants.watering-logs.destroy', [$otherPlant, $otherWateringLog]),
            )
            ->assertForbidden();

        $this->assertDatabaseHas('watering_logs', [
            'id' => $otherWateringLog->id,
        ]);
    }

    private function otherUsersPlant(): array
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        return [$user, $this->createPlantFor($otherUser)];
    }

    private function otherUsersPlantWithLog(): array
    {
        [$user, $otherPlant] = $this->otherUsersPlant();
        $otherWateringLog = $this->createWateringLogFor($otherPlant);

        return [$user, $otherPlant, $otherWateringLog];
    }

    private function createPlantFor(User $user, array $overrides = []): Plant
    {
        return Plant::forceCreate(array_merge([
            'user_id' => $user->id,
            'name' => 'test_plant',
            'species' => 'test_plant',
            'location' => 'test_location',
            'pot_size' => 'middle',
            'daily_base_ml' => 200,
            'default_interval_days' => 7,
        ], $overrides));
    }

    private function createWateringLogFor(Plant $plant, array $overrides = []): WateringLog
    {
        return WateringLog::forceCreate(array_merge([
            'plant_id' => $plant->id,
            'watered_at' => now()->subDay(),
            'amount_ml' => 200,
            'memo' => 'test_record',
        ], $overrides));
    }
}