<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ]);

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
                'fecha_nacimiento' => 'nullable|date',
                'direccion' => 'nullable|string|max:500',
                'activo' => 'boolean'
            ]);

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
}