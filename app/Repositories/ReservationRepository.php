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

    public function confirmReservation(int $reservationId): Reservation
    {
        $reservation = $this->findReservationById($reservationId);
        $reservation->update(['status' => 'confirmed']);
        $reservation->refresh();
        return $reservation;
    }

    public function cancelReservation(int $id): bool
    {
        $reservation = $this->findReservationById($id);
        $reservation->update(['status' => 'cancelled']);
        return true;
    }

    public function findReservationById(int $id): Reservation
    {
        return $this->model->findOrFail($id);
    }
}
