<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    // Función para listar los estudiantes
    public function index()
    {
        $estudiantes = Estudiante::all();

        return response()->json([
            'estudiantes' => $estudiantes,
        ], 200);
    }

    // Función para registrar un estudiante
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'cod' => 'required|integer|unique:estudiantes',
            'nombres' => 'required|string|max:250',
            'email' => 'required|email|unique:estudiantes',
        ]);

        // Crear el estudiante
        $estudiante = Estudiante::create($request->all());

        return response()->json([
            'message' => 'Estudiante registrado con éxito.',
            'estudiante' => $estudiante,
        ], 201);
    }
}
