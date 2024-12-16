<?php


namespace App\Factories;

use App\Models\Reservation;

class ReservationFactory
{
    public static function create(array $data): Reservation
    {
        $reservation = new Reservation();
        $reservation->reserved_at = \Carbon\Carbon::parse($data['reserved_at']);
        $reservation->duration = $data['duration'];
        $reservation->resource_id = $data['resource_id'];
        $reservation->status = $data['status'] ?? 'pending';

        return $reservation;
    }
}
