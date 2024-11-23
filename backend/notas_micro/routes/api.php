<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\NotaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rutas para Estudiantes

Route::prefix('estudiantes')->group(function () {
    Route::get('/', [EstudianteController::class, 'index']); // Listar todos los estudiantes
    Route::post('/', [EstudianteController::class, 'store']); // Crear un nuevo estudiante
    Route::get('/filter', [EstudianteController::class, 'filter']); // Filtrar estudiantes
    Route::get('/resumen', [EstudianteController::class, 'resumen']); // Resumen de estudiantes
    Route::get('/{id}', [EstudianteController::class, 'show']); // Mostrar un estudiante específico
    Route::put('/{id}', [EstudianteController::class, 'update']); // Actualizar un estudiante
    Route::delete('/{id}', [EstudianteController::class, 'destroy']); // Eliminar un estudiante
});

Route::get('/estudiantes/resumen', [EstudianteController::class, 'resumen']);
//Route::get('/notas/resumen', [NotaController::class, 'resumen']);

// Rutas para el CRUD de notas
Route::get('/notas', [NotaController::class, 'index']); // Listar todas las notas
Route::post('/notas', [NotaController::class, 'store']); // Crear una nueva nota
Route::get('/notas/{id}', [NotaController::class, 'show']); // Ver una nota específica
Route::put('/notas/{id}', [NotaController::class, 'update']); // Actualizar una nota
Route::delete('/notas/{id}', [NotaController::class, 'destroy']); // Eliminar una nota