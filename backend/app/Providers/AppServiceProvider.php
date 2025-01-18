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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ToolServiceInterface::class, ToolService::class);
        $this->app->bind(ToolRepositoryInterface::class, ToolRepository::class);
        $this->app->bind(MaterialServiceInterface::class, MaterialService::class);
        $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        $this->app->bind(CityServiceInterface::class, CityService::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(EstateRepositoryInterface::class, EstateRepository::class);
        $this->app->bind(EstateServiceInterface::class, EstateService::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(PlanServiceInterface::class, PlanService::class);
        $this->app->bind(PinRepositoryInterface::class, PinRepository::class);
        $this->app->bind(PinServiceInterface::class, PinService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
