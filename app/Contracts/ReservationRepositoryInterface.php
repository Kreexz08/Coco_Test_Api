<?php

namespace App\Contracts;

use App\Models\Reservation;

interface ReservationRepositoryInterface
{
    public function createReservation(array $data): Reservation;

    public function confirmReservation(int $reservationId): Reservation;

    public function cancelReservation(int $id): bool;

    public function findReservationById(int $id): Reservation;
}
