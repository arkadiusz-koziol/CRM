<?php

namespace App\Providers;

use App\Interfaces\Repositories\CityRepositoryInterface;
use App\Interfaces\Repositories\EstateRepositoryInterface;
use App\Interfaces\Repositories\MaterialRepositoryInterface;
use App\Interfaces\Repositories\PinRepositoryInterface;
use App\Interfaces\Repositories\PlanRepositoryInterface;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Repositories\CityRepository;
use App\Repositories\EstateRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\PinRepository;
use App\Repositories\PlanRepository;
use App\Repositories\ToolRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class InterfaceServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        ToolRepositoryInterface::class => ToolRepository::class,
        MaterialRepositoryInterface::class => MaterialRepository::class,
        CityRepositoryInterface::class => CityRepository::class,
        EstateRepositoryInterface::class => EstateRepository::class,
        PlanRepositoryInterface::class => PlanRepository::class,
        PinRepositoryInterface::class => PinRepository::class,
    ];

}
