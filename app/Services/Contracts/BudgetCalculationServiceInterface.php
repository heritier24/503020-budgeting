<?php

namespace App\Services\Contracts;

use App\Models\User;
use Carbon\Carbon;

interface BudgetCalculationServiceInterface
{
    /**
     * Calculate budget allocation based on user's income and configuration.
     */
    public function calculateBudgetAllocation(User $user, Carbon $startDate, Carbon $endDate): array;

    /**
     * Get budget summary for a specific period.
     */
    public function getBudgetSummary(User $user, Carbon $startDate, Carbon $endDate): array;

    /**
     * Calculate remaining budget for each category.
     */
    public function calculateRemainingBudget(User $user, Carbon $startDate, Carbon $endDate): array;
}