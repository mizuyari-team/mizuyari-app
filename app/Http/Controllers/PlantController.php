<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\UpdatePlantRequest;
use App\Models\Plant;
use App\Services\PredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PlantController extends Controller
{
    public function __construct(
        private PredictionService $predictionService
    ) {}

    public function index(Request $request)
    {
        $plants = $request->user()
            ->plants()
            ->where('is_demo', false)
            ->with('wateringLogs')
            ->get()
            ->map(function (Plant $plant) {
                $plant->next_watering_date = $this->predictNextDate($plant);

                return $plant;
            })
            ->sortBy(function (Plant $plant) {
                return $plant->next_watering_date ? $plant->next_watering_date->timestamp : PHP_INT_MAX;
            })
            ->values();

        return view('plants.index', compact('plants'));
    }

    public function create()
    {
        return view('plants.create');
    }

    public function store(StorePlantRequest $request)
    {
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('plants', 'public');
        }

        $plant = $request->user()->plants()->create($data);

        return redirect()->route('plants.show', $plant);
    }

    public function show(Plant $plant)
    {
        $this->authorize('view', $plant);

        $plant->load('wateringLogs');
        $plant->next_watering_date = $this->predictNextDate($plant);

        return view('plants.show', compact('plant'));
    }

    public function edit(Plant $plant)
    {
        $this->authorize('update', $plant);

        return view('plants.edit', compact('plant'));
    }

    public function update(UpdatePlantRequest $request, Plant $plant)
    {
        $this->authorize('update', $plant);

        $data = $request->safe()->except('photo');
        $oldPath = $plant->photo_path;
        $newPath = null;

        if ($request->hasFile('photo')) {
            $newPath = $request->file('photo')->store('plants', 'public');
            $data['photo_path'] = $newPath;
        }

        try {
            $plant->update($data);
        } catch (Throwable $e) {
            // 更新に失敗したら、保存した新しい写真を消して元の状態に戻す
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $e;
        }

        // 更新が成功してから、古い写真を消す
        if ($newPath && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('plants.show', $plant);
    }

    public function destroy(Plant $plant)
    {
        $this->authorize('delete', $plant);

        if ($plant->photo_path) {
            Storage::disk('public')->delete($plant->photo_path);
        }
        $plant->delete();

        return redirect()->route('plants.index');
    }

    private function predictNextDate(Plant $plant)
    {
        return $this->predictionService->predictNextWateringDate(
            $plant->wateringLogs->pluck('watered_at'),
            $plant->default_interval_days
        );
    }
}