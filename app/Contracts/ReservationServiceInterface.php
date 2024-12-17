<?php

namespace App\Contracts;

use App\Models\Reservation;

interface ReservationServiceInterface
{
    public function createReservation(array $data): Reservation;

    public function confirmReservation(int $reservationId): array;

    public function cancelReservation(int $id): array;
}
