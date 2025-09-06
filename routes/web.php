<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurnosLoginController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta de login por defecto que redirige al login de turnos
Route::get('/login', function () {
    return redirect()->route('turnos.login');
})->name('login');

// Rutas para el sistema de turnos (fuera del admin)
Route::get('/gas', [TurnosLoginController::class, 'mostrarLogin'])->name('turnos.login');
Route::post('/gas/operadores', [TurnosLoginController::class, 'obtenerOperadores'])->name('turnos.operadores');
Route::post('/gas/login', [TurnosLoginController::class, 'procesarLogin'])->name('turnos.procesar');

Route::middleware('auth')->group(function () {
    Route::get('/gas/panel', [TurnosLoginController::class, 'panel'])->name('turnos.panel');
    Route::post('/gas/panel/guardar', [TurnosLoginController::class, 'guardarLecturasGrupo'])->name('turnos.guardar_lecturas_grupo');
});

Route::post('/gas/abrir-turno', [TurnosLoginController::class, 'abrirTurno'])->name('turnos.abrir-turno')->middleware('auth');
Route::post('/gas/cerrar-turno', [TurnosLoginController::class, 'cerrarTurno'])->name('turnos.cerrar-turno')->middleware('auth');
Route::post('/gas/bomba/{bomba}/guardar-lectura', [TurnosLoginController::class, 'guardarLecturaBomba'])->name('turnos.bomba.guardar-lectura')->middleware('auth');
Route::post('/gas/bomba-grupo/{nombreBomba}/guardar', [TurnosLoginController::class, 'guardarLecturasGrupo'])->name('turnos.bomba.guardar-grupo')->middleware('auth');
Route::post('/gas/guardar-efectivo', [TurnosLoginController::class, 'guardarEfectivo'])->name('turnos.guardar-efectivo')->middleware('auth');
Route::post('/gas/logout', [TurnosLoginController::class, 'logout'])->name('turnos.logout');
