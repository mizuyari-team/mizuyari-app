<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Services\PredictionService;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function __construct(
        protected PredictionService $predictionService
    ) {}

    /**
     * 植物一覧（F-04）
     * 自分の植物カードを表示し、次回予測日が近い順に並べる
     */
    public function index(Request $request)
    {
        $plants = $request->user()->plants()
            ->where('is_demo', false)
            ->with(['wateringLogs' => function ($query) {
                $query->orderBy('watered_at', 'asc');
            }])
            ->get()
            ->map(function (Plant $plant) {
                // 水やり履歴の日時配列を取得
                $waterLogs = $plant->wateringLogs->pluck('watered_at')->toArray();

                // 次回予測日を計算（履歴3件未満は植物ごとの既定間隔を使用）
                $plant->next_watering_date = $this->predictionService->predictNextWateringDate(
                    $waterLogs,
                    $plant->default_interval_days
                );

                return $plant;
            })
            // 次回予測日が近い順（昇順）にソート（nullは最後）
            ->sortBy(function (Plant $plant) {
                return $plant->next_watering_date ? $plant->next_watering_date->timestamp : PHP_INT_MAX;
            })
            ->values();

        return view('plants.index', compact('plants'));
    }

    /**
     * 植物詳細（F-07）
     * 植物情報、最新の記録、次回予測日を表示
     */
    public function show(Plant $plant)
    {
        // 他人の植物は見られないように認可チェック
        $this->authorize('view', $plant);

        $plant->load(['wateringLogs' => function ($query) {
            $query->orderBy('watered_at', 'desc');
        }]);

        $waterLogs = $plant->wateringLogs->pluck('watered_at')->sort()->values()->toArray();

        $plant->next_watering_date = $this->predictionService->predictNextWateringDate(
            $waterLogs,
            $plant->default_interval_days
        );

        return view('plants.show', compact('plant'));
    }
}
