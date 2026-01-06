<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\StandardController;


Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::prefix('masters')->name('masters.')->group(function () {

    Route::get('standards', [StandardController::class, 'index'])->name('standards');
    Route::post('standards/save', [StandardController::class, 'save'])->name('standards.save');
    Route::delete('standards/{standard}', [StandardController::class, 'destroy'])->name('standards.destroy');

    Route::get('states', [StateController::class, 'index'])->name('states');
    Route::post('states/save', [StateController::class, 'save'])->name('states.save');
    Route::delete('states/{state}', [StateController::class, 'destroy'])->name('states.destroy');

    Route::get('districts', [DistrictController::class, 'index'])->name('districts.index');
    Route::post('districts/save', [DistrictController::class, 'save'])->name('districts.save');
    Route::delete('districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy');

    Route::get('cities', [CityController::class, 'index'])->name('cities.index');
    Route::post('cities/save', [CityController::class, 'save'])->name('cities.save');
    Route::delete('cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');

    // AJAX – Get districts by state
    Route::get('districts/by-state/{state}', [CityController::class, 'getDistricts'])->name('districts.by-state');
});
