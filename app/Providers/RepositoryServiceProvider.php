<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ResourceRepository;
use App\Repositories\ReservationRepository;
use App\Models\Resource;
use App\Models\Reservation;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar ResourceRepository
        $this->app->bind(ResourceRepository::class, function ($app) {
            return new ResourceRepository(new Resource());
        });

        // Registrar ReservationRepository
        $this->app->bind(ReservationRepository::class, function ($app) {
            return new ReservationRepository(new Reservation(), $app->make(ResourceRepository::class));
        });
    }
}
