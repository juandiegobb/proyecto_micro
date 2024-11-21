<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    /**
     * Mostrar todas las notas.
     */
    public function index()
    {
        $notas = Nota::with('estudiante')->get();
        return response()->json(['data' => $notas], 200);
    }

    /**
     * Crear una nueva nota.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'actividad' => 'required|string|max:100',
            'nota' => 'required|numeric|between:0,5',
            'codEstudiante' => 'required|exists:estudiantes,cod',
        ]);

        $nota = Nota::create($validated);
        return response()->json(['data' => $nota], 201);
    }

    /**
     * Mostrar una nota específica.
     */
    public function show($id)
    {
        $nota = Nota::with('estudiante')->find($id);
        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }
        return response()->json(['data' => $nota], 200);
    }

    /**
     * Actualizar una nota.
     */
    public function update(Request $request, $id)
    {
        $nota = Nota::find($id);
        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }

        $validated = $request->validate([
            'actividad' => 'required|string|max:100',
            'nota' => 'required|numeric|between:0,5',
            'codEstudiante' => 'required|exists:estudiantes,cod',
        ]);

        $nota->update($validated);
        return response()->json(['data' => $nota], 200);
    }

    /**
     * Eliminar una nota.
     */
    public function destroy($id)
    {
        $nota = Nota::find($id);
        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }

        $nota->delete();
        return response()->json(['message' => 'Nota eliminada con éxito'], 200);
    }
}
