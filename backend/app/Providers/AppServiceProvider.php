<?php

namespace App\Providers;

use App\Config\TrainingConfig;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app
        ->when(TrainingConfig::class)
        ->needs('$config')
        ->giveConfig('training');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
