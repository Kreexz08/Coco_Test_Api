<?php

namespace App\Services;

use App\Contracts\ResourceRepositoryInterface;
use App\Contracts\ResourceServiceInterface;
use App\Exceptions\InvalidResourceDataException;
use App\Exceptions\ResourceAlreadyExistsException;
use App\Exceptions\ResourceUnavailableException;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

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
        try {
            return $this->repository->createResource($data);
        } catch (QueryException $e) {
            if ($e->getCode() === '23505') {
                throw new ResourceAlreadyExistsException();
            }
            throw $e;
        }
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
        $start = Carbon::parse($datetime);
        $end = $this->calculateEndDatetime($start, $duration);
        $this->validateBusinessHours($start, $end);
        return !$this->repository->getResourceById($id)->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($start, $end) {
                $query->where('reserved_at', '<', $end)
                    ->whereRaw('reserved_at + (duration::interval) > ?', [$start]);
            })
            ->exists();
    }

    private function validateDatetimeAndDuration(string $datetime, string $duration)
    {
        if (!$datetime || !$duration || !$this->isValidDurationFormat($duration)) {
            throw new InvalidResourceDataException('Invalid datetime or duration format.');
        }
    }

    private function validateBusinessHours(Carbon $start, Carbon $end)
    {
        if ($start->format('H:i:s') < '09:00:00' || $end->format('H:i:s') > '18:00:00') {
            throw new ResourceUnavailableException('Resource is only available between 9:00 AM and 6:00 PM.');
        }

        if ($start->isWeekend() || $end->isWeekend()) {
            throw new ResourceUnavailableException('Resource is not available on weekends.');
        }
    }

    private function calculateEndDatetime(Carbon $start, string $duration): Carbon
    {
        [$hours, $minutes, $seconds] = array_map('intval', explode(':', $duration));
        return $start->copy()->addHours($hours)->addMinutes($minutes)->addSeconds($seconds);
    }

    private function isValidDurationFormat(string $duration): bool
    {
        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $duration);
    }
}
