<?php

namespace App\Repositories;

use App\Factories\ReservationFactory;
use App\Contracts\ReservationRepositoryInterface;
use App\Models\Reservation;

class ReservationRepository implements ReservationRepositoryInterface
{
    protected Reservation $model;

    public function __construct(Reservation $reservation)
    {
        $this->model = $reservation;
    }

    public function createReservation(array $data): Reservation
    {
        $reservation = ReservationFactory::create($data);
        $reservation->save();
        return $reservation;
    }

    public function confirmReservation(int $id): Reservation
    {
        return $this->updateReservationStatus($id, 'confirmed');
    }

    public function cancelReservation(int $id): bool
    {
        $this->updateReservationStatus($id, 'cancelled');
        return true;
    }

    public function findReservationById(int $id): Reservation
    {
        return $this->model->findOrFail($id);
    }

    protected function updateReservationStatus(int $id, string $status): Reservation
    {
        $reservation = $this->findReservationById($id);
        $reservation->update(['status' => $status]);
        return $reservation;
    }
}
