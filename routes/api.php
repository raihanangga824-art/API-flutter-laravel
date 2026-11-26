<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CharacterCardController;
use App\Http\Controllers\Api\FactionController;
use Illuminate\Support\Facades\Route;

// ---------- PUBLIC ----------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ---------- PROTECTED (butuh Bearer Token) ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Tabel 1 (induk): index, store, show, update, destroy
    Route::apiResource('factions', FactionController::class);

    // Tabel 2 (anak)
    Route::apiResource('character-cards', CharacterCardController::class);
});
