<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\SchoolController;


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
    Route::get('districts/by-state/{state}', [CityController::class, 'getDistricts'])->name('get.districts');
    Route::get('get-cities/{district}', [CityController::class, 'cities'])->name('get.cities');
});

Route::get('schools', [SchoolController::class, 'index'])->name('schools.index');
Route::get('schools/create', [SchoolController::class, 'create'])->name('schools.create');
Route::get('schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
Route::post('schools/save', [SchoolController::class, 'save'])->name('schools.save');
Route::delete('schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');

/* Export PDF */
Route::get('schools/{school}/pdf', [SchoolController::class, 'exportPdf'])->name('schools.pdf');
