<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\ResourceUnavailableException;
use App\Exceptions\InvalidResourceDataException;
use App\Exceptions\ReservationAlreadyCancelledException;
use App\Exceptions\ReservationAlreadyConfirmedException;
use App\Exceptions\ReservationNotFoundException;
use App\Exceptions\ReservationStatusException;
use App\Exceptions\ResourceAlreadyExistsException;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            ResourceUnavailableException::class,
            InvalidResourceDataException::class,
            ReservationAlreadyCancelledException::class,
            ReservationAlreadyConfirmedException::class,
            ReservationNotFoundException::class,
            ReservationStatusException::class,
            ResourceAlreadyExistsException::class,
        ]);

        $exceptions->report(function (ResourceUnavailableException $exception) {
            \Illuminate\Support\Facades\Log::warning("Resource unavailable: " . $exception->getMessage());
        });

        $exceptions->report(function (InvalidResourceDataException $exception) {
            \Illuminate\Support\Facades\Log::warning("Invalid resource data: " . $exception->getMessage());
        });

        $exceptions->report(function (ReservationAlreadyCancelledException $exception) {
            \Illuminate\Support\Facades\Log::warning("Reservation already cancelled: " . $exception->getMessage());
        });

        $exceptions->report(function (ResourceAlreadyExistsException $exception) { // Reportar la nueva excepción
            \Illuminate\Support\Facades\Log::warning("Resource already exists: " . $exception->getMessage());
        });

        $exceptions->render(function (ReservationNotFoundException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 404); 
        });

        $exceptions->render(function (ResourceUnavailableException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        });

        $exceptions->render(function (InvalidResourceDataException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        });

        $exceptions->render(function (ReservationAlreadyCancelledException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        });

        $exceptions->render(function (ReservationAlreadyConfirmedException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        });

        $exceptions->render(function (ReservationStatusException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        });

        $exceptions->render(function (ResourceAlreadyExistsException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        });

        $exceptions->render(function (\Exception $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        });
    })
    ->create();
