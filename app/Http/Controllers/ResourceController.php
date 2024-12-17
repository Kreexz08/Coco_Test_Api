<?php

namespace App\Http\Controllers;

use App\Contracts\ResourceServiceInterface as ContractsResourceServiceInterface;
use App\Http\Requests\ResourceRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    use ApiResponse;

    protected ContractsResourceServiceInterface $service;

    public function __construct(ContractsResourceServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->getAllResources());
    }

    public function show(int $id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->getResourceById($id), 200, 'Resource not found.');
    }

    public function store(ResourceRequest $request): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->createResource($request->validated()), 201);
    }

    public function update(ResourceRequest $request, int $id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->updateResource($id, $request->validated()), 200, 'Resource not found.');
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->handleResponse(fn() => $this->service->deleteResource($id), 204, 'Resource not found.');
    }

    public function availability(int $id, Request $request): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $datetime = $request->query('datetime');
            $duration = $request->query('duration');
            return [
                'available' => $this->service->checkResourceAvailability($id, $datetime, $duration),
            ];
        });
    }
}
