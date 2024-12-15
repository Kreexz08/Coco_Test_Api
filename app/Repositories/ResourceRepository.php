<?php

namespace App\Repositories;

use App\Factories\ResourceFactory;
use App\Models\{Resource, Reservation};
use Exception;

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
        $resource = ResourceFactory::create($data);
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

    public function checkAvailability(int $id, string $datetime, string $duration): bool
    {
        $resource = $this->find($id);

        return $resource->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($datetime, $duration) {
                $start = $datetime;
                $end = strtotime("+$duration seconds", strtotime($datetime));

                $query->where(function ($subQuery) use ($start, $end) {
                    $subQuery->where('reserved_at', '<=', $start)
                        ->whereRaw('DATE_ADD(reserved_at, INTERVAL TIME_TO_SEC(duration) SECOND) > ?', [$start]);
                })->orWhere(function ($subQuery) use ($start, $end) {
                    $subQuery->where('reserved_at', '<', $end)
                        ->whereRaw('DATE_ADD(reserved_at, INTERVAL TIME_TO_SEC(duration) SECOND) > ?', [$start]);
                });
            })
            ->doesntExist();
    }
}
