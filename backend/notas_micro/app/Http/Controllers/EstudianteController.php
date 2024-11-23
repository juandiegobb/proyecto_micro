<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rows = Estudiante::all(); // Obtener todos los registros
        $data = ["data" => $rows];
        return response()->json($data, 200); // Respuesta exitosa con datos
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Capturar los datos enviados
        $dataBody = $request->all();

        // Verificar si el código de estudiante ya existe
        $codExistente = Estudiante::where('cod', $dataBody['cod'])->first();
        if ($codExistente) {
            return response()->json(["msg" => "El código de estudiante ya existe."], 400); // Error 400 si el código ya existe
        }

        // Verificar si el email ya existe
        $emailExistente = Estudiante::where('email', $dataBody['email'])->first();
        if ($emailExistente) {
            return response()->json(["msg" => "El correo electrónico ya está registrado."], 400); // Error 400 si el email ya existe
        }

        // Verificar el formato del email
        if (!filter_var($dataBody['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(["msg" => "El formato del correo electrónico no es válido."], 400); // Error 400 si el formato del email es incorrecto
        }

        // Si las validaciones pasaron, crear el estudiante
        $estudiante = new Estudiante();
        $estudiante->cod = $dataBody['cod'];
        $estudiante->nombres = $dataBody['nombres'];
        $estudiante->email = $dataBody['email'];
        $estudiante->save(); // Guarda el estudiante en la base de datos

        return response()->json(["data" => $estudiante], 201); // Respuesta con código 201 (creado)
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $row = Estudiante::find($id); // Buscar estudiante por ID
        if (empty($row)) {
            return response()->json(["msg" => "Estudiante no encontrado :((..."], 404); // Error 404 si no existe
        }
        $data = ["data" => $row];
        return response()->json($data, 200); // Respuesta exitosa con datos
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Capturar los datos enviados
        $dataBody = $request->all();

        // Buscar el estudiante por ID
        $estudiante = Estudiante::find($id);
        if (!$estudiante) {
            return response()->json(["msg" => "Estudiante no encontrado"], 404); // Error 404 si no se encuentra
        }

        // Verificar si el código de estudiante ya existe (si no es el mismo estudiante)
        $codExistente = Estudiante::where('cod', $dataBody['cod'])->where('id', '!=', $id)->first();
        if ($codExistente) {
            return response()->json(["msg" => "El codigo de estudiante ya existe."], 400); // Error 400 si el código ya existe
        }

        // Verificar si el email ya existe (si no es el mismo estudiante)
        $emailExistente = Estudiante::where('email', $dataBody['email'])->where('id', '!=', $id)->first();
        if ($emailExistente) {
            return response()->json(["msg" => "El correo electronico ya está registrado."], 400); // Error 400 si el email ya existe
        }

        // Verificar el formato del email
        if (!filter_var($dataBody['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json(["msg" => "El formato del correo electrónico no es válido."], 400); // Error 400 si el formato del email es incorrecto
        }

        // Si las validaciones pasaron, actualizar el estudiante
        $estudiante->cod = $dataBody['cod'];
        $estudiante->nombres = $dataBody['nombres'];
        $estudiante->email = $dataBody['email'];
        $estudiante->save(); // Actualiza el estudiante en la base de datos

        return response()->json(["data" => $estudiante], 200); // Respuesta exitosa con los datos actualizados
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $row = Estudiante::find($id); // Buscar estudiante por ID
        if (empty($row)) {
            return response()->json(["msg" => "Estudiante no encontrado, registre nuevamente el codigo"], 404); // Error 404 si no existe
        }
        $row->delete(); // Elimina el estudiante
        return response()->json(["msg" => "Estudiante eliminado"], 200); // Respuesta exitosa
    }

    /**
     * Generar un resumen de estudiantes con sus notas.
     */
    public function resumen()
    {
        // Cargar todos los estudiantes con sus notas
        $estudiantes = Estudiante::with('notas')->get();

        // Filtrar los estudiantes
        $aprobados = $estudiantes->filter(fn($e) => $e->notas->avg('nota') >= 3)->count();
        $reprobados = $estudiantes->filter(fn($e) => $e->notas->isNotEmpty() && $e->notas->avg('nota') < 3)->count();
        $sinNotas = $estudiantes->filter(fn($e) => $e->notas->isEmpty())->count();

        // Responder con el resumen
        return response()->json([
            "aprobados" => $aprobados,
            "reprobados" => $reprobados,
            "sin_notas" => $sinNotas,
        ], 200);
    }


    public function filter(Request $request)
    {
        // Iniciamos la consulta
        $query = Estudiante::query();

        // Filtramos por código
        if ($request->has('cod')) {
            $query->where('cod', $request->input('cod'));
        }

        // Filtramos por nombre
        if ($request->has('nombres')) {
            $query->where('nombres', 'like', '%' . $request->input('nombres') . '%');
        }

        // Filtramos por email
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        // Filtramos por estudiantes aprobados
        if ($request->has('estado') && $request->input('estado') == 'aprobado') {
            $query->whereHas('notas', function ($query) {
                $query->havingRaw('avg(nota) >= 3');
            });
        }

        // Filtramos por estudiantes reprobados
        if ($request->has('estado') && $request->input('estado') == 'reprobado') {
            $query->whereHas('notas', function ($query) {
                $query->havingRaw('avg(nota) < 3');
            });
        }

        // Filtramos por estudiantes sin notas
        if ($request->has('sin_notas') && $request->input('sin_notas') == 'true') {
            $query->whereDoesntHave('notas');
        }

        // Filtramos por rango de notas
        if ($request->has('rango_min') && $request->has('rango_max')) {
            $query->whereHas('notas', function ($query) use ($request) {
                $query->whereBetween('nota', [$request->input('rango_min'), $request->input('rango_max')]);
            });
        }

        // Ejecutamos la consulta
        $rows = $query->get();

        // Verificamos si se encontraron resultados
        if ($rows->isEmpty()) {
            return response()->json(["msg" => "No se encontraron estudiantes con los parámetros solicitados"], 404);
        }

        // Si hay resultados, retornamos los datos
        return response()->json(["data" => $rows], 200);
    }
}
