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



// Rutas para el CRUD de notas
Route::prefix('notas')->group(function () {
    Route::get('/', [NotaController::class, 'index']); // Listar todas las notas
    Route::post('/', [NotaController::class, 'store']); // Crear una nueva nota
    Route::get('/filter', [NotaController::class, 'filter']); // Filtrar notas
    Route::get('/resumen', [NotaController::class, 'resumen']); // Resumen de notas
    Route::get('/{id}', [NotaController::class, 'show']); // Mostrar una nota específica
    Route::put('/{id}', [NotaController::class, 'update']); // Actualizar una nota
    Route::delete('/{id}', [NotaController::class, 'destroy']); // Eliminar una nota
});
