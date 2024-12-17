<?php

namespace App\Services;

use App\Contracts\ReservationRepositoryInterface as ContractsReservationRepositoryInterface;
use App\Contracts\ReservationServiceInterface;
use App\Contracts\ResourceServiceInterface;
use App\Models\Reservation;
use Carbon\Carbon;
use Exception;

class ReservationService implements ReservationServiceInterface
{
    protected ContractsReservationRepositoryInterface $repository;
    protected ResourceServiceInterface $resourceService;

    public function __construct(
        ContractsReservationRepositoryInterface $repository,
        ResourceServiceInterface $resourceService
    ) {
        $this->repository = $repository;
        $this->resourceService = $resourceService;
    }

    public function createReservation(array $data): Reservation
    {
        $reservedAt = Carbon::parse($data['reserved_at']);

        $isAvailable = $this->resourceService->checkResourceAvailability(
            $data['resource_id'],
            $data['reserved_at'],
            $data['duration']
        );

        if (!$isAvailable) {
            throw new Exception('The resource is not available at the selected time.');
        }

        $data['reserved_at'] = $reservedAt;
        $data['status'] = 'pending';

        return $this->repository->createReservation($data);
    }

    public function confirmReservation(int $reservationId): array
    {
        $reservation = $this->repository->findReservationById($reservationId);

        if (!$reservation) {
            throw new Exception('Reservation not found.');
        }

        if ($reservation->status === 'confirmed') {
            throw new Exception('Reservation is already confirmed.');
        }

        $this->repository->confirmReservation($reservationId);

        return [
            'message' => 'Reservation confirmed successfully.',
            'reservation' => $reservation->refresh(),
        ];
    }

    public function cancelReservation(int $id): array
    {
        $reservation = $this->repository->findReservationById($id);

        if (!$reservation) {
            throw new Exception('Reservation not found.');
        }

        if ($reservation->status === 'cancelled') {
            throw new Exception('Reservation is already cancelled.');
        }

        $this->repository->cancelReservation($id);

        return [
            'message' => 'Reservation successfully canceled.',
            'success' => true,
        ];
    }
}
