<?php

namespace Tests\Unit;

use App\Services\PredictionService;
use PHPUnit\Framework\TestCase;

class PredictionServiceTest extends TestCase
{
    private PredictionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PredictionService();
    }

    /**
     * 直近5回の水やり間隔（平均4日）から正確に次回予測日が計算されるか
     */
    public function test_predicts_next_watering_date_correctly_with_five_intervals(): void
    {
        $logs = [
            '2026-10-01',
            '2026-10-04',
            '2026-10-08',
            '2026-10-11',
            '2026-10-16',
            '2026-10-21',
        ];

        $nextDate = $this->service->predictNextWateringDate($logs);

        $this->assertEquals('2026-10-25', $nextDate?->format('Y-m-d'));
    }

    /**
     * 7件以上の記録があってもキーが振り直されてエラーにならず正常計算されるか
     */
    public function test_handles_more_than_six_logs_without_index_error(): void
    {
        $logs = [
            '2026-10-01',
            '2026-10-03',
            '2026-10-05',
            '2026-10-07',
            '2026-10-09',
            '2026-10-11',
            '2026-10-13',
            '2026-10-15', // 直近6件は 10/05 〜 10/15（間隔はすべて2日）
        ];

        $nextDate = $this->service->predictNextWateringDate($logs);

        $this->assertEquals('2026-10-17', $nextDate?->format('Y-m-d'));
    }

    /**
     * 記録が0件の場合は null が返るか
     */
    public function test_returns_null_when_logs_are_empty(): void
    {
        $this->assertNull($this->service->predictNextWateringDate([]));
    }

    /**
     * 記録が3件未満（1〜2件）の場合は既定間隔が使用されるか
     */
    public function test_uses_default_interval_when_logs_are_less_than_three(): void
    {
        $logs = ['2026-10-01', '2026-10-05'];

        // 既定値 5日 を指定
        $nextDate = $this->service->predictNextWateringDate($logs, 5);

        // 10/05 + 5日 = 10/10
        $this->assertEquals('2026-10-10', $nextDate?->format('Y-m-d'));
    }
}
