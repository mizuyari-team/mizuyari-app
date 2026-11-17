<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\StoreWateringLogRequest;
use App\Http\Requests\UpdatePlantRequest;
use App\Models\Plant;
use App\Services\PredictionService;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PlantController extends Controller
{
    public function __construct(
        private PredictionService $predictionService
    ) {}

    public function index(Request $request): View
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
            ->sortBy(fn (Plant $plant) => $plant->next_watering_date?->timestamp ?? PHP_INT_MAX)
            ->values();

        $today = today(StoreWateringLogRequest::TIMEZONE);
        $tomorrow = $today->copy()->addDay();
        $schedule = $plants->groupBy(fn (Plant $plant) => match (true) {
            $plant->next_watering_date === null => 'undated',
            $plant->next_watering_date->lt($tomorrow) => 'due',
            default => 'upcoming',
        });

        return view('plants.index', compact('plants', 'today', 'schedule'));
    }

    public function create(): View
    {
        return view('plants.create');
    }

    public function store(StorePlantRequest $request): RedirectResponse
    {
        $plant = $request->user()->plants()->make();
        $this->savePlant($plant, $request->safe()->except('photo'), $request->file('photo'));

        return redirect()->route('plants.show', $plant)->with('success', '植物を登録しました。');
    }

    public function show(Plant $plant): View
    {
        $this->authorize('view', $plant);

        $plant->load(['wateringLogs' => fn ($query) => $query->latest('watered_at')->latest('id')]);
        $plant->next_watering_date = $this->predictNextDate($plant);

        return view('plants.show', compact('plant'));
    }

    public function edit(Plant $plant): View
    {
        $this->authorize('update', $plant);

        return view('plants.edit', compact('plant'));
    }

    public function update(UpdatePlantRequest $request, Plant $plant): RedirectResponse
    {
        $this->savePlant($plant, $request->safe()->except('photo'), $request->file('photo'));

        return redirect()->route('plants.show', $plant)->with('success', '植物の情報を更新しました。');
    }

    public function destroy(Plant $plant): RedirectResponse
    {
        $this->authorize('delete', $plant);

        $photoPath = $plant->getConnection()->transaction(function () use ($plant) {
            $record = Plant::query()->lockForUpdate()->findOrFail($plant->getKey());
            $record->delete();

            return $record->photo_path;
        });
        $this->deletePhoto($photoPath);

        return redirect()->route('plants.index')->with('success', '植物を削除しました。');
    }

    public function photo(Plant $plant): StreamedResponse
    {
        $this->authorize('view', $plant);

        abort_unless($plant->photo_path, 404);

        $disk = $this->photoDisk($plant->photo_path);
        abort_unless($disk->exists($plant->photo_path), 404);

        return $disk->response($plant->photo_path, headers: ['Cache-Control' => 'private, no-store']);
    }

    /** @param array<string, mixed> $attributes */
    private function savePlant(Plant $plant, array $attributes, ?UploadedFile $photo): void
    {
        if ($photo === null) {
            $plant->fill($attributes)->save();

            return;
        }

        $path = $photo->store('plants', 'local');
        abort_if($path === false, 500, '写真を保存できませんでした。');

        try {
            $previousPath = $plant->getConnection()->transaction(function () use ($plant, $attributes, $path) {
                $record = $plant->exists ? Plant::query()->lockForUpdate()->findOrFail($plant->getKey()) : $plant;
                $previousPath = $record->photo_path;
                $record->fill($attributes)->fill(['photo_path' => $path])->save();

                return $previousPath;
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        $this->deletePhoto($previousPath);
    }

    private function deletePhoto(?string $path): void
    {
        if ($path !== null && $path !== '') {
            $this->photoDisk($path)->delete($path);
        }
    }

    private function photoDisk(string $path): FilesystemAdapter
    {
        $privateDisk = Storage::disk('local');

        return $privateDisk->exists($path) ? $privateDisk : Storage::disk('public');
    }

    private function predictNextDate(Plant $plant): ?Carbon
    {
        return $this->predictionService->predictNextWateringDate(
            $plant->wateringLogs->pluck('watered_at'),
            $plant->default_interval_days
        )?->setTimezone(StoreWateringLogRequest::TIMEZONE);
    }
}
