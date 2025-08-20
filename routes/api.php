<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelContoller;

Route::prefix('v1')->group(function () {
    Route::get('/travels', [TravelContoller::class, 'index']);
    Route::get('/travels/{travel:slug}/tours',[TourController::class , 'index']);
});

