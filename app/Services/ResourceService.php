<?php

namespace App\Services;

use App\Repositories\ResourceRepository;
use Illuminate\Support\Facades\Log;
use Exception;

class ResourceService
{
    protected $repository;

    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllResources()
    {
        return $this->repository->all();
    }

    public function getResourceById(int $id)
    {
        return $this->repository->find($id);
    }

    public function createResource(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateResource(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteResource(int $id)
    {
        return $this->repository->delete($id);
    }

    public function checkResourceAvailability(int $id, string $datetime, string $duration): bool
    {
        if (empty($datetime) || empty($duration)) {
            throw new Exception('datetime and duration are required.');
        }

        if (!$this->isValidDurationFormat($duration)) {
            throw new Exception('Invalid duration format, expected HH:MM:SS.');
        }

        try {
            $start = \Carbon\Carbon::parse($datetime);
            list($hours, $minutes, $seconds) = array_map('intval', explode(':', $duration));
            $end = $start->copy()->addHours($hours)->addMinutes($minutes)->addSeconds($seconds);

            $resource = $this->repository->find($id);

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
