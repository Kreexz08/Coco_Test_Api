<?php

namespace App\Providers;

use App\Contracts\ReservationRepositoryInterface as ContractsReservationRepositoryInterface;
use App\Contracts\ReservationServiceInterface as ContractsReservationServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Services\ReservationService;
use App\Repositories\ReservationRepository;

class ReservationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ContractsReservationServiceInterface::class, ReservationService::class);
        $this->app->bind(ContractsReservationRepositoryInterface::class, ReservationRepository::class);
    }

    public function boot()
    {
        //
    }
}
