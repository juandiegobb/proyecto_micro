<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas'; // Nombre de la tabla
    protected $primaryKey = 'id'; // Llave primaria
    public $timestamps = false; // La tabla no usa timestamps

    protected $fillable = [
        'actividad',
        'nota',
        'codEstudiante',
    ];

    /**
     * Relación con el modelo Estudiante
     */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'codEstudiante', 'cod');
    }
}
