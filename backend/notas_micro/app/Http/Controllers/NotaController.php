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
            return response()->json(['message' => 'Nota no encontrada :('], 404);
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
    public function destroy(Request $request, $id)
    {
        $nota = Nota::find($id);

        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }

        // Verificar si se ha enviado la confirmación
        $confirmar = $request->query('confirmar', false); // Por defecto es false si no se envía

        if ($confirmar != 'true') {
            return response()->json([
                'message' => 'Se requiere confirmación para eliminar la nota. Añade "?confirmar=true" al endpoint.'
            ], 400);
        }

        $nota->delete();

        return response()->json(['message' => 'Nota eliminada con éxito'], 200);
    }

    /**
     * Mostrar las notas de un estudiante específico con su promedio.
     */
    public function notasPorEstudiante($codEstudiante)
    {
        $estudiante = Estudiante::with('notas')->where('cod', $codEstudiante)->first();

        if (!$estudiante) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        $promedio = $estudiante->notas->avg('nota') ?? 0;
        $estado = $promedio >= 3 ? 'Aprobado' : 'Reprobado';

        return response()->json([
            'estudiante' => [
                'cod' => $estudiante->cod,
                'nombres' => $estudiante->nombres,
                'email' => $estudiante->email,
                'estado' => $estado,
                'promedio' => number_format($promedio, 2),
            ],
            'notas' => $estudiante->notas->map(function ($nota) {
                return [
                    'id' => $nota->id,
                    'actividad' => $nota->actividad,
                    'nota' => $nota->nota,
                ];
            }),
        ], 200);
    }

    /**
     * Filtrar notas por actividad o rango de notas.
     */
    public function filter(Request $request)
{
    $query = Nota::query();

    if ($request->has('actividad')) {
        $query->where('actividad', 'LIKE', '%' . $request->actividad . '%');
    }

    if ($request->has('rango_min') && $request->has('rango_max')) {
        $query->whereBetween('nota', [$request->rango_min, $request->rango_max]);
    }

    $notas = $query->get();

    if ($notas->isEmpty()) {
        return response()->json(['message' => 'No se encontraron notas con los criterios dados.'], 404);
    }

    return response()->json(['data' => $notas], 200);
}


    /**
     * Resumen de las notas.
     */
    public function resumen()
    {
        $notas = Nota::all();

        $bajo3 = $notas->where('nota', '<', 3)->count();
        $igualOArriba3 = $notas->where('nota', '>=', 3)->count();

        return response()->json([
            'total_notas' => $notas->count(),
            'bajo3' => $bajo3,
            'igual_o_arriba3' => $igualOArriba3,
        ], 200);
    }
}
