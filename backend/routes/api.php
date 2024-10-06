<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin routes
Route::prefix('admin/users')
    ->group(function () {
        Route::post('/', [AdminUserController::class, 'store']);
        Route::get('{id}', [AdminUserController::class, 'show']);
        Route::put('{id}', [AdminUserController::class, 'update']);
        Route::delete('{id}', [AdminUserController::class, 'destroy']);
    });

// Basic user routes
Route::prefix('users')
    ->group(function () {
        Route::get('{id}', [UserController::class, 'show']);
        Route::put('{id}', [UserController::class, 'update']);
    });
