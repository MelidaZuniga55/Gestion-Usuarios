<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

 /* ESTADÍSTICAS*/
Route::prefix('usuarios/estadisticas')->group(function () {
    Route::get('/', [UsuarioController::class, 'getStatistics']);
    Route::get('/diarias', [UsuarioController::class, 'getDailyStatistics']);
    Route::get('/semanales', [UsuarioController::class, 'getWeeklyStatistics']);
    Route::get('/mensuales', [UsuarioController::class, 'getMonthlyStatistics']);
});

 /* AUTH*/
Route::post('/register', [UsuarioController::class, 'register']);
Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/logout', [UsuarioController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/refresh', [UsuarioController::class, 'refreshToken'])->middleware('auth:sanctum');

 /* CRUD PROTEGIDO*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::post('/usuarios', [UsuarioController::class, 'store']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::patch('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);
});
