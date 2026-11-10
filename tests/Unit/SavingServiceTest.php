<?php

namespace Tests\Unit;

use App\Services\SavingService;
use PHPUnit\Framework\TestCase;

class SavingServiceTest extends TestCase
{
    private SavingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SavingService();
    }

    /**
     * 節水量と節水率が正確に計算されるか（30日間・従来30回 vs 予測実績10回）
     */
    public function test_calculates_water_savings_correctly(): void
    {
        // 30日間, 実績10回, 1回10L, 固定周期1日1回
        $result = $this->service->calculateSavings(30, 10, 10.0, 1);

        $this->assertEquals(300.0, $result['baseline_amount']); // 従来: 300L
        $this->assertEquals(100.0, $result['actual_amount']);   // 実績: 100L
        $this->assertEquals(200.0, $result['saved_amount']);    // 節水: 200L
        $this->assertEquals(66.7, $result['saving_rate']);     // 節水率: 66.7%
    }
}
