<?php

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResourceController;

// Rutas para recursos (Resources)
Route::get('/resources', [ResourceController::class, 'index']);     // Listar recursos
Route::post('/resources', [ResourceController::class, 'store']);    // Crear recurso
Route::get('/resources/{id}', [ResourceController::class, 'show']); // Ver un recurso
Route::put('/resources/{id}', [ResourceController::class, 'update']);
Route::delete('/resources/{id}', [ResourceController::class, 'destroy']);
Route::get('resources/{id}/availability', [ResourceController::class, 'availability']);

Route::post('/reservations', [ReservationController::class, 'store']);
Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
