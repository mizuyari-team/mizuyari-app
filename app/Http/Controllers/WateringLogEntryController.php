<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWateringLogRequest;
use App\Models\Plant;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WateringLogEntryController extends Controller
{
    public function create(Plant $plant): View
    {
        $this->authorize('view', $plant);

        return view('watering-logs.create', compact('plant'));
    }

    public function store(StoreWateringLogRequest $request, Plant $plant): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['watered_at'] = CarbonImmutable::createFromFormat(
            '!'.StoreWateringLogRequest::DATETIME_FORMAT,
            $attributes['watered_at'],
            StoreWateringLogRequest::TIMEZONE,
        )->setTimezone(config('app.timezone'));

        $plant->wateringLogs()->create($attributes);

        return redirect()->route('plants.show', $plant)->with('success', '水やりを記録しました。');
    }
}
