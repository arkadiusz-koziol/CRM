<?php

use App\Http\Controllers\Admin\ToolController as AdminToolController;
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

        Route::prefix('admin')
            ->middleware('auth:sanctum')
            ->group(function () {

                // Admin Users
                Route::prefix('users')->group(function () {
                    Route::post('/user', [AdminUserController::class, 'store'])
                        ->name('admin.users.store')
                        ->can('user.create');
                    Route::get('/user/{user}', [AdminUserController::class, 'show'])
                        ->name('admin.users.show')
                        ->can('user.show');
                    Route::put('/user/{user}', [AdminUserController::class, 'update'])
                        ->name('admin.users.update')
                        ->can('user.update');
                    Route::delete('/user/{user}', [AdminUserController::class, 'destroy'])
                        ->name('admin.users.destroy')
                        ->can('user.delete');
                    Route::get('/list', [AdminUserController::class, 'list'])
                        ->name('admin.users.list')
                        ->can('user.list');
                });

                // Admin Tools
                Route::prefix('tools')->group(function () {
                    Route::get('/list', [AdminToolController::class, 'list'])
                        ->name('tools.index')
                        ->can('tool.list');
                    Route::get('/{tool}', [AdminToolController::class, 'show'])
                        ->name('tools.show');
//                        ->can('tool.show');
                    Route::post('/', [AdminToolController::class, 'store'])
                        ->name('tools.store');
//                        ->can('tool.create');
                    Route::put('/{tool}', [AdminToolController::class, 'update'])
                        ->name('tools.update');
//                        ->can('tool.update');
                    Route::delete('/{tool}', [AdminToolController::class, 'destroy'])
                        ->name('tools.destroy');
//                        ->can('tool.delete');
                });


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
