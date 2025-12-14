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
Route::post('/categories/save', [CategoryController::class, 'save']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Material Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::get('/materials', [MaterialController::class, 'index']);
Route::post('/materials/save', [MaterialController::class, 'save']);
Route::delete('/materials/{material}', [MaterialController::class, 'destroy']);
Route::get('/materials/by-category/{categoryId}', [MaterialController::class, 'getByCategory']);

/*
|--------------------------------------------------------------------------
| Inward / Outward (Material Transactions)
|--------------------------------------------------------------------------
*/
Route::get('/material-transactions', [TransactionController::class, 'index']);
Route::post('/material-transactions/save', [TransactionController::class, 'save']);
Route::delete('/material-transactions/{id}', [TransactionController::class, 'destroy']);
