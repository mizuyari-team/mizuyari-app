<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\WateringLog;
use Illuminate\Http\Request;

class WateringLogController extends Controller
{
    public function index(Plant $plant)
    {
        $this->authorizePlant($plant);

        $wateringLogs = $plant->wateringLogs()
            ->latest('watered_at')
            ->paginate(10);

        return view('watering-logs.index', compact('plant', 'wateringLogs'));
    }

    public function edit(Plant $plant, WateringLog $wateringLog)
    {
        $this->ensurePlantOwner($plant);

        return view('watering-logs.edit', compact('plant', 'wateringLog'));
    }

    public function update(Request $request, Plant $plant, WateringLog $wateringLog)
    {
        $this->ensurePlantOwner($plant);

        $validated = $request->validate([
            'watered_at' => ['required', 'date'],
            'amount_ml' => ['required', 'integer', 'min:1'],
            'memo' => ['nullable', 'string', 'max:1000'],
        ]);

        $wateringLog->update($validated);

        return redirect()
            ->route('plants.watering-logs.index', $plant)
            ->with('success', '水やり記録を更新しました。');
    }

    public function destroy(Plant $plant, WateringLog $wateringLog)
    {
        $this->ensurePlantOwner($plant);

        $wateringLog->delete();

        return redirect()
            ->route('plants.watering-logs.index', $plant)
            ->with('success', '水やり記録を削除しました。');
    }

    private function ensurePlantOwner(Plant $plant): void
    {
        abort_if($plant->user_id !== auth()->id(), 403);
    }
}
