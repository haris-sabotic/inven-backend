<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAdController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/ads', [AdController::class, 'index']);
Route::get('/ads/{ad}', [AdController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/user/edit', [UserController::class, 'edit']);

    Route::middleware('user-type:individual')->group(function () {
        Route::post('/user/ads/{ad}/apply', [UserAdController::class, 'apply']);
        Route::get('/user/ads/{ad}/applied', [UserAdController::class, 'applied']);
    });

    Route::middleware('user-type:company')->group(function () {
        Route::post('/user/ads', [UserAdController::class, 'create']);
        Route::get('/user/ads', [UserAdController::class, 'index']);
        Route::delete('/user/ads/{ad}', [UserAdController::class, 'delete']);
        Route::get('/user/ads/{ad}/applications', [UserAdController::class, 'applications']);
    });
});
