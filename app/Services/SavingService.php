<?php

namespace App\Services;

class SavingService
{
    /**
     * 節水量および節水率を計算する
     *
     * @param int $totalDays 対象期間の日数
     * @param int $actualWateringCount 予測に基づいて実際に水やりを行った回数
     * @param float $waterAmountPerTime 1回あたりの水やり量（L）
     * @param int $fixedIntervalDays 従来の固定周期（例: 1日1回なら1）
     * @return array
     */
    public function calculateSavings(
        int $totalDays,
        int $actualWateringCount,
        float $waterAmountPerTime = 10.0,
        int $fixedIntervalDays = 1
    ): array {
        // 従来の想定水やり回数
        $fixedCount = (int) floor($totalDays / $fixedIntervalDays);

        // 基準水量と実際の使用水量
        $baselineAmount = $fixedCount * $waterAmountPerTime;
        $actualAmount = $actualWateringCount * $waterAmountPerTime;

        // 節水量（L）
        $savedAmount = max(0.0, $baselineAmount - $actualAmount);

        // 節水率（%）
        $savingRate = $baselineAmount > 0
            ? round(($savedAmount / $baselineAmount) * 100, 1)
            : 0.0;

        return [
            'baseline_amount' => $baselineAmount,
            'actual_amount' => $actualAmount,
            'saved_amount' => $savedAmount,
            'saving_rate' => $savingRate,
        ];
    }
}
