<?php

namespace App\Http\Controllers;

use App\Services\SavingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function __construct(
        private SavingService $savingService
    ) {}

    public function index(Request $request)
    {
        // 集計期間の日数（デフォルト30日、1〜365日の範囲で指定可能）
        $days = max(1, min(365, (int) $request->query('days', 30)));
        $startDate = now()->subDays($days)->startOfDay();

        // デモ植物（is_demo = true）を除外して自分の植物を取得
        $plants = $request->user()
            ->plants()
            ->where('is_demo', false)
            ->with('wateringLogs')
            ->get();

        // 1. 植物別の集計 & 全体の節水量計算
        $totalBaselineMl = 0.0;
        $totalActualMl = 0.0;
        $plantSummaries = [];

        foreach ($plants as $plant) {
            $periodLogs = $plant->wateringLogs->filter(function ($log) use ($startDate) {
                return Carbon::parse($log->watered_at)->gte($startDate);
            });

            $actualList = $periodLogs->pluck('amount_ml')->all();
            $dailyBase = (float) ($plant->daily_base_ml ?? 500.0);

            $saving = $this->savingService->calculateSavings($days, $actualList, $dailyBase);

            $totalBaselineMl += $saving['baseline_amount_ml'];
            $totalActualMl += $saving['actual_amount_ml'];

            $plantSummaries[] = [
                'plant_id' => $plant->id,
                'plant_name' => $plant->name,
                'daily_base_ml' => $dailyBase,
                'baseline_amount_ml' => $saving['baseline_amount_ml'],
                'actual_amount_ml' => $saving['actual_amount_ml'],
                'saved_amount_ml' => $saving['saved_amount_ml'],
                'saving_rate' => $saving['saving_rate'],
            ];
        }

        $totalSavedMl = $totalBaselineMl - $totalActualMl;
        $totalSavingRate = $totalBaselineMl > 0
            ? round(($totalSavedMl / $totalBaselineMl) * 100, 1)
            : 0.0;

        $overallSaving = [
            'days' => $days,
            'baseline_amount_ml' => $totalBaselineMl,
            'actual_amount_ml' => $totalActualMl,
            'saved_amount_ml' => $totalSavedMl,
            'saving_rate' => $totalSavingRate,
        ];

        // 2. 月別の給水量集計（直近6ヶ月分・Chart.js用）
        $monthlyTotals = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $monthlyTotals[$monthKey] = 0;
        }

        foreach ($plants as $plant) {
            foreach ($plant->wateringLogs as $log) {
                $monthKey = Carbon::parse($log->watered_at)->format('Y-m');
                if (array_key_exists($monthKey, $monthlyTotals)) {
                    $monthlyTotals[$monthKey] += (int) $log->amount_ml;
                }
            }
        }

        $monthlySummary = [
            'labels' => array_keys($monthlyTotals),
            'values' => array_values($monthlyTotals),
        ];

        return view('summary.index', compact(
            'overallSaving',
            'plantSummaries',
            'monthlySummary',
            'days'
        ));
    }
}
