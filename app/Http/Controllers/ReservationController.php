<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Repositories\ReservationRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->repository->cancel($id);
            return response()->json(['message' => 'Reservation cancelled successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Reservation not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to cancel reservation.'], 500);
        }
    }
}
