<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

/* ============================================
 * AUTENTICACIÓN - RUTAS PÚBLICAS
 * ============================================ */
Route::prefix('auth')->group(function () {
    Route::post('/register', [UsuarioController::class, 'store']);
    Route::post('/login', [UsuarioController::class, 'login']);
});

/* ============================================
 * AUTENTICACIÓN - RUTAS PROTEGIDAS
 * ============================================ */
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UsuarioController::class, 'logout']);
    Route::post('/refresh', [UsuarioController::class, 'refreshToken']);
    Route::get('/check', [UsuarioController::class, 'checkToken']);
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'status' => 200
        ]);
    });
});

/* ============================================
 * ESTADÍSTICAS - RUTAS PÚBLICAS
 * ============================================ */
Route::prefix('estadisticas')->group(function () {
    Route::get('/', [UsuarioController::class, 'getStatistics']);
    Route::get('/diarias', [UsuarioController::class, 'getDailyStatistics']);
    Route::get('/semanales', [UsuarioController::class, 'getWeeklyStatistics']);
    Route::get('/mensuales', [UsuarioController::class, 'getMonthlyStatistics']);
});

/* ============================================
 * USUARIOS - CRUD PROTEGIDO
 * ============================================ */
Route::prefix('usuarios')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store']);
    Route::get('/{id}', [UsuarioController::class, 'show']);
    Route::put('/{id}', [UsuarioController::class, 'update']);
    Route::patch('/{id}', [UsuarioController::class, 'update']);
    Route::delete('/{id}', [UsuarioController::class, 'destroy']);
});
