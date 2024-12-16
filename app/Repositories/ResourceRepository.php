<?php

namespace App\Repositories;

use App\Models\Resource;

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
        $resource = new Resource($data);
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
}
