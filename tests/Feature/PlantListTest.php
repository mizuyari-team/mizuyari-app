<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantListTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_the_plant_photo_above_its_name(): void
    {
        $user = User::factory()->create();
        $plantWithPhoto = Plant::factory()->for($user)->create([
            'name' => 'モンステラ',
            'photo_path' => 'plants/monstera.jpg',
        ]);
        Plant::factory()->for($user)->create([
            'name' => 'ポトス',
            'photo_path' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSeeInOrder([route('plants.photo', $plantWithPhoto), 'モンステラ'])
            ->assertSeeInOrder(['写真未登録', 'ポトス']);
    }

    public function test_dashboard_displays_an_empty_state_when_no_plants_are_registered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()->assertSee('登録された植物はありません。');
    }
}
