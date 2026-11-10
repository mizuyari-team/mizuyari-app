<?php

namespace Tests\Unit;

use App\Services\PredictionService;
use Carbon\Carbon;
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
        // 10/1, 10/4(3日差), 10/8(4日差), 10/11(3日差), 10/16(5日差), 10/21(5日差)
        // 平均間隔: (3 + 4 + 3 + 5 + 5) / 5 = 4日
        $logs = [
            '2026-10-01',
            '2026-10-04',
            '2026-10-08',
            '2026-10-11',
            '2026-10-16',
            '2026-10-21',
        ];

        $nextDate = $this->service->predictNextWateringDate($logs);

        // 10/21 の 4日後 = 10/25
        $this->assertEquals('2026-10-25', $nextDate?->format('Y-m-d'));
    }

    /**
     * 記録が2回未満の場合は null（予測不可）が返るか
     */
    public function test_returns_null_when_logs_are_insufficient(): void
    {
        $logs = ['2026-10-01'];

        $nextDate = $this->service->predictNextWateringDate($logs);

        $this->assertNull($nextDate);
    }

    /**
     * 日当たり補正（日向: 0.8倍）が正しく計算されるか
     */
    public function test_applies_sunlight_factor_correctly(): void
    {
        // 平均間隔 5日
        $logs = [
            '2026-10-01',
            '2026-10-06',
            '2026-10-11',
        ];

        // 5日 * 0.8 = 4日
        $nextDate = $this->service->predictNextWateringDate($logs, 0.8);

        // 10/11 の 4日後 = 10/15
        $this->assertEquals('2026-10-15', $nextDate?->format('Y-m-d'));
    }
}
