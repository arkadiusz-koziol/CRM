<?php

namespace App\Providers;

use App\Interfaces\Repositories\ToolRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\ToolServiceInterface;
use App\Repositories\ToolRepository;
use App\Repositories\UserRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
