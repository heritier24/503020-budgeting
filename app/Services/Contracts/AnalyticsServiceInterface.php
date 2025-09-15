<?php

namespace App\Services\Contracts;

use App\Models\User;
use Carbon\Carbon;

interface AnalyticsServiceInterface
{
    /**
     * Generate dashboard overview data.
     */
    public function generateDashboardData(User $user): array;

    /**
     * Get spending trends for a specific period.
     */
    public function getSpendingTrends(User $user, Carbon $startDate, Carbon $endDate): array;

    /**
     * Get category breakdown for spending.
     */
    public function getCategoryBreakdown(User $user, Carbon $startDate, Carbon $endDate): array;

    /**
     * Get budget performance metrics.
     */
    public function getBudgetPerformance(User $user, Carbon $startDate, Carbon $endDate): array;
}