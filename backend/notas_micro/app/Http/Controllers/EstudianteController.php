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
        $dataBody = $request->all(); // Captura todos los datos enviados
        $estudiante = new Estudiante();
        $estudiante->cod = $dataBody['cod'];
        $estudiante->nombres = $dataBody['nombres'];
        $estudiante->email = $dataBody['email'];
        $estudiante->save(); // Guarda el estudiante en la base de datos
        $data = ["data" => $estudiante];
        return response()->json($data, 201); // Respuesta con código 201 (creado)
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $row = Estudiante::find($id); // Buscar estudiante por ID
        if (empty($row)) {
            return response()->json(["msg" => "Estudiante no encontrado"], 404); // Error 404 si no existe
        }
        $data = ["data" => $row];
        return response()->json($data, 200); // Respuesta exitosa con datos
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dataBody = $request->all(); // Captura los datos enviados
        $estudiante = Estudiante::find($id); // Buscar estudiante por ID
        if (empty($estudiante)) {
            return response()->json(["msg" => "Estudiante no encontrado"], 404); // Error 404 si no existe
        }
        $estudiante->cod = $dataBody['cod'];
        $estudiante->nombres = $dataBody['nombres'];
        $estudiante->email = $dataBody['email'];
        $estudiante->save(); // Actualiza el estudiante
        $data = ["data" => $estudiante];
        return response()->json($data, 200); // Respuesta exitosa con datos
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $row = Estudiante::find($id); // Buscar estudiante por ID
        if (empty($row)) {
            return response()->json(["msg" => "Estudiante no encontrado"], 404); // Error 404 si no existe
        }
        $row->delete(); // Elimina el estudiante
        return response()->json(["msg" => "Estudiante eliminado"], 200); // Respuesta exitosa
    }
}
