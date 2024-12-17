<?php

namespace App\Repositories;

use App\Factories\ResourceFactory;
use App\Contracts\ResourceRepositoryInterface;
use App\Models\Resource;

class ResourceRepository implements ResourceRepositoryInterface
{
    protected Resource $model;

    public function __construct(Resource $resource)
    {
        $this->model = $resource;
    }

    public function getAllResources(): iterable
    {
        return $this->model->all();
    }

    public function getResourceById(int $id): ?Resource
    {
        return $this->model->findOrFail($id);
    }

    public function createResource(array $data): Resource
    {
        $resource = ResourceFactory::create($data);
        $resource->save();
        return $resource;
    }

    public function updateResource(int $id, array $data): Resource
    {
        $resource = $this->getResourceById($id);
        $resource->fill($data);
        $resource->save();
        return $resource;
    }

    public function deleteResource(int $id): bool
    {
        $resource = $this->getResourceById($id);
        return $resource->delete();
    }
}
