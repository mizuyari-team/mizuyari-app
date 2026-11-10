<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WateringLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/plants/{plant}/watering-logs', [WateringLogController::class, 'index'])
        ->name('plants.watering-logs.index');

    Route::get('/plants/{plant}/watering-logs/{wateringLog}/edit', [WateringLogController::class, 'edit'])
        ->name('plants.watering-logs.edit');

    Route::put('/plants/{plant}/watering-logs/{wateringLog}', [WateringLogController::class, 'update'])
        ->name('plants.watering-logs.update');

    Route::delete('/plants/{plant}/watering-logs/{wateringLog}', [WateringLogController::class, 'destroy'])
        ->name('plants.watering-logs.destroy');
});

require __DIR__.'/auth.php';
