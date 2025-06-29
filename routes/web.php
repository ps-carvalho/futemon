<?php

declare(strict_types=1);

use App\Http\Controllers\PlayersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PlayersController::class, 'index'])->name('welcome');
Route::get('/setup', [PlayersController::class, 'setup'])->name('setup');
Route::get('/players', [PlayersController::class, 'players'])->name('players');
