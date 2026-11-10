<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class PredictionService
{
    /**
     * 水やり履歴から次回予測日を計算する
     *
     * @param Collection|array $waterLogs 水やり履歴の日付
     * @param int $defaultIntervalDays 記録不足時の既定間隔（日数）
     * @return Carbon|null
     */
    public function predictNextWateringDate($waterLogs, int $defaultIntervalDays = 7): ?Carbon
    {
        $logs = collect($waterLogs)->map(fn($date) => Carbon::parse($date))->sort()->values();

        // 0件の場合は予測不可
        if ($logs->isEmpty()) {
            return null;
        }

        $lastWateringDate = $logs->last();

        // 記録が3件未満（1件または2件）の場合は既定間隔を使用
        if ($logs->count() < 3) {
            return $lastWateringDate->copy()->addDays($defaultIntervalDays);
        }

        // 直近最大6件の記録を抽出し、キーを振り直す
        $recentLogs = $logs->take(-6)->values();
        $intervals = [];

        for ($i = 0; $i < $recentLogs->count() - 1; $i++) {
            $intervals[] = $recentLogs[$i]->diffInDays($recentLogs[$i + 1]);
        }

        $averageInterval = (int) round(array_sum($intervals) / count($intervals));

        return $lastWateringDate->copy()->addDays($averageInterval);
    }
}
