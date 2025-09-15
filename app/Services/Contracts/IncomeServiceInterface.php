<?php

namespace App\Services\Contracts;

use App\Models\User;
use Carbon\Carbon;

interface IncomeServiceInterface
{
    /**
     * Calculate total income for a user in a specific period.
     */
    public function calculateTotalIncome(User $user, Carbon $startDate, Carbon $endDate): float;

    /**
     * Get income sources for a user.
     */
    public function getIncomeSources(User $user): array;

    /**
     * Get income records for a user in a specific period.
     */
    public function getIncomeRecords(User $user, Carbon $startDate, Carbon $endDate): array;

    /**
     * Calculate average monthly income.
     */
    public function calculateAverageMonthlyIncome(User $user, int $months = 6): float;
}