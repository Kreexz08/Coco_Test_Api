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
            return $this->errorResponse($e->getMessage(), 400); // Cambia según el contexto
        }
    }

    /**
     * Retorna una respuesta de error estandarizada.
     */
    private function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }
}
