<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UsuarioController extends Controller
{
    // LISTAR USUARIOS
    public function index()
    {
        $usuarios = Usuario::all();

        return response()->json([
            'data' => $usuarios,
            'message' => 'Usuarios retrieved successfully',
            'status' => 200
        ]);
    }

    // CREAR USUARIO
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $usuario = Usuario::create($validated);

        return response()->json([
            'data' => $usuario,
            'message' => 'Usuario created successfully',
            'status' => 201
        ], 201);
    }

    //  MOSTRAR USUARIO
    public function show(string $id)
    {
        try {
            $usuario = Usuario::findOrFail($id);

            return response()->json([
                'data' => $usuario,
                'message' => 'Usuario retrieved successfully',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Usuario not found',
                'status' => 404
            ], 404);
        }
    }

    // ACTUALIZAR USUARIO
    public function update(Request $request, string $id)
    {
        try {
            $usuario = Usuario::findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'apellido' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:usuarios,email,' . $id,
                'telefono' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:8',
                'fecha_nacimiento' => 'nullable|date',
                'direccion' => 'nullable|string|max:500',
                'activo' => 'sometimes|boolean'
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $usuario->update($validated);

            return response()->json([
                'data' => $usuario,
                'message' => 'Usuario updated successfully',
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating usuario',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    // ELIMINAR USUARIO
    public function destroy(string $id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            return response()->json([
                'message' => 'Usuario deleted successfully',
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting usuario',
                'status' => 500
            ], 500);
        }
    }

    // ESTADÍSTICAS GENERALES
    public function getStatistics()
    {
        try {
            $totalUsuarios = Usuario::count();
            $usuariosActivos = Usuario::where('activo', true)->count();
            $registrosHoy = Usuario::whereDate('created_at', today())->count();

            $registrosEstaSemana = Usuario::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();

            $registrosEsteMes = Usuario::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return response()->json([
                'data' => [
                    'total_usuarios' => $totalUsuarios,
                    'usuarios_activos' => $usuariosActivos,
                    'usuarios_inactivos' => $totalUsuarios - $usuariosActivos,
                    'registros_hoy' => $registrosHoy,
                    'registros_esta_semana' => $registrosEstaSemana,
                    'registros_este_mes' => $registrosEsteMes,
                ],
                'message' => 'Estadísticas generales obtenidas exitosamente',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas',
                'status' => 500
            ], 500);
        }
    }

    // ESTADÍSTICAS DIARIAS
    public function getDailyStatistics()
    {
        try {
            $estadisticas = Usuario::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(*) as total_registros')
            )
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('fecha')
                ->orderBy('fecha', 'desc')
                ->get();

            return response()->json([
                'data' => $estadisticas,
                'periodo' => 'Últimos 30 días',
                'message' => 'Estadísticas diarias obtenidas exitosamente',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas diarias',
                'status' => 500
            ], 500);
        }
    }

    // ESTADÍSTICAS SEMANALES
    public function getWeeklyStatistics()
    {
        try {
            $estadisticas = Usuario::select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('WEEK(created_at) as semana'),
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('MIN(DATE(created_at)) as fecha_inicio'),
                DB::raw('MAX(DATE(created_at)) as fecha_fin')
            )
                ->where('created_at', '>=', now()->subWeeks(12))
                ->groupBy('año', 'semana')
                ->orderBy('año', 'desc')
                ->orderBy('semana', 'desc')
                ->get();

            return response()->json([
                'data' => $estadisticas,
                'periodo' => 'Últimas 12 semanas',
                'message' => 'Estadísticas semanales obtenidas exitosamente',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas semanales',
                'status' => 500
            ], 500);
        }
    }

    // ESTADÍSTICAS MENSUALES
    public function getMonthlyStatistics()
    {
        try {
            $estadisticas = Usuario::select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('MONTHNAME(created_at) as nombre_mes')
            )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('año', 'mes', 'nombre_mes')
                ->orderBy('año', 'desc')
                ->orderBy('mes', 'desc')
                ->get();

            return response()->json([
                'data' => $estadisticas,
                'periodo' => 'Últimos 12 meses',
                'message' => 'Estadísticas mensuales obtenidas exitosamente',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas mensuales',
                'status' => 500
            ], 500);
        }
    }

    //REGISTRO DE USUARIO
    // public function register(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'nombre' => 'required|string|max:255',
    //             'apellido' => 'required|string|max:255',
    //             'email' => 'required|email|unique:usuarios,email',
    //             'telefono' => 'nullable|string|max:20',
    //             'fecha_nacimiento' => 'nullable|date',
    //             'direccion' => 'nullable|string|max:500',
    //             'password' => 'required|string|min:8'
    //         ]);

    //         $usuario = Usuario::create([
    //             'nombre' => $request->nombre,
    //             'apellido' => $request->apellido,
    //             'email' => $request->email,
    //             'telefono' => $request->telefono,
    //             'fecha_nacimiento' => $request->fecha_nacimiento,
    //             'direccion' => $request->direccion,
    //             'password' => Hash::make($request->password),
    //         ]);

    //         return response()->json([
    //             'message' => 'Usuario registered successfully',
    //             'data' => $usuario,
    //             'status' => 201
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Error registering usuario',
    //             'status' => 500
    //         ], 500);
    //     }
    // }


    /* LOGIN */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {

                $user = $request->user();
                $expirationTimeToken = Carbon::now()->addMinutes(7);

                $token = $user->createToken(
                    'auth_token',
                    ['server:update'],
                    $expirationTimeToken
                )->plainTextToken;

                return response()->json([
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token,
                    'expires_at' => $expirationTimeToken->toIso8601String(),
                    'expires_in' => 300, // 5 minutos en segundos
                    'status' => 200
                ]);
            }

            return response()->json([
                'message' => 'Invalid credentials',
                'status' => 401
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during login',
                'status' => 500
            ], 500);
        }
    }

    /* ------------------------------------------
     * REFRESH TOKEN
     * ------------------------------------------ */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();

            // Eliminar token actual
            $user->currentAccessToken()->delete();

            // Crear nuevo token con expiración de 5 minutos
            $expirationTimeToken = Carbon::now()->addMinutes(5);

            $newToken = $user->createToken(
                'auth_token',
                ['server:update'],
                $expirationTimeToken
            )->plainTextToken;

            return response()->json([
                'message' => 'Token refreshed successfully',
                'token' => $newToken,
                'expires_at' => $expirationTimeToken->toIso8601String(),
                'expires_in' => 300, // 5 minutos en segundos
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error refreshing token',
                'status' => 500
            ], 500);
        }
    }

    /* ------------------------------------------
     * CHECK TOKEN
     * ------------------------------------------ */
    public function checkToken(Request $request)
    {
        try {
            $user = $request->user();
            $token = $user->currentAccessToken();

            $expiresAt = $token->expires_at;
            $now = Carbon::now();

            // Calcular segundos restantes hasta la expiración
            $expiresIn = $expiresAt ? $expiresAt->diffInSeconds($now, false) : null;

            return response()->json([
                'message' => 'Token is valid',
                'valid' => true,
                'expires_at' => $expiresAt ? $expiresAt->toIso8601String() : null,
                'expires_in' => $expiresIn ? abs($expiresIn) : null,
                'user' => $user,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid token',
                'valid' => false,
                'status' => 401
            ], 401);
        }
    }

    /* ------------------------------------------
     * LOGOUT
     * ------------------------------------------ */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'User logged out successfully',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error during logout',
                'status' => 500
            ], 500);
        }
    }
}
