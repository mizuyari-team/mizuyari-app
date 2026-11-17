<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use App\Models\WateringLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlantManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_registers_a_plant_with_a_private_photo(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->post(route('plants.store'), [
            'name' => '窓辺のモンステラ',
            'species' => 'モンステラ',
            'location' => '図書室の南向きの窓',
            'pot_size' => '中',
            'daily_base_ml' => 120,
            'photo' => UploadedFile::fake()->image('plant.jpg', 24, 24),
        ]);

        $plant = $owner->plants()->sole();
        $response->assertRedirect(route('plants.show', $plant));
        $this->assertDatabaseHas('plants', [
            'id' => $plant->id,
            'name' => '窓辺のモンステラ',
            'pot_size' => '中',
            'daily_base_ml' => 120,
            'default_interval_days' => 7,
        ]);
        Storage::disk('local')->assertExists($plant->photo_path);
        $this->get(route('plants.show', $plant))
            ->assertOk()
            ->assertSeeInOrder([route('plants.photo', $plant), '窓辺のモンステラ'])
            ->assertSee('図書室の南向きの窓');
        $this->get(route('plants.photo', $plant))->assertOk()->assertStreamed();
        $this->actingAs(User::factory()->create())->get(route('plants.photo', $plant))->assertForbidden();
    }

    public function test_owner_updates_the_plant_and_replaces_its_existing_photo(): void
    {
        Storage::fake('local');
        Storage::fake('public')->put('plants/previous.jpg', 'previous photo');
        $owner = User::factory()->create();
        $plant = Plant::factory()->for($owner)->create([
            'photo_path' => 'plants/previous.jpg',
            'default_interval_days' => 11,
        ]);

        $response = $this->actingAs($owner)->patch(route('plants.update', $plant), [
            'name' => '新しい植物名',
            'location' => '廊下の棚',
            'pot_size' => '小',
            'daily_base_ml' => 150,
            'photo' => UploadedFile::fake()->image('new.png', 24, 24),
        ]);

        $response->assertRedirect(route('plants.show', $plant));
        $this->assertDatabaseHas('plants', [
            'id' => $plant->id,
            'name' => '新しい植物名',
            'location' => '廊下の棚',
            'default_interval_days' => 11,
        ]);
        Storage::disk('local')->assertExists($plant->refresh()->photo_path);
        Storage::disk('public')->assertMissing('plants/previous.jpg');
    }

    public function test_registration_rejects_large_photos_and_invalid_pot_sizes(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->post(route('plants.store'), [
            'name' => '入力確認',
            'pot_size' => '150 mm',
            'daily_base_ml' => 100,
            'photo' => UploadedFile::fake()->image('large.png')->size(5121),
        ]);

        $response->assertRedirect(route('plants.create'))->assertSessionHasErrors(['pot_size', 'photo']);
        $this->assertDatabaseMissing('plants', ['user_id' => $owner->id]);
    }

    public function test_owner_deletes_the_plant_with_its_photo_and_logs(): void
    {
        Storage::fake('local')->put('plants/detail.jpg', 'plant photo');
        $owner = User::factory()->create();
        $plant = Plant::factory()->for($owner)->create(['photo_path' => 'plants/detail.jpg']);
        $log = WateringLog::factory()->for($plant)->create();

        $response = $this->actingAs($owner)->delete(route('plants.destroy', $plant));

        $response->assertRedirect(route('plants.index'));
        $this->assertModelMissing($plant);
        $this->assertModelMissing($log);
        Storage::disk('local')->assertMissing('plants/detail.jpg');
    }
}
