<?php

namespace App\Contracts;

interface ReservationServiceInterface
{
    public function createReservation(array $data);

    public function confirmReservation(int $reservationId);

    public function cancelReservation(int $id);
}
