<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelContoller;
use App\Http\Controllers\Api\V1\Auth\LoginController;

Route::prefix('v1')->group(function () {
    Route::get('/travels', [TravelContoller::class, 'index']);
    Route::get('/travels/{travel:slug}/tours',[TourController::class , 'index']);

    Route::prefix('admin')->middleware(['auth:sanctum' , 'role:admin'])->group(function (){
        Route::post('/travels', [App\Http\Controllers\Api\V1\Admin\TravelController::class ,'store']);
    });

    Route::post('/login',LoginController::class);
});

