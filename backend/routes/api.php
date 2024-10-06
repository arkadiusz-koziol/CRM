<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\UserRegistrationController;
use Illuminate\Support\Facades\Route;

Route::group(
    ['prefix' => env('API_VERSION', 'v1'), 'middleware' => 'api'],
    function (): void {
        Route::prefix('auth')
            ->group(function () {
                Route::post('register', UserRegistrationController::class)
                    ->name('auth.register');
                Route::post('login', [AuthController::class, 'login'])
                    ->name('auth.login');
                Route::post('logout', [AuthController::class, 'logout'])
                    ->middleware('auth:sanctum')
                    ->name('auth.logout');
            });

        // Admin routes for user management
        Route::prefix('admin/users')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::post('/user', [AdminUserController::class, 'store'])
                    ->name('admin.users.store');
                Route::get('/user/{id}', [AdminUserController::class, 'show'])
                    ->name('admin.users.show');
                Route::put('/user/{id}', [AdminUserController::class, 'update'])
                    ->name('admin.users.update');
                Route::delete('/user/{id}', [AdminUserController::class, 'destroy'])
                    ->name('admin.users.destroy');
                Route::get('/list', [AdminUserController::class, 'list'])
                    ->name('admin.users.list');
            });

        // Regular user routes
        Route::prefix('users')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('{id}', [UserController::class, 'show'])
                    ->name('users.show');
                Route::put('{id}', [UserController::class, 'update'])
                    ->name('users.update');
                Route::post('change-password', [UserController::class, 'changePassword'])
                    ->name('users.change_password');
            });
    }
);
