<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ApiResponse
{
    /**
     * Maneja una respuesta API y captura excepciones comunes.
     */
    private function handleResponse(
        callable $callback,
        int $successStatus = 200,
        string $notFoundMessage = 'Resource not found.'
    ): JsonResponse {
        try {
            $result = $callback();
            return response()->json($result, $successStatus);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($notFoundMessage, 404);
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => $message,
                'status' => $status,
                'timestamp' => now()->toDateTimeString(),
            ]
        ], $status);
    }
}
