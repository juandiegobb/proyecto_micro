<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cambiar si se requiere control de autorización
    }

    public function rules()
    {
        return [
            'actividad' => 'required|string|max:100',
            'nota' => 'required|numeric|min:0|max:5|regex:/^\d+(\.\d{1,2})?$/', // Nota con máximo 2 decimales
            'codEstudiante' => 'required|exists:estudiantes,cod', // Debe existir en la tabla de estudiantes
        ];
    }

    public function messages()
    {
        return [
            'actividad.required' => 'La actividad es obligatoria.',
            'nota.required' => 'La nota es obligatoria.',
            'nota.numeric' => 'La nota debe ser un número.',
            'nota.min' => 'La nota no puede ser menor a 0.',
            'nota.max' => 'La nota no puede ser mayor a 5.',
            'nota.regex' => 'La nota debe tener como máximo dos decimales.',
            'codEstudiante.required' => 'El código del estudiante es obligatorio.',
            'codEstudiante.exists' => 'El estudiante no existe.',
        ];
    }
}
