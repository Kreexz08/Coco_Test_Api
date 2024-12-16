<?php

namespace App\Contracts;

use App\Models\Resource;

interface ResourceRepositoryInterface
{
    public function all();

    public function find(int $id): ?Resource;

    public function create(array $data): Resource;

    public function update(int $id, array $data): Resource;

    public function delete(int $id): bool;
}
