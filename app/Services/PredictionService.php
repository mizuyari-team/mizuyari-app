<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class PredictionService
{
    /**
     * 直近5回分の水やり間隔の平均から次回予測日を計算する
     *
     * @param Collection|array $waterLogs 水やり履歴の日付（Carbonオブジェクトまたは日付文字列の配列）
     * @param float $sunlightFactor 日当たり補正係数（デフォルト: 1.0 / 日向: 0.8 / 日陰: 1.2 など）
     * @return Carbon|null
     */
    public function predictNextWateringDate($waterLogs, float $sunlightFactor = 1.0): ?Carbon
    {
        $logs = collect($waterLogs)->map(fn($date) => Carbon::parse($date))->sort()->values();

        // 記録が2回未満の場合は計算不可（次回予測日はnull）
        if ($logs->count() < 2) {
            return null;
        }

        // 直近最大6回分の記録から間隔（最大5個）を抽出
        $recentLogs = $logs->take(-6);
        $intervals = [];

        for ($i = 0; $i < $recentLogs->count() - 1; $i++) {
            $intervals[] = $recentLogs[$i]->diffInDays($recentLogs[$i + 1]);
        }

        // 平均間隔（日数）の計算
        $averageInterval = array_sum($intervals) / count($intervals);

        // 日当たりなどの環境補正を適用
        $adjustedInterval = max(1, (int) round($averageInterval * $sunlightFactor));

        // 最後の水やり日に補正後間隔を足して次回予測日を算出
        $lastWateringDate = $recentLogs->last();

        return $lastWateringDate->copy()->addDays($adjustedInterval);
    }
}
