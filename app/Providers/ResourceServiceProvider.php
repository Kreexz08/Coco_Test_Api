<?php

namespace App\Providers;

use App\Contracts\ResourceRepositoryInterface as ContractsResourceRepositoryInterface;
use App\Contracts\ResourceServiceInterface;
use App\Repositories\ResourceRepository;
use Illuminate\Support\ServiceProvider;
use App\Services\ResourceService as ServicesResourceService;


class ResourceServiceProvider extends ServiceProvider
{
    /**
     * Registrar los bindings en el contenedor.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ResourceServiceInterface::class, ServicesResourceService::class);
        $this->app->bind(ContractsResourceRepositoryInterface::class, ResourceRepository::class);
    }

    public function boot()
    {
        //
    }
}
