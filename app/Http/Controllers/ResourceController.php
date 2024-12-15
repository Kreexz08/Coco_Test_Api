<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckResourceAvailabilityRequest;
use App\Http\Requests\ResourceRequest;
use App\Repositories\ResourceRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    protected $repository;

    public function __construct(ResourceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): JsonResponse
    {
        try {
            $resources = $this->repository->all();
            return response()->json($resources);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch resources.'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $resource = $this->repository->find($id);
            return response()->json($resource);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch resource.'], 500);
        }
    }

    public function store(ResourceRequest $request): JsonResponse
    {
        try {
            $resource = $this->repository->create($request->validated());
            return response()->json($resource, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create resource.'], 500);
        }
    }

    public function update(ResourceRequest $request, $id): JsonResponse
    {
        try {
            $resource = $this->repository->update($id, $request->validated());
            return response()->json($resource);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update resource.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->repository->delete($id);
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete resource.'], 500);
        }
    }

    public function availability(int $id, CheckResourceAvailabilityRequest $request): JsonResponse
    {
        try {
            $availability = $this->repository->checkAvailability(
                $id,
                $request->datetime,
                $request->duration
            );

            return response()->json(['available' => $availability]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to check availability.'], 500);
        }
    }
}
