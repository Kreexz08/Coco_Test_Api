<?php


namespace App\Repositories;

use App\Factories\ReservationFactory;
use App\Models\Reservation;
use App\Repositories\ResourceRepository;
use FFI\Exception;

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
        // Convertir el valor de 'reserved_at' a formato DateTime
        $reservedAt = \Carbon\Carbon::parse($data['reserved_at']);

        // Verificar si el recurso está disponible utilizando el ResourceRepository
        $isAvailable = $this->resourceRepository->checkAvailability(
            $data['resource_id'],
            $data['reserved_at'],
            $data['duration']
        );

        if (!$isAvailable) {
            throw new Exception('El recurso no está disponible en el horario seleccionado.');
        }

        // Usar la Fábrica para crear la reserva
        $data['reserved_at'] = $reservedAt; // Asegurarse de que el formato sea correcto
        $data['duration'] = $data['duration']; // Esto ya debería ser un string de tipo "HH:MM:SS"
        $data['status'] = 'pending';

        $reservation = ReservationFactory::create($data);
        $reservation->save();  // Guardar la reserva

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
        // Cambiar el estado de la reserva a 'cancelled'
        $reservation->update(['status' => 'cancelled']);
        return true;
    }
}
