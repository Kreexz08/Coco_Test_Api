<?php
// app/Factories/ReservationFactory.php

namespace App\Factories;

use App\Models\Reservation;
use App\Models\Resource;

class ReservationFactory
{
    public static function create(array $data): Reservation
    {
        $reservation = new Reservation();
        $reservation->fill($data);
        return $reservation;
    }
}
