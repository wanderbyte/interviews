<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SchoolController;


Route::get('school', [SchoolController::class, 'fetch'])
    ->middleware('encrypt.decrypt');

Route::get('school/decrypt', [SchoolController::class, 'decryptPayload']);