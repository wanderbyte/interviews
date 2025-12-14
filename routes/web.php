<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\TransactionController;

Route::get('/', [HomeController::class, 'index'])->name('dashboard');
/*
|--------------------------------------------------------------------------
| Category Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{category}', [CategoryController::class, 'update']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Material Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::get('/materials', [MaterialController::class, 'index']);
Route::post('/materials', [MaterialController::class, 'store']);
Route::put('/materials/{material}', [MaterialController::class, 'update']);
Route::delete('/materials/{material}', [MaterialController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Inward / Outward Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::post('/material-transactions', [TransactionController::class, 'store']);
