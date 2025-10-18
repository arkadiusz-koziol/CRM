<?php

declare(strict_types=1);

namespace App\Providers;

use App\Interfaces\Repositories\ActivityRepositoryInterface;
use App\Interfaces\Repositories\TrainingFileRepositoryInterface;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Interfaces\Services\FileStorageServiceInterface;
use App\Interfaces\Services\UuidServiceInterface;
use App\Repositories\ActivityRepository;
use App\Repositories\TrainingFileRepository;
use App\Repositories\TrainingRepository;
use App\Services\FileStorageService;
use App\Services\ForgotPasswordService;
use App\Services\ResetPasswordService;
use App\Services\UuidService;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ActivityRepositoryInterface::class, ActivityRepository::class);
        $this->app->bind(TrainingRepositoryInterface::class, TrainingRepository::class);
        $this->app->bind(TrainingFileRepositoryInterface::class, TrainingFileRepository::class);
        $this->app->bind(\App\Interfaces\Repositories\TrainingCategoryRepositoryInterface::class, \App\Repositories\TrainingCategoryRepository::class);
        $this->app->bind(FileStorageServiceInterface::class, function ($app) {
            return new FileStorageService(
                $app->make('filesystem')->disk('public')
            );
        });
        $this->app->bind(UuidServiceInterface::class, UuidService::class);
        $this->app->bind(\App\Interfaces\Repositories\UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Interfaces\Repositories\TrainingUserRepositoryInterface::class, \App\Repositories\TrainingUserRepository::class);

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
