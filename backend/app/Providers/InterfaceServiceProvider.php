<?php

namespace App\Providers;

use App\Interfaces\Repositories\CityRepositoryInterface;
use App\Interfaces\Repositories\EstateRepositoryInterface;
use App\Interfaces\Repositories\MaterialRepositoryInterface;
use App\Interfaces\Repositories\PinRepositoryInterface;
use App\Interfaces\Repositories\PlanRepositoryInterface;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\CityServiceInterface;
use App\Interfaces\Services\EstateServiceInterface;
use App\Interfaces\Services\MaterialServiceInterface;
use App\Interfaces\Services\PinServiceInterface;
use App\Interfaces\Services\PlanServiceInterface;
use App\Interfaces\Services\ToolServiceInterface;
use App\Repositories\CityRepository;
use App\Repositories\EstateRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\PinRepository;
use App\Repositories\PlanRepository;
use App\Repositories\ToolRepository;
use App\Repositories\UserRepository;
use App\Services\CityService;
use App\Services\EstateService;
use App\Services\MaterialService;
use App\Services\PinService;
use App\Services\PlanService;
use App\Services\ToolService;
use Illuminate\Support\ServiceProvider;

class InterfaceServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        ToolServiceInterface::class => ToolService::class,
        ToolRepositoryInterface::class => ToolRepository::class,
        MaterialServiceInterface::class => MaterialService::class,
        MaterialRepositoryInterface::class => MaterialRepository::class,
        CityServiceInterface::class => CityService::class,
        CityRepositoryInterface::class => CityRepository::class,
        EstateRepositoryInterface::class => EstateRepository::class,
        EstateServiceInterface::class => EstateService::class,
        PlanRepositoryInterface::class => PlanRepository::class,
        PlanServiceInterface::class => PlanService::class,
        PinRepositoryInterface::class => PinRepository::class,
        PinServiceInterface::class => PinService::class,
    ];

}
