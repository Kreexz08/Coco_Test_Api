<?php

namespace App\Repositories;

use App\Factories\ReservationFactory;
use App\Models\Reservation;

class ReservationRepository
{
    protected $model;

    public function __construct(Reservation $reservation)
    {
        $this->model = $reservation;
    }

    public function create(array $data): Reservation
    {
        $reservation = ReservationFactory::create($data);
        $reservation->save();

        return $reservation;
    }

    public function confirm(int $reservationId): Reservation
    {
        $reservation = $this->findOrFail($reservationId);
        $reservation->update(['status' => 'confirmed']);
        return $reservation;
    }

    public function delete(int $id): bool
    {
        $reservation = $this->findOrFail($id);
        $reservation->update(['status' => 'cancelled']);
        return true;
    }

    public function findOrFail(int $id): Reservation
    {
        return $this->model->findOrFail($id);
    }
}
