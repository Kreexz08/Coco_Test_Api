<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceRequest;
use App\Repositories\ResourceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Exception;

class ResourceController extends Controller
{
    use ApiResponse;

    protected $repository;

    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): JsonResponse
    {
        return $this->handleResponse(fn() => $this->repository->all());
    }

    public function show($id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->repository->find($id), 200, 'Resource not found.');
    }

    public function store(ResourceRequest $request): JsonResponse
    {
        return $this->handleResponse(fn() => $this->repository->create($request->validated()), 201);
    }

    public function update(ResourceRequest $request, $id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->repository->update($id, $request->validated()), 200, 'Resource not found.');
    }

    public function destroy($id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->repository->delete($id), 204, 'Resource not found.');
    }

    // En app/Http/Controllers/ResourceController.php
    public function availability(int $id, Request $request): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $datetime = $request->query('datetime');
            $duration = $request->query('duration');

            if (!$datetime || !$duration) {
                throw new Exception('datetime and duration are required.');
            }

            if (!$this->repository->isValidDurationFormat($duration)) {
                throw new Exception('Invalid duration format, expected HH:MM:SS.');
            }

            $available = $this->repository->checkAvailability($id, $datetime, $duration);

            return ['available' => $available];
        });
    }
}
