<?php

namespace App\Contracts;

use App\Models\Resource;

interface ResourceRepositoryInterface
{
    public function getAllResources(): iterable;

    public function getResourceById(int $id): ?Resource;

    public function createResource(array $data): Resource;

    public function updateResource(int $id, array $data): Resource;

    public function deleteResource(int $id): bool;
}
