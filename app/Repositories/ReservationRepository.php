<?php

namespace App\Repositories;

use App\Factories\ReservationFactory;
use App\Contracts\ReservationRepositoryInterface;
use App\Exceptions\ReservationNotFoundException;
use App\Models\Reservation;

class ReservationRepository implements ReservationRepositoryInterface
{
    protected Reservation $model;

    public function __construct(Reservation $reservation)
    {
        $this->model = $reservation;
    }

    public function findReservationById(int $id): Reservation
    {
        try {
            return $this->model->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ReservationNotFoundException("Reservation not found.");
        }
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

    protected function updateReservationStatus(int $id, string $status): Reservation
    {
        $reservation = $this->findReservationById($id);
        $reservation->update(['status' => $status]);
        return $reservation;
    }
}
