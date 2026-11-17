<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Services\PredictionService;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function __construct(
        private PredictionService $predictionService
    ) {}

    public function index(Request $request)
    {
        $plants = $request->user()
            ->plants()
            ->with('wateringLogs')
            ->get()
            ->map(function (Plant $plant) {
                $plant->next_watering_date = $this->predictionService->predictNextWateringDate(
                    $plant->wateringLogs->pluck('watered_at'),
                    $plant->default_interval_days
                );
                return $plant;
            })
            ->sortBy(function (Plant $plant) {
                return $plant->next_watering_date ? $plant->next_watering_date->timestamp : PHP_INT_MAX;
            })
            ->values();

        return view('plants.index', compact('plants'));
    }

    public function show(Request $request, Plant $plant)
    {
        abort_if($plant->user_id !== $request->user()->id, 403);

        $plant->load('wateringLogs');
        $plant->next_watering_date = $this->predictionService->predictNextWateringDate(
            $plant->wateringLogs->pluck('watered_at'),
            $plant->default_interval_days
        );

        return view('plants.show', compact('plant'));
    }
}
