<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Estadísticas de usuarios (debe ir antes del apiResource)
Route::prefix('usuarios/estadisticas')->group(function () {
    Route::get('/', [UsuarioController::class, 'getStatistics']);
    Route::get('/diarias', [UsuarioController::class, 'getDailyStatistics']);
    Route::get('/semanales', [UsuarioController::class, 'getWeeklyStatistics']);
    Route::get('/mensuales', [UsuarioController::class, 'getMonthlyStatistics']);
});

// CRUD de Usuarios
Route::apiResource('usuarios', UsuarioController::class);