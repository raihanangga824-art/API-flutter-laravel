<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sinilah Anda dapat mendaftarkan rute API untuk aplikasi Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider di dalam sebuah grup yang
| ditetapkan ke dalam kelompok middleware "api". Selamat membangun API Anda!
|
*/

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::apiResource('articles', \App\Http\Controllers\Api\ArticleController::class);
});
