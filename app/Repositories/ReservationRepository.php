<?php


namespace App\Repositories;

use App\Factories\ReservationFactory;
use App\Models\Reservation;
use App\Models\Resource;
use FFI\Exception;

class ReservationRepository
{
    protected $model;

    public function __construct(Reservation $reservation)
    {
        $this->model = $reservation;
    }

    public function create(array $data)
    {
        $resource = Resource::findOrFail($data['resource_id']);

        $isAvailable = $resource->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($data) {
                $start = $data['reserved_at'];
                $end = strtotime("+{$data['duration']} seconds", strtotime($data['reserved_at']));

                $query->where(function ($subQuery) use ($start, $end) {
                    $subQuery->where('reserved_at', '<=', $start)
                        ->whereRaw('DATE_ADD(reserved_at, INTERVAL TIME_TO_SEC(duration) SECOND) > ?', [$start]);
                })->orWhere(function ($subQuery) use ($start, $end) {
                    $subQuery->where('reserved_at', '<', $end)
                        ->whereRaw('DATE_ADD(reserved_at, INTERVAL TIME_TO_SEC(duration) SECOND) > ?', [$start]);
                });
            })
            ->doesntExist();

        if (!$isAvailable) {
            throw new Exception('Resource is not available for the selected time.');
        }

        $reservation = ReservationFactory::create($data);
        $reservation->save();

        return $reservation;
    }

    public function cancel($id)
    {
        $reservation = $this->model->findOrFail($id);
        $reservation->update(['status' => 'cancelled']);
        return true;
    }
}
