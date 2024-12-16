<?php

namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Services\ResourceService;
use Carbon\Carbon;
use Exception;

class ReservationService
{
    protected $repository;
    protected $resourceService;

    public function __construct(ReservationRepository $repository, ResourceService $resourceService)
    {
        $this->repository = $repository;
        $this->resourceService = $resourceService;
    }

    public function createReservation(array $data)
    {
        $reservedAt = Carbon::parse($data['reserved_at']);

        $isAvailable = $this->resourceService->checkResourceAvailability(
            $data['resource_id'],
            $data['reserved_at'],
            $data['duration']
        );

        if (!$isAvailable) {
            throw new Exception('El recurso no está disponible en el horario seleccionado.');
        }

        $data['reserved_at'] = $reservedAt;
        $data['status'] = 'pending';

        return $this->repository->create($data);
    }

    public function confirmReservation(int $reservationId)
    {
        return [
            'message' => 'Reservation confirmed successfully.',
            'reservation' => $this->repository->confirm($reservationId),
        ];
    }

    public function cancelReservation(int $id)
    {
        $success = $this->repository->delete($id);
        return [
            'message' => 'Reservation successfully canceled.',
            'success' => $success,
        ];
    }
}
