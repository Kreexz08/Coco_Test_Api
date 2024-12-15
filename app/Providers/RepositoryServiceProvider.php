<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ResourceRepository;
use App\Factories\ResourceFactory;
use App\Repositories\ReservationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar ResourceRepository
        $this->app->bind(ResourceRepository::class, function ($app) {
            return new ResourceRepository(new \App\Models\Resource());
        });

        // Registrar ReservationRepository
        $this->app->bind(ReservationRepository::class, function ($app) {
            return new ReservationRepository(new \App\Models\Reservation());
        });
    }
}
