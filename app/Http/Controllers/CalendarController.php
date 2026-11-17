<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StoreWateringLogRequest;
use App\Services\PredictionService;

class CalendarController extends Controller
{
    public function __construct(
        private PredictionService $predictionService,
    ) {}

    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
        ]);

        $start = Carbon::parse($validated['start'])->startOfDay();
        $end = Carbon::parse($validated['end'])->endOfDay();

        $plants = Plant::query()
            ->where('user_id', auth()->id())
            ->where('is_demo', false)
            ->with([
                'wateringLogs' => fn ($query) => $query
                    ->whereBetween('watered_at', [$start, $end])
                    ->latest('watered_at'),
            ])
            ->get();

        $events = [];

        foreach ($plants as $plant) {
            foreach ($plant->wateringLogs as $wateringLog) {
                $events[] = [
                    'title' => "{$plant->name}：{$wateringLog->amount_ml}ml",
                    'start' => $wateringLog->watered_at->toIso8601String(),
                    'color' => '#16a34a',
                    'url' => route('plants.watering-logs.index', $plant),
                ];
            }

            $predictedDate = $this->predictNextWateringDate($plant);

            if ($predictedDate && $predictedDate->betweenIncluded($start, $end)) {
                $events[] = [
                    'title' => "{$plant->name}：水やり予測",
                    'start' => $predictedDate->toDateString(),
                    'allDay' => true,
                    'color' => '#f59e0b',
                    'url' => route('plants.watering-logs.index', $plant),
                ];
            }
        }

        return response()->json($events);
    }

    private function predictNextWateringDate(Plant $plant): ?Carbon
    {
        return $this->predictionService->predictNextWateringDate(
            $plant->wateringLogs()->pluck('watered_at'),
            $plant->default_interval_days,
        )?->setTimezone(StoreWateringLogRequest::TIMEZONE);
    }
}