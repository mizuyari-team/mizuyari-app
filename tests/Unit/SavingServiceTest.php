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
     * F-12 に基づき想定給水量・節水量（ml）・節水率（%）が正しく計算されるか
     */
    public function test_calculates_water_savings_according_to_f12_spec(): void
    {
        // 10日間、1日あたり500ml想定（計5000ml）
        // 実際は3回給水（各1000ml ＝ 計3000ml）
        $actualLogs = [1000, 1000, 1000];

        $result = $this->service->calculateSavings(10, $actualLogs, 500.0);

        $this->assertEquals(5000.0, $result['baseline_amount_ml']); // 想定: 5000ml
        $this->assertEquals(3000.0, $result['actual_amount_ml']);   // 実績: 3000ml
        $this->assertEquals(2000.0, $result['saved_amount_ml']);    // 節水: 2000ml
        $this->assertEquals(40.0, $result['saving_rate']);         // 節水率: 40%
    }

    /**
     * 与えすぎた場合に節水量がマイナス値で返るか
     */
    public function test_returns_negative_saved_amount_when_overwatered(): void
    {
        // 5日間（想定2500ml）に対して 3000ml 給水
        $result = $this->service->calculateSavings(5, [3000], 500.0);

        $this->assertEquals(-500.0, $result['saved_amount_ml']);
    }
}
