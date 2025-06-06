<?php

namespace App\Providers;

use App\Contracts\CategoryRepository;
use App\Contracts\LocationRepository;
use App\Contracts\MoneyService;
use App\Contracts\PriceCalculatorService;
use App\Services\DefaultMoneyService;
use App\Services\DefaultPriceCalculatorService;
use App\Services\EloquentCategoryRepository;
use App\Services\EloquentLocationRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MoneyService::class, static function (Application $app) {
            return new DefaultMoneyService($app->make('config')->get('money', []));
        });
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(LocationRepository::class, EloquentLocationRepository::class);
        $this->app->bind(PriceCalculatorService::class, static function (Application $app) {
            return $app->make(DefaultPriceCalculatorService::class, [
                'config' => $app->make('config')->get('price_calculator', []),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
