<?php

namespace App\Providers;

use App\Interfaces\Repositories\MaterialRepositoryInterface;
use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\MaterialServiceInterface;
use App\Interfaces\Services\ToolServiceInterface;
use App\Repositories\MaterialRepository;
use App\Repositories\ToolRepository;
use App\Repositories\UserRepository;
use App\Services\MaterialService;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
