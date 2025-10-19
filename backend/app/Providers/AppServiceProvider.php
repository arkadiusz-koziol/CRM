<?php

declare(strict_types=1);

namespace App\Providers;

use App\Infrastructure\Automation\WorkflowMapper;
use App\Infrastructure\Automation\WorkflowRuleMapper;
use App\Infrastructure\Billing\ContractMapper;
use App\Infrastructure\Billing\InvoiceMapper;
use App\Interfaces\Repositories\ActivityRepositoryInterface;
use App\Interfaces\Repositories\ContractRepositoryInterface;
use App\Interfaces\Repositories\InvoiceRepositoryInterface;
use App\Interfaces\Repositories\TrainingFileRepositoryInterface;
use App\Interfaces\Repositories\TrainingRepositoryInterface;
use App\Interfaces\Repositories\WorkflowRepositoryInterface;
use App\Interfaces\Repositories\WorkflowRuleRepositoryInterface;
use App\Interfaces\Services\FileStorageServiceInterface;
use App\Interfaces\Services\UuidServiceInterface;
use App\Repositories\ActivityRepository;
use App\Repositories\ContractRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\TrainingFileRepository;
use App\Repositories\TrainingRepository;
use App\Repositories\WorkflowRepository;
use App\Repositories\WorkflowRuleRepository;
use App\Services\Automation\WorkflowActionExecutor;
use App\Services\Automation\WorkflowConditionEvaluator;
use App\Services\Automation\WorkflowService;
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

        // Billing repository bindings
        $this->app->bind(ContractRepositoryInterface::class, function ($app) {
            return new ContractRepository(new ContractMapper);
        });

        $this->app->bind(InvoiceRepositoryInterface::class, function ($app) {
            return new InvoiceRepository(new InvoiceMapper);
        });

        // Workflow repository bindings
        $this->app->bind(WorkflowRepositoryInterface::class, function ($app) {
            return new WorkflowRepository(new WorkflowMapper);
        });

        $this->app->bind(WorkflowRuleRepositoryInterface::class, function ($app) {
            return new WorkflowRuleRepository(new WorkflowRuleMapper);
        });

        // Report repository bindings
        $this->app->bind(\App\Interfaces\Repositories\ReportRepositoryInterface::class, function ($app) {
            return new \App\Repositories\ReportRepository(
                $app->make(\App\Infrastructure\Reports\ReportMapper::class)
            );
        });

        $this->app->bind(\App\Interfaces\Repositories\ReportRunRepositoryInterface::class, function ($app) {
            return new \App\Repositories\ReportRunRepository(
                $app->make(\App\Infrastructure\Reports\ReportRunMapper::class)
            );
        });

        // Comment repository bindings
        $this->app->bind(\App\Interfaces\Repositories\CommentRepositoryInterface::class, function ($app) {
            return new \App\Repositories\CommentRepository(
                $app->make(\App\Infrastructure\Collab\CommentMapper::class)
            );
        });

        // Mention repository bindings
        $this->app->bind(\App\Interfaces\Repositories\MentionRepositoryInterface::class, function ($app) {
            return new \App\Repositories\MentionRepository(
                $app->make(\App\Models\Mention::class)
            );
        });

        // Notification service bindings
        $this->app->bind(\App\Interfaces\Services\RealtimeNotificationServiceInterface::class, \App\Services\Notification\RealtimeNotificationService::class);

        // Workflow service bindings
        $this->app->bind(WorkflowService::class, function ($app) {
            return new WorkflowService(
                $app->make(WorkflowRepositoryInterface::class),
                $app->make(WorkflowRuleRepositoryInterface::class),
                new WorkflowConditionEvaluator,
                new WorkflowActionExecutor($app->make(\Psr\Log\LoggerInterface::class)),
                $app->make(\Psr\Log\LoggerInterface::class)
            );
        });

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
        // Register event listeners
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\CommentAdded::class,
            \App\Listeners\ParseMentionsFromComment::class
        );
    }
}
