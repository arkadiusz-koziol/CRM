<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ForgotPasswordService;
use App\Services\ResetPasswordService;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ForgotPasswordService::class, function ($app) {
            return new ForgotPasswordService($app->make(PasswordBroker::class));
        });

        $this->app->bind(ResetPasswordService::class, function ($app) {
            return new ResetPasswordService($app->make(PasswordBroker::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
