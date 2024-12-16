<?php

namespace App\Contracts;

use App\Models\Resource;

interface ResourceServiceInterface
{
    public function getAllResources();

    public function getResourceById(int $id): ?Resource;

    public function createResource(array $data): Resource;

    public function updateResource(int $id, array $data): Resource;

    public function deleteResource(int $id): bool;

    public function checkResourceAvailability(int $id, string $datetime, string $duration): bool;
}
