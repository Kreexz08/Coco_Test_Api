<?php

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResourceController;


Route::resource('resources', ResourceController::class)->except(['create', 'edit']);
Route::get('resources/{id}/availability', [ResourceController::class, 'availability']);
Route::resource('reservations', ReservationController::class)->only(['store', 'destroy']);
Route::put('reservations/{reservationId}/confirm', [ReservationController::class, 'confirmReservation']);
