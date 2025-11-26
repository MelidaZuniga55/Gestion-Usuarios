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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = Usuario::all();

        return response()->json([
            'data' => $usuarios,
            'message' => 'Usuarios retrieved successfully',
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
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

        // Hasheamos la contraseña antes de guardar
        $validated['password'] = Hash::make($validated['password']);

        $usuario = Usuario::create($validated);

        return response()->json([
            'data' => $usuario,
            'message' => 'Usuario created successfully',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $usuario = Usuario::findOrFail($id);

            return response()->json([
                'data' => $usuario,
                'message' => 'Usuario retrieved successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Usuario not found',
                'error' => $e->getMessage(),
                'status' => 404
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Buscamos el usuario por su id
            $usuario = Usuario::findOrFail($id);

            // Validación de datos
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
                // Hasheamos la nueva contraseña si se proporciona
                $validated['password'] = Hash::make($validated['password']);
            }

            // Actualizamos el usuario
            $usuario->update($validated);

            return response()->json([
                'data' => $usuario,
                'message' => 'Usuario updated successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating usuario',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Buscamos el usuario por su id
            $usuario = Usuario::findOrFail($id);

            // Eliminamos el usuario
            $usuario->delete();

            return response()->json([
                'message' => 'Usuario deleted successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting usuario',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Get general statistics overview
     */
    public function getStatistics()
    {
        try {
            // Total de usuarios
            $totalUsuarios = Usuario::count();

            // Usuarios activos
            $usuariosActivos = Usuario::where('activo', true)->count();

            // Registros hoy
            $registrosHoy = Usuario::whereDate('created_at', today())->count();

            // Registros esta semana
            $registrosEstaSemana = Usuario::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count();

            // Registros este mes
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
                    'registros_este_mes' => $registrosEsteMes
                ],
                'message' => 'Estadísticas generales obtenidas exitosamente',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Get daily registration statistics (last 30 days)
     */
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
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas diarias',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Get weekly registration statistics (last 12 weeks)
     */
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
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas semanales',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Get monthly registration statistics (last 12 months)
     */
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
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas mensuales',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            // Validar los datos enviados para crear un usuario nuevo
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'email' => 'required|email|unique:usuarios,email',
                'telefono' => 'nullable|string|max:20',
                'fecha_nacimiento' => 'nullable|date',
                'direccion' => 'nullable|string|max:500',
                'password' => 'required|string|min:8'
            ]);

            // Crear el usuario en la tabla Usuario
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'direccion' => $request->direccion,
                'password' => Hash::make($request->password),
            ]);

            // Respuesta en caso de registro exitoso
            return response()->json([
                'message' => 'Usuario Registered successfully',
                'data' => $usuario,
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            // Manejo de errores durante el registro
            return response()->json([
                'message' => 'Error registering usuario',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validar credenciales para iniciar sesión
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            // Obtener credenciales del request
            $credentials = $request->only('email', 'password');

            // Intento de autenticación con Auth::attempt
            if (Auth::attempt($credentials)) {

                // Obtener el usuario autenticado
                $user = $request->user();

                // Definir tiempo de expiración del token
                $expirationTimeToken = Carbon::now()->addMinutes(10);

                // Generar token de Sanctum con permisos
                $token = $user->createToken('auth_token', ['server:update'], $expirationTimeToken)->plainTextToken;

                // Respuesta con token
                return response()->json([
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token,
                    'status' => 200
                ], 200);
            }

            // Si las credenciales no son correctas
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => 401
            ], 401);

        } catch (\Exception $e) {
            // Error en el proceso de login
            return response()->json([
                'message' => 'Error during login',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Obtener el usuario autenticado mediante el token
            $user = $request->user();

            // Eliminar únicamente el token actual
            $user->currentAccessToken()->delete();

            // Respuesta de cierre de sesión
            return response()->json([
                'message' => 'User Log out successful',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {

            // Error en el proceso de logout
            return response()->json([
                'message' => 'Error during logout',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

}