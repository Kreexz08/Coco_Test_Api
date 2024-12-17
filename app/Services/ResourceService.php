<?php

namespace App\Services;

use App\Contracts\ResourceRepositoryInterface;
use App\Contracts\ResourceServiceInterface;
use App\Models\Resource;
use Carbon\Carbon;
use Exception;

class ResourceService implements ResourceServiceInterface
{
    protected ResourceRepositoryInterface $repository;

    public function __construct(ResourceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllResources(): iterable
    {
        return $this->repository->getAllResources();
    }

    public function getResourceById(int $id): ?Resource
    {
        return $this->repository->getResourceById($id);
    }

    public function createResource(array $data): Resource
    {
        return $this->repository->createResource($data);
    }

    public function updateResource(int $id, array $data): Resource
    {
        return $this->repository->updateResource($id, $data);
    }

    public function deleteResource(int $id): bool
    {
        return $this->repository->deleteResource($id);
    }

    public function checkResourceAvailability(int $id, string $datetime, string $duration): bool
    {
        $this->validateDatetimeAndDuration($datetime, $duration);
        $start = $this->parseDatetime($datetime);
        $end = $this->calculateEndDatetime($start, $duration);
        $this->validateBusinessHours($start, $end);
        return $this->checkReservationAvailability($id, $start, $end);
    }

    private function validateDatetimeAndDuration(string $datetime, string $duration)
    {
        if (!$datetime || !$duration || !$this->isValidDurationFormat($duration)) {
            throw new Exception('Invalid datetime or duration format.');
        }
    }

    private function validateBusinessHours(Carbon $start, Carbon $end)
    {
        if ($start->format('H:i:s') < '09:00:00' || $end->format('H:i:s') > '18:00:00') {
            throw new Exception('Resource is only available between 9:00 AM and 6:00 PM.');
        }
        if ($start->isWeekend() || $end->isWeekend()) {
            throw new Exception('Resource is not available on weekends.');
        }
    }

    private function parseDatetime(string $datetime): Carbon
    {
        return Carbon::parse($datetime);
    }

    private function calculateEndDatetime(Carbon $start, string $duration): Carbon
    {
        [$hours, $minutes, $seconds] = array_map('intval', explode(':', $duration));
        return $start->copy()->addHours($hours)->addMinutes($minutes)->addSeconds($seconds);
    }

    private function checkReservationAvailability(int $id, Carbon $start, Carbon $end): bool
    {
        $resource = $this->repository->getResourceById($id);
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
