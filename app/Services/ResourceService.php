<?php

namespace App\Services;

use App\Repositories\ResourceRepository;
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

        if (!$this->repository->isValidDurationFormat($duration)) {
            throw new Exception('Invalid duration format, expected HH:MM:SS.');
        }

        return $this->repository->checkAvailability($id, $datetime, $duration);
    }
}
