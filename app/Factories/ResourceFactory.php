<?php

namespace App\Factories;

use App\Models\Resource;

class ResourceFactory
{
    public static function create(array $data): Resource
    {
        $resource = new Resource();
        $resource->fill($data);
        return $resource;
    }
}
