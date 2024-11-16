<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $primaryKey = 'cod'; // Define la clave primaria

    protected $fillable = [
        'cod',
        'nombres',
        'email',
    ];

    public function notas()
    {
        return $this->hasMany(Nota::class, 'codEstudiante');
    }
}
