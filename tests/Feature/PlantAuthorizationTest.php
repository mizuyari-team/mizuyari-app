<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_他人の植物は表示・編集・更新・削除できない(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $plant = Plant::factory()->for($owner)->create(['name' => '元の名前']);

        $this->actingAs($other)->get(route('plants.show', $plant))->assertForbidden();
        $this->actingAs($other)->get(route('plants.edit', $plant))->assertForbidden();
        $this->actingAs($other)->put(route('plants.update', $plant), [
            'name' => '乗っ取り',
            'daily_base_ml' => 100,
            'default_interval_days' => 7,
        ])->assertForbidden();
        $this->actingAs($other)->delete(route('plants.destroy', $plant))->assertForbidden();

        // 実際に変更・削除されていないことも確認する
        $this->assertDatabaseHas('plants', ['id' => $plant->id, 'name' => '元の名前']);
    }

    public function test_user_idをフォームで送っても本人名義で作られる(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($me)->post(route('plants.store'), [
            'name' => '新しい植物',
            'daily_base_ml' => 100,
            'default_interval_days' => 7,
            'user_id' => $other->id,
        ]);

        $this->assertDatabaseHas('plants', ['name' => '新しい植物', 'user_id' => $me->id]);
        $this->assertDatabaseMissing('plants', ['name' => '新しい植物', 'user_id' => $other->id]);
    }

    public function test_ログインしていないと植物にアクセスできない(): void
    {
        $plant = Plant::factory()->create();

        $this->get(route('plants.index'))->assertRedirect(route('login'));
        $this->get(route('plants.show', $plant))->assertRedirect(route('login'));
    }

    public function test_自分の植物は表示できる(): void
    {
        $this->skipUnlessViewExists('plants.show');

        $owner = User::factory()->create();
        $plant = Plant::factory()->for($owner)->create();

        $this->actingAs($owner)->get(route('plants.show', $plant))->assertOk();
    }

    public function test_一覧には自分の植物だけ出る(): void
    {
        $this->skipUnlessViewExists('plants.index');

        $me = User::factory()->create();
        $other = User::factory()->create();
        Plant::factory()->for($me)->create(['name' => '自分の植物']);
        Plant::factory()->for($other)->create(['name' => '他人の植物']);

        $this->actingAs($me)->get(route('plants.index'))
            ->assertOk()
            ->assertSee('自分の植物')
            ->assertDontSee('他人の植物');
    }

    // ③の画面（Blade）が届くまでは、画面が必要なテストを自動でスキップする
    private function skipUnlessViewExists(string $view): void
    {
        if (! view()->exists($view)) {
            $this->markTestSkipped("ビュー {$view} がまだ無いためスキップ");
        }
    }
}
