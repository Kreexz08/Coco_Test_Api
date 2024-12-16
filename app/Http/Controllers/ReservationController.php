<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Repositories\ReservationRepository;
use Exception;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    protected $repository;

    public function __construct(ReservationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function store(ReservationRequest $request): JsonResponse
    {
        try {
            $reservation = $this->repository->create($request->validated());
            return response()->json($reservation, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function confirmReservation($reservationId): JsonResponse
    {
        try {
            $reservation = $this->repository->confirm($reservationId);
            return response()->json([
                'message' => 'Reservation confirmed successfully.',
                'reservation' => $reservation,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to confirm reservation: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->repository->cancel($id);
            return response()->json(['message' => 'Reserva cancelada exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo cancelar la reserva.'], 500); // Error si no se pudo cancelar
        }
    }
}
