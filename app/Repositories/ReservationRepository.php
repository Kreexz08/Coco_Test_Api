<?php

namespace App\Repositories;

use App\Factories\ReservationFactory;
use App\Models\Reservation;
use App\Repositories\ResourceRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ReservationRepository
{
    protected $model;
    protected $resourceRepository;

    public function __construct(Reservation $reservation, ResourceRepository $resourceRepository)
    {
        $this->model = $reservation;
        $this->resourceRepository = $resourceRepository;
    }

    public function create(array $data)
    {
        $reservedAt = \Carbon\Carbon::parse($data['reserved_at']);

        $isAvailable = $this->resourceRepository->checkAvailability(
            $data['resource_id'],
            $data['reserved_at'],
            $data['duration']
        );

        if (!$isAvailable) {
            throw new Exception('El recurso no está disponible en el horario seleccionado.');
        }

        $data['reserved_at'] = $reservedAt;
        $data['status'] = 'pending';

        $reservation = ReservationFactory::create($data);
        $reservation->save();

        return $reservation;
    }

    public function confirm(int $reservationId)
    {
        $reservation = $this->model->findOrFail($reservationId);
        $reservation->status = 'confirmed';
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
