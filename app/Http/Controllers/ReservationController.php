<?php

namespace App\Http\Controllers;

use App\Contracts\ReservationServiceInterface;
use App\Http\Requests\ReservationRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    use ApiResponse;

    protected ReservationServiceInterface $service;

    public function __construct(ReservationServiceInterface $service)
    {
        $this->service = $service;
    }

    public function store(ReservationRequest $request): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->createReservation($request->all()), 201);
    }

    public function confirm($id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->confirmReservation($id));
    }

    public function destroy($id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->cancelReservation($id));
    }
}
