<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WateringLogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_another_users_watering_logs(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherPlant = Plant::factory()
            ->for($otherUser)
            ->create();

        $this->actingAs($user)
            ->get(route('plants.watering-logs.index', $otherPlant))
            ->assertForbidden();
    }
}
