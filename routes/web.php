<?php

use App\Http\Controllers\PlantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WateringLogEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [PlantController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('plants', PlantController::class);
    Route::get('/plants/{plant}/photo', [PlantController::class, 'photo'])->name('plants.photo');
    Route::resource('plants.watering-logs', WateringLogEntryController::class)
        ->only(['create', 'store'])
        ->scoped();
});

require __DIR__.'/auth.php';
