<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Http\Requests\UpdatePlantRequest;
use App\Models\Plant;
use Illuminate\Support\Facades\Storage;

class PlantController extends Controller
{
    public function index()
    {
        $plants = auth()->user()->plants()
            ->where('is_demo', false)
            ->orderBy('name')
            ->get();

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

        $plant->load(['wateringLogs' => fn ($q) => $q->latest('watered_at')]);

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

        if ($request->hasFile('photo')) {
            if ($plant->photo_path) {
                Storage::disk('public')->delete($plant->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('plants', 'public');
        }

        $plant->update($data);

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
}
