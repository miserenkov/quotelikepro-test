<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 19:51
 */

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PriceCalculationController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class)->only('index');
Route::apiResource('locations', LocationController::class)->only('index');

Route::post('price-calculation/calculate', [PriceCalculationController::class, 'calculate'])
    ->name('price-calculation.calculate');
