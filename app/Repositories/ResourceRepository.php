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

    public function all(): iterable
    {
        return $this->model->all();
    }

    public function find(int $id): ?Resource
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Resource
    {
        $resource = ResourceFactory::create($data);
        $resource->save();
        return $resource;
    }

    public function update(int $id, array $data): Resource
    {
        $resource = $this->find($id);
        $resource->fill($data);
        $resource->save();
        return $resource;
    }

    public function delete(int $id): bool
    {
        $resource = $this->find($id);
        return $resource->delete();
    }
}
