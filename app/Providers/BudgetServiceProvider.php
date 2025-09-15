<?php

namespace App\Providers;

use App\Services\AnalyticsService;
use App\Services\BudgetCalculationService;
use App\Services\Contracts\AnalyticsServiceInterface;
use App\Services\Contracts\BudgetCalculationServiceInterface;
use App\Services\Contracts\IncomeServiceInterface;
use App\Services\IncomeService;
use Illuminate\Support\ServiceProvider;

class BudgetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind service interfaces to implementations
        $this->app->bind(IncomeServiceInterface::class, IncomeService::class);
        $this->app->bind(BudgetCalculationServiceInterface::class, BudgetCalculationService::class);
        $this->app->bind(AnalyticsServiceInterface::class, AnalyticsService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
