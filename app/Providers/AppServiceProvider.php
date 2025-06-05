<?php

namespace App\Providers;

use App\Contracts\CategoryRepository;
use App\Contracts\LocationRepository;
use App\Contracts\MoneyService;
use App\Services\DefaultMoneyService;
use App\Services\EloquentCategoryRepository;
use App\Services\EloquentLocationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MoneyService::class, DefaultMoneyService::class);
        $this->app->bind(CategoryRepository::class, EloquentCategoryRepository::class);
        $this->app->bind(LocationRepository::class, EloquentLocationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
