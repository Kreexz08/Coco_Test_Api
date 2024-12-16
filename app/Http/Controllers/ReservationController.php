<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Repositories\ReservationRepository;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;

class ReservationController extends Controller
{
    use ApiResponse;

    protected $repository;

    public function __construct(ReservationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store(ReservationRequest $request): JsonResponse
    {
        return $this->handleResponse(fn() => $this->repository->create($request->validated()), 201);
    }

    public function confirmReservation($reservationId): JsonResponse
    {
        return $this->handleResponse(fn() => [
            'message' => 'Reservation confirmed successfully.',
            'reservation' => $this->repository->confirm($reservationId),
        ], 200, 'Reservation not found.');
    }

    public function destroy($id): JsonResponse
    {
        return $this->handleResponse(fn() => [
            'message' => 'Reservation successfully canceled.',
            'success' => $this->repository->cancel($id),
        ], 200, 'Reservation not found.');
    }
}
