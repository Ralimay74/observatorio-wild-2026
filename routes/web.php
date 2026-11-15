<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VotacionController;

Route::get('/', [VotacionController::class, 'index']);
Route::post('/votar', [VotacionController::class, 'votar'])->name('votar');
