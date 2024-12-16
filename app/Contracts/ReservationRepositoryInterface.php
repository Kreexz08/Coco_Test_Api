<?php

namespace App\Contracts;

use App\Models\Reservation;

interface ReservationRepositoryInterface
{
    public function create(array $data): Reservation;

    public function confirm(int $reservationId): Reservation;

    public function delete(int $id): bool;

    public function findOrFail(int $id): Reservation;
}
