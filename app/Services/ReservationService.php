<?php

namespace App\Services;

use App\Contracts\ReservationRepositoryInterface;
use App\Contracts\ReservationServiceInterface;
use App\Contracts\ResourceServiceInterface;
use App\Exceptions\ReservationAlreadyCancelledException;
use App\Exceptions\ReservationAlreadyConfirmedException;
use App\Exceptions\ReservationNotFoundException;
use App\Exceptions\ResourceUnavailableException;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationService implements ReservationServiceInterface
{
    protected ReservationRepositoryInterface $repository;
    protected ResourceServiceInterface $resourceService;

    public function __construct(
        ReservationRepositoryInterface $repository,
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
            throw new ResourceUnavailableException();
        }

        $data['reserved_at'] = $reservedAt;
        $data['status'] = 'pending';

        return $this->repository->createReservation($data);
    }

    public function confirmReservation(int $reservationId): array
    {
        $reservation = $this->repository->findReservationById($reservationId);

        if ($reservation->status === 'confirmed') {
            throw new ReservationAlreadyConfirmedException();
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

        if ($reservation->status === 'cancelled') {
            throw new ReservationAlreadyCancelledException();
        }

        $this->repository->cancelReservation($id);

        return [
            'message' => 'Reservation successfully canceled.',
            'success' => true,
        ];
    }
}
