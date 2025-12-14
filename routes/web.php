<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// CACHE CLEAR ROUTE
Route::get('cache-clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    session()->flash('success', 'Successfully cache cleared.');
    return redirect()->back();
})->name('cache.clear');

Route::get('/', [HomeController::class, 'index'])->name('dashboard');
