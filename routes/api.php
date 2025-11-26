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

// registro de usuario
Route::post('/register', [UsuarioController::class, 'register']);
// Login de usuario
Route::post('/login', [UsuarioController::class, 'login']);
// Logout de usuario
Route::post('/logout', [UsuarioController::class, 'logout'])->middleware('auth:sanctum');

// crud de usuarios

// CRUD de Usuarios (rutas explícitas)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::patch('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
});
