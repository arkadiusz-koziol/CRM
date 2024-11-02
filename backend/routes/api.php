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
                    ->name('admin.users.store')
                    ->permission('user.create');
                Route::get('/user/{user}', [AdminUserController::class, 'show'])
                    ->name('admin.users.show')
                    ->permission('user.show');
                Route::put('/user/{user}', [AdminUserController::class, 'update'])
                    ->name('admin.users.update')
                    ->permission('user.update');
                Route::delete('/user/{user}', [AdminUserController::class, 'destroy'])
                    ->name('admin.users.destroy')
                    ->permission('user.delete');
                Route::get('/list', [AdminUserController::class, 'list'])
                    ->name('admin.users.list')
                    ->permission('user.list');
            });

        // Regular user routes
        Route::prefix('users')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('{user}', [UserController::class, 'show'])
                    ->name('users.show');
                Route::put('{user}', [UserController::class, 'update'])
                    ->name('users.update');
                Route::post('change-password', [UserController::class, 'changePassword'])
                    ->name('users.change_password');
            });
    }
);
