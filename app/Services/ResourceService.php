<?php

namespace App\Services;

use App\Contracts\ResourceRepositoryInterface;
use App\Contracts\ResourceServiceInterface as ContractsResourceServiceInterface;
use App\Models\Resource;
use Carbon\Carbon;
use Exception;

class ResourceService implements ContractsResourceServiceInterface
{
    protected ResourceRepositoryInterface $repository;

    public function __construct(ResourceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllResources(): iterable
    {
        return $this->repository->all();
    }

    public function getResourceById(int $id): ?Resource
    {
        return $this->repository->find($id);
    }

    public function createResource(array $data): Resource
    {
        return $this->repository->create($data);
    }

    public function updateResource(int $id, array $data): Resource
    {
        return $this->repository->update($id, $data);
    }

    public function deleteResource(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function checkResourceAvailability(int $id, string $datetime, string $duration): bool
    {
        if (!$datetime || !$duration || !$this->isValidDurationFormat($duration)) {
            throw new Exception('Invalid datetime or duration format.');
        }
        $start = Carbon::parse($datetime);
        [$hours, $minutes, $seconds] = array_map('intval', explode(':', $duration));
        $end = $start->copy()->addHours($hours)->addMinutes($minutes)->addSeconds($seconds);

        if ($start->format('H:i:s') < '09:00:00' || $end->format('H:i:s') > '18:00:00') {
            throw new Exception('Resource is only available between 9:00 AM and 6:00 PM.');
        }
        if ($start->isWeekend() || $end->isWeekend()) {
            throw new Exception('Resource is not available on weekends.');
        }
        $resource = $this->repository->find($id);
        return !$resource->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($start, $end) {
                $query->where('reserved_at', '<', $end)
                    ->whereRaw('reserved_at + (duration::interval) > ?', [$start]);
            })
            ->exists();
    }


    private function isValidDurationFormat(string $duration): bool
    {
        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $duration);
    }
}
