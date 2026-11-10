<?php

namespace App\Services;

class SavingService
{
    /**
     * 要件定義 F-12 に基づき節水量（ml）および節水率（%）を計算する
     *
     * @param int $totalDays 期間日数
     * @param array $actualAmountMlList 実際の給水量（amount_ml）の配列
     * @param float $dailyBaseMl 1日あたりの想定給水量（ml）
     * @return array
     */
    public function calculateSavings(
        int $totalDays,
        array $actualAmountMlList,
        float $dailyBaseMl = 500.0
    ): array {
        // 想定給水量（ml）
        $baselineAmountMl = $dailyBaseMl * $totalDays;

        // 実際の給水量の合計（ml）
        $actualAmountMl = (float) array_sum($actualAmountMlList);

        // 節水量（ml）※与えすぎの場合はマイナス
        $savedAmountMl = $baselineAmountMl - $actualAmountMl;

        // 節水率（%）
        $savingRate = $baselineAmountMl > 0
            ? round(($savedAmountMl / $baselineAmountMl) * 100, 1)
            : 0.0;

        return [
            'baseline_amount_ml' => $baselineAmountMl,
            'actual_amount_ml' => $actualAmountMl,
            'saved_amount_ml' => $savedAmountMl,
            'saving_rate' => $savingRate,
        ];
    }
}
