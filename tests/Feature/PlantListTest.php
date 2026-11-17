<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Carbon\CarbonImmutable;
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

    public function test_dashboard_groups_predictions_by_the_calendar_date_in_japan(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-17 00:30:00', 'Asia/Tokyo'));
        $user = User::factory()->create();

        foreach ([
            '予測日を過ぎた鉢' => '2026-11-16 23:50:00',
            '今日の鉢' => '2026-11-17 00:15:00',
            '明日の鉢' => '2026-11-18 00:15:00',
        ] as $name => $prediction) {
            $plant = Plant::factory()->for($user)->create([
                'name' => $name,
                'default_interval_days' => 7,
            ]);
            WateringLog::factory()->for($plant)->create([
                'watered_at' => CarbonImmutable::parse($prediction, 'Asia/Tokyo')->subDays(7)->utc(),
            ]);
        }

        Plant::factory()->for($user)->create(['name' => '記録前の苗']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()->assertSeeInOrder([
            '予測日を過ぎた鉢',
            '予測日を過ぎています',
            '今日の鉢',
            '予測日が今日',
            '次の予定',
            '明日の鉢',
            'あと1日',
            '予測日未定',
            '記録前の苗',
        ]);
    }
}
