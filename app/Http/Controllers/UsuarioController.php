<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

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
}