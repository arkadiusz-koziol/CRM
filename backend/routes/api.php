<?php

use App\Http\Controllers\Admin\Activities\ListActivityController;
use App\Http\Controllers\Admin\Cars\CreateCarController;
use App\Http\Controllers\Admin\Cars\DeleteCarController;
use App\Http\Controllers\Admin\Cars\ListCarController;
use App\Http\Controllers\Admin\Cars\ShowCarController;
use App\Http\Controllers\Admin\Cars\UpdateCarController;
use App\Http\Controllers\Admin\Cities\CreateCityController;
use App\Http\Controllers\Admin\Cities\DestroyCityController;
use App\Http\Controllers\Admin\Cities\ListCityController;
use App\Http\Controllers\Admin\Cities\UpdateCityController;
use App\Http\Controllers\Admin\Dashboard\StatsController;
use App\Http\Controllers\Admin\Estates\DestroyEstateController;
use App\Http\Controllers\Admin\Estates\ListEstateController;
use App\Http\Controllers\Admin\Estates\ShowEstateController;
use App\Http\Controllers\Admin\Estates\StoreEstateController;
use App\Http\Controllers\Admin\Estates\UpdateEstateController;
use App\Http\Controllers\Admin\Materials\DestroyMaterialController;
use App\Http\Controllers\Admin\Materials\ListMaterialController;
use App\Http\Controllers\Admin\Materials\ShowMaterialController;
use App\Http\Controllers\Admin\Materials\StoreMaterialController;
use App\Http\Controllers\Admin\Materials\UpdateMaterialController;
use App\Http\Controllers\Admin\Pins\ShowPinByPlanController;
use App\Http\Controllers\Admin\Plans\DestroyPlanController;
use App\Http\Controllers\Admin\Plans\ShowPlanController;
use App\Http\Controllers\Admin\Plans\StorePlanController;
use App\Http\Controllers\Admin\Tasks\DestroyTaskController;
use App\Http\Controllers\Admin\Tasks\ListTaskController;
use App\Http\Controllers\Admin\Tasks\ShowTaskController;
use App\Http\Controllers\Admin\Tasks\StoreTaskController;
use App\Http\Controllers\Admin\Tasks\UpdateTaskController;
use App\Http\Controllers\Admin\Tools\DestroyToolController;
use App\Http\Controllers\Admin\Tools\ListToolController;
use App\Http\Controllers\Admin\Tools\ShowToolController;
use App\Http\Controllers\Admin\Tools\StoreToolController;
use App\Http\Controllers\Admin\Tools\UpdateToolController;
use App\Http\Controllers\Admin\Trainings\CreateTrainingController;
use App\Http\Controllers\Admin\Trainings\DeleteTrainingController;
use App\Http\Controllers\Admin\Trainings\UpdateTrainingController;
use App\Http\Controllers\Admin\TrainingUsers\AssignAllUsersToTrainingController;
use App\Http\Controllers\Admin\TrainingUsers\AssignSelectedUsersToTrainingController;
use App\Http\Controllers\Admin\TrainingUsers\AssignUsersByRoleToTrainingController;
use App\Http\Controllers\Admin\TrainingUsers\AssignUserToTrainingController;
use App\Http\Controllers\Admin\TrainingUsers\GetTrainingUsersController;
use App\Http\Controllers\Admin\TrainingUsers\RemoveUserFromTrainingController;
use App\Http\Controllers\Admin\Users\DestroyUserController;
use App\Http\Controllers\Admin\Users\ListUserController;
use App\Http\Controllers\Admin\Users\ShowUserController;
use App\Http\Controllers\Admin\Users\StoreUserController;
use App\Http\Controllers\Admin\Users\UpdateUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserRegistrationController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(
    ['prefix' => env('API_VERSION', 'v1'), 'middleware' => 'api'],
    function (): void {
        Route::prefix('auth')
            ->group(function () {
                Route::post('register', UserRegistrationController::class)
                    ->name('auth.register');
                Route::post('login', LoginController::class)
                    ->name('auth.login');
                Route::post('forgot-password', ForgotPasswordController::class)
                    ->name('auth.forgot-password');
                Route::post('reset-password', ResetPasswordController::class)
                    ->name('auth.reset-password');
                Route::get('me', MeController::class)
                    ->middleware('auth:sanctum')
                    ->name('auth.me');
                Route::post('logout', LogoutController::class)
                    ->middleware('auth:sanctum')
                    ->name('auth.logout');
            });

        Route::prefix('admin')
            ->middleware('auth:sanctum')
            ->group(function () {

                // Admin Users
                Route::prefix('users')->group(function () {
                    Route::post('/user', StoreUserController::class)
                        ->name('admin.users.store')
                        ->can('user.create');
                    Route::get('/user/{user}', ShowUserController::class)
                        ->name('admin.users.show')
                        ->can('user.show');
                    Route::put('/user/{user}', UpdateUserController::class)
                        ->name('admin.users.update')
                        ->can('user.update');
                    Route::delete('/user/{user}', DestroyUserController::class)
                        ->name('admin.users.destroy')
                        ->can('user.delete');
                    Route::get('/list', ListUserController::class)
                        ->name('admin.users.list')
                        ->can('user.list');
                });

                // Admin Tools
                Route::prefix('tools')->group(function () {
                    Route::get('/list', ListToolController::class)
                        ->name('tools.index')
                        ->can('tool.list');
                    Route::get('/{tool}', ShowToolController::class)
                        ->name('tools.show')
                        ->can('tool.show');
                    Route::post('/', StoreToolController::class)
                        ->name('tools.store')
                        ->can('tool.create');
                    Route::put('/{tool}', UpdateToolController::class)
                        ->name('tools.update')
                        ->can('tool.update');
                    Route::delete('/{tool}', DestroyToolController::class)
                        ->name('tools.destroy')
                        ->can('tool.delete');
                });

                // Admin materials
                Route::prefix('materials')->group(function () {
                    Route::get('/list', ListMaterialController::class)
                        ->name('materials.index')
                        ->can('material.list');
                    Route::get('/{material}', ShowMaterialController::class)
                        ->name('materials.show')
                        ->can('material.show');
                    Route::post('/', StoreMaterialController::class)
                        ->name('materials.store')
                        ->can('material.create');
                    Route::put('/{material}', UpdateMaterialController::class)
                        ->name('materials.update')
                        ->can('material.update');
                    Route::delete('/{material}', DestroyMaterialController::class)
                        ->name('materials.destroy')
                        ->can('material.delete');
                });

                // Admin cities
                Route::prefix('cities')->group(function () {
                    Route::get('/list', ListCityController::class)
                        ->name('cities.index')
                        ->can('city.list');
                    Route::post('/', CreateCityController::class)
                        ->name('cities.store')
                        ->can('city.create');
                    Route::put('/{city}', UpdateCityController::class)
                        ->name('cities.update')
                        ->can('city.update');
                    Route::delete('/{city}', DestroyCityController::class)
                        ->name('cities.destroy')
                        ->can('city.delete');
                });

                // Admin estates
                Route::prefix('estates')->group(function () {
                    Route::get('/list', ListEstateController::class)
                        ->name('estates.index')
                        ->can('estate.list');
                    Route::post('/', StoreEstateController::class)
                        ->name('estates.store')
                        ->can('estate.create');
                    Route::put('/{estate}', UpdateEstateController::class)
                        ->name('estates.update')
                        ->can('estate.update');
                    Route::delete('/{estate}', DestroyEstateController::class)
                        ->name('estates.destroy')
                        ->can('estate.delete');
                    Route::get('/{estate}', ShowEstateController::class)
                        ->name('estates.show')
                        ->can('estate.show');
                });

                // Admin Plans
                Route::prefix('plans')->group(function () {
                    Route::get('/{estate}', ShowPlanController::class)
                        ->can('plan.list');
                    Route::post('/{estate}', StorePlanController::class)
                        ->can('plan.create');
                    Route::delete('/{estate}', DestroyPlanController::class)
                        ->can('plan.delete');
                });

                // Admin Pins
                Route::prefix('pins/{plan}')->group(function () {
                    Route::get('/', ShowPinByPlanController::class)
                        ->can('pin.list.by.plan');
                });

                // Admin Cars
                Route::prefix('cars')->group(function () {
                    Route::get('/list', ListCarController::class)
                        ->name('cars.index')
                        ->can('car.list');
                    Route::get('/{car}', ShowCarController::class)
                        ->name('cars.show')
                        ->can('car.show');
                    Route::post('/create', CreateCarController::class)
                        ->can('car.create');
                    Route::put('/{car}', UpdateCarController::class)
                        ->name('cars.update')
                        ->can('car.update');
                    Route::delete('/{car}', DeleteCarController::class)
                        ->name('cars.delete')
                        ->can('car.delete');
                });

                // Admin Trainings
                Route::prefix('trainings')->group(function () {
                    Route::post('/', CreateTrainingController::class)
                        ->name('trainings.create')
                        ->can('training.create');
                    Route::put('/{training}', UpdateTrainingController::class)
                        ->name('trainings.update')
                        ->can('training.update');
                    Route::delete('/{training}', DeleteTrainingController::class)
                        ->name('trainings.delete')
                        ->can('training.delete');

                    // Training User Assignment Routes
                    Route::prefix('{training}/users')->group(function () {
                        Route::get('/', GetTrainingUsersController::class)
                            ->name('training.users.index')
                            ->can('training.user.list');
                        Route::post('/', AssignUserToTrainingController::class)
                            ->name('training.users.assign')
                            ->can('training.user.assign');
                        Route::delete('/{user}', RemoveUserFromTrainingController::class)
                            ->name('training.users.remove')
                            ->can('training.user.remove');
                        Route::post('/assign-all', AssignAllUsersToTrainingController::class)
                            ->name('training.users.assign_all')
                            ->can('training.user.assign_all');
                        Route::post('/assign-by-role', AssignUsersByRoleToTrainingController::class)
                            ->name('training.users.assign_by_role')
                            ->can('training.user.assign_by_role');
                        Route::post('/assign-selected', AssignSelectedUsersToTrainingController::class)
                            ->name('training.users.assign_selected')
                            ->can('training.user.assign_selected');
                    });
                });

                // Admin Tasks
                Route::prefix('tasks')->group(function () {
                    Route::get('/list', ListTaskController::class)
                        ->name('tasks.index')
                        ->can('task.list');
                    Route::get('/{task}', ShowTaskController::class)
                        ->name('tasks.show')
                        ->can('task.show');
                    Route::post('/', StoreTaskController::class)
                        ->name('tasks.store')
                        ->can('task.create');
                    Route::put('/{task}', UpdateTaskController::class)
                        ->name('tasks.update')
                        ->can('task.update');
                    Route::delete('/{task}', DestroyTaskController::class)
                        ->name('tasks.destroy')
                        ->can('task.delete');
                });

                // Admin Activities
                Route::prefix('activities')->group(function () {
                    Route::get('/list', ListActivityController::class)
                        ->name('activities.index')
                        ->can('activity.list');
                });

                // Admin Dashboard
                Route::prefix('dashboard')->group(function () {
                    Route::get('/stats', StatsController::class)
                        ->name('dashboard.stats');
                });
            });

        // Regular user routes
        Route::prefix('users')
            ->middleware('auth:sanctum')
            ->group(function () {
                Route::get('{user}', [UserController::class, 'show'])
                    ->can('user.show')
                    ->name('users.show');
                Route::put('{user}', [UserController::class, 'update'])
                    ->can('user.update')
                    ->name('users.update');
                Route::post('change-password', [UserController::class, 'changePassword'])
                    ->can('user.change_password')
                    ->name('users.change_password');

                Route::prefix('plans/{plan}/pins')->group(function () {
                    Route::get('/', [PinController::class, 'index'])
                        ->can('user.pin.list.by.plan');
                    Route::post('/', [PinController::class, 'store'])
                        ->can('user.pin.create');
                });
            });
    }
);
