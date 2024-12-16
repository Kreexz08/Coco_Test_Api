<?php

namespace App\Repositories;

use App\Factories\ResourceFactory;
use App\Models\Resource;
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
            $start = \Carbon\Carbon::parse($datetime);
            list($hours, $minutes, $seconds) = array_map('intval', explode(':', $duration));
            $end = $start->copy()->addHours($hours)->addMinutes($minutes)->addSeconds($seconds);

            $resource = $this->find($id);

            if ($start->format('H:i:s') < '09:00:00' || $end->format('H:i:s') > '18:00:00') {
                throw new Exception('El recurso solo está disponible de 9:00 AM a 6:00 PM');
            }

            if ($start->isWeekend() || $end->isWeekend()) {
                throw new Exception('El recurso no está disponible los fines de semana');
            }

            return $resource->reservations()
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($start, $end) {
                    $query->where(function ($subQuery) use ($start, $end) {
                        $subQuery->where('reserved_at', '<', $end)
                            ->whereRaw('reserved_at + (duration::interval) > ?', [$start]);
                    });
                })
                ->doesntExist();
        } catch (Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage());
            throw $e;
        }
    }

    public function isValidDurationFormat(string $duration): bool
    {
        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $duration);
    }
}
