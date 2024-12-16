<?php

namespace App\Repositories;

use App\Factories\ResourceFactory;
use App\Models\{Resource, Reservation};
use Illuminate\Support\Facades\Log;
use Exception;

class ResourceRepository
{
    protected $model;

    public function __construct(Resource $resource)
    {
        $this->model = $resource;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        $resource = ResourceFactory::create($data);
        $resource->save();
        return $resource;
    }

    public function update($id, array $data)
    {
        $resource = $this->find($id);
        $resource->fill($data);
        $resource->save();
        return $resource;
    }

    public function delete($id)
    {
        $resource = $this->find($id);
        $resource->delete();
        return true;
    }

    public function checkAvailability(int $id, string $datetime, string $duration): bool
    {
        try {
            // Convertir el datetime a una instancia de Carbon
            $start = \Carbon\Carbon::parse($datetime);

            // Dividir la duración y convertir a enteros
            $durationParts = explode(':', $duration);
            $hours = (int) $durationParts[0];
            $minutes = (int) $durationParts[1];
            $seconds = (int) $durationParts[2];

            // Agregar la duración a la fecha de inicio (en segundos)
            $end = $start->copy()->addHours($hours)
                ->addMinutes($minutes)
                ->addSeconds($seconds);

            // Buscar el recurso
            $resource = $this->find($id);

            if (!$resource) {
                throw new \Exception("Resource not found");
            }

            // Verificar si el recurso tiene reservas que se superpongan con la nueva reserva
            return $resource->reservations()
                ->where('status', '!=', 'cancelled') // Solo considerar reservas no canceladas
                ->where(function ($query) use ($start, $end) {
                    // Comprobar si la nueva reserva se superpone con reservas existentes
                    $query->where(function ($subQuery) use ($start, $end) {
                        $subQuery->where('reserved_at', '<', $end)
                            ->whereRaw('reserved_at + (duration::interval) > ?', [$start]);  // Ajuste en la consulta
                    })->orWhere(function ($subQuery) use ($start, $end) {
                        $subQuery->where('reserved_at', '>=', $start)
                            ->whereRaw('reserved_at + (duration::interval) > ?', [$start]);  // Ajuste en la consulta
                    });
                })
                ->doesntExist(); // Si no hay superposición de reservas, el recurso está disponible
        } catch (\Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage());
            return false;
        }
    }
}
