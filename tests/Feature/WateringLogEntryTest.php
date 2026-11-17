<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WateringLogEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_displays_the_current_time_in_japan(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-17 03:34:56', 'UTC'));
        $owner = User::factory()->create();
        $plant = Plant::factory()->for($owner)->create();

        $response = $this->actingAs($owner)->get(route('plants.watering-logs.create', $plant));

        $response->assertOk()->assertSee('2026-11-17T12:34')->assertSee('mL');
    }

    public function test_owner_adds_a_log_and_sees_it_as_the_latest_record(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-17 03:34:56', 'UTC'));
        $owner = User::factory()->create();
        $plant = Plant::factory()->for($owner)->create();
        WateringLog::factory()->for($plant)->create([
            'watered_at' => '2026-11-16 12:00:00',
            'memo' => '以前の記録',
        ]);

        $response = $this->actingAs($owner)->post(route('plants.watering-logs.store', $plant), [
            'watered_at' => '2026-11-17T12:34',
            'amount_ml' => 175,
            'memo' => '土が乾いていた',
        ]);

        $response->assertRedirect(route('plants.show', $plant));
        $this->assertDatabaseHas('watering_logs', [
            'plant_id' => $plant->id,
            'watered_at' => '2026-11-17 03:34:00',
            'amount_ml' => 175,
            'memo' => '土が乾いていた',
        ]);
        $this->get(route('plants.show', $plant))
            ->assertOk()
            ->assertSee('175 mL')
            ->assertSee('2026/11/17 12:34')
            ->assertSee('土が乾いていた')
            ->assertDontSee('以前の記録');
    }

    public function test_rejects_future_dates_and_invalid_quantities_and_memo_lengths(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-17 03:34:56', 'UTC'));
        $owner = User::factory()->create();
        $plant = Plant::factory()->for($owner)->create();

        $response = $this->actingAs($owner)->post(route('plants.watering-logs.store', $plant), [
            'watered_at' => '2026-11-17T12:35',
            'amount_ml' => 0,
            'memo' => str_repeat('多', 1001),
        ]);

        $response->assertRedirect(route('plants.watering-logs.create', $plant))
            ->assertSessionHasErrors(['watered_at', 'amount_ml', 'memo']);
        $this->assertDatabaseMissing('watering_logs', ['plant_id' => $plant->id]);
    }

    public function test_other_users_cannot_open_the_form_or_add_a_log(): void
    {
        $plant = Plant::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other)->get(route('plants.watering-logs.create', $plant))->assertForbidden();
        $this->actingAs($other)->post(route('plants.watering-logs.store', $plant), [
            'watered_at' => '2026-11-16T12:34',
            'amount_ml' => 100,
        ])->assertForbidden();

        $this->assertDatabaseMissing('watering_logs', ['plant_id' => $plant->id]);
    }
}
