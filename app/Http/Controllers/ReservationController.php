<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class ReservationController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(ReservationService $service)
    {
        $this->service = $service;
    }

    public function store(ReservationRequest $request): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->createReservation($request->validated()), 201);
    }

    public function confirm($id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->confirmReservation($id), 200, 'Reservation not found.');
    }

    public function cancel($id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->cancelReservation($id), 200, 'Reservation not found.');
    }
}
