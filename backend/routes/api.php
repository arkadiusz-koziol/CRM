<?php

use App\Http\Controllers\Admin\CityController as AdminCityController;
use App\Http\Controllers\Admin\EstateController as AdminEstateController;
use App\Http\Controllers\Admin\PinController as AdminPinController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ToolController as AdminToolController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\PinController;
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
                        ->name('tools.show')
                        ->can('tool.show');
                    Route::post('/', [AdminToolController::class, 'store'])
                        ->name('tools.store')
                        ->can('tool.create');
                    Route::put('/{tool}', [AdminToolController::class, 'update'])
                        ->name('tools.update')
                        ->can('tool.update');
                    Route::delete('/{tool}', [AdminToolController::class, 'destroy'])
                        ->name('tools.destroy')
                        ->can('tool.delete');
                });

                // Admin materials
                Route::prefix('materials')->group(function () {
                    Route::get('/list', [AdminMaterialController::class, 'list'])
                        ->name('materials.index')
                        ->can('material.list');
                    Route::get('/{material}', [AdminMaterialController::class, 'show'])
                        ->name('materials.show')
                        ->can('material.show');
                    Route::post('/', [AdminMaterialController::class, 'store'])
                        ->name('materials.store')
                        ->can('material.create');
                    Route::put('/{material}', [AdminMaterialController::class, 'update'])
                        ->name('materials.update')
                        ->can('material.update');
                    Route::delete('/{material}', [AdminMaterialController::class, 'destroy'])
                        ->name('materials.destroy')
                        ->can('material.delete');
                });

                // Admin cities
                Route::prefix('cities')->group(function () {
                    Route::get('/list', [AdminCityController::class, 'list'])
                        ->name('cities.index')
                        ->can('city.list');
                    Route::post('/', [AdminCityController::class, 'store'])
                        ->name('cities.store')
                        ->can('city.create');
                    Route::put('/{city}', [AdminCityController::class, 'update'])
                        ->name('cities.update')
                        ->can('city.update');
                    Route::delete('/{city}', [AdminCityController::class, 'destroy'])
                        ->name('cities.destroy')
                        ->can('city.delete');
                });

                // Admin estates
                Route::prefix('estates')->group(function () {
                    Route::get('/list', [AdminEstateController::class, 'list'])
                        ->name('estates.index')
                        ->can('estate.list');
                    Route::post('/', [AdminEstateController::class, 'store'])
                        ->name('estates.store')
                        ->can('estate.create');
                    Route::put('/{estate}', [AdminEstateController::class, 'update'])
                        ->name('estates.update')
                        ->can('estate.update');
                    Route::delete('/{estate}', [AdminEstateController::class, 'destroy'])
                        ->name('estates.destroy')
                        ->can('estate.delete');
                    Route::get('/{estate}', [AdminEstateController::class, 'show'])
                        ->name('estates.show')
                        ->can('estate.show');
                });

                //Admin Plans
                Route::prefix('plans')->group(function () {
                    Route::get('/{estate}', [PlanController::class, 'index']);
                    Route::post('/{estate}', [PlanController::class, 'store']);
                    Route::delete('/{estate}', [PlanController::class, 'destroy']);
                });

                //Admin Pins
                Route::prefix('pins/{plan}')->group(function () {
                    Route::get('/', [AdminPinController::class, 'index']);
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

                Route::prefix('plans/{plan}/pins')->group(function () {
                    Route::get('/', [PinController::class, 'index']);
                    Route::post('/', [PinController::class, 'store']);
                });
            });
    }
);
