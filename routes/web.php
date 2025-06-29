<?php

declare(strict_types=1);

use App\Http\Controllers\PlayersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PlayersController::class, 'index'])->name('welcome');
Route::get('/players/{id}', [PlayersController::class, 'show'])->name('players.show');
