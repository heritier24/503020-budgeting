<?php

namespace App\Services;

use App\Models\User;
use App\Services\Contracts\BudgetCalculationServiceInterface;
use App\Services\Contracts\IncomeServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BudgetCalculationService implements BudgetCalculationServiceInterface
{
    public function __construct(
        private IncomeServiceInterface $incomeService
    ) {}

    /**
     * Calculate budget allocation based on user's income and configuration.
     */
    public function calculateBudgetAllocation(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $budgetConfig = $user->budgetConfig;
        $totalIncome = $this->incomeService->calculateTotalIncome($user, $startDate, $endDate);

        if (!$budgetConfig || $totalIncome <= 0) {
            return [
                'needs' => 0,
                'wants' => 0,
                'savings' => 0,
                'total_income' => $totalIncome,
            ];
        }

        return [
            'needs' => $totalIncome * ($budgetConfig->needs_percentage / 100),
            'wants' => $totalIncome * ($budgetConfig->wants_percentage / 100),
            'savings' => $totalIncome * ($budgetConfig->savings_percentage / 100),
            'total_income' => $totalIncome,
            'percentages' => [
                'needs' => $budgetConfig->needs_percentage,
                'wants' => $budgetConfig->wants_percentage,
                'savings' => $budgetConfig->savings_percentage,
            ],
        ];
    }

    /**
     * Get budget summary for a specific period.
     */
    public function getBudgetSummary(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $allocation = $this->calculateBudgetAllocation($user, $startDate, $endDate);
        $spending = $this->getSpendingByCategory($user, $startDate, $endDate);

        return [
            'allocation' => $allocation,
            'spending' => $spending,
            'remaining' => [
                'needs' => $allocation['needs'] - $spending['needs'],
                'wants' => $allocation['wants'] - $spending['wants'],
                'savings' => $allocation['savings'] - $spending['savings'],
            ],
            'status' => $this->calculateBudgetStatus($allocation, $spending),
        ];
    }

    /**
     * Calculate remaining budget for each category.
     */
    public function calculateRemainingBudget(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $summary = $this->getBudgetSummary($user, $startDate, $endDate);
        
        return [
            'needs' => [
                'allocated' => $summary['allocation']['needs'],
                'spent' => $summary['spending']['needs'],
                'remaining' => $summary['remaining']['needs'],
                'percentage_used' => $summary['allocation']['needs'] > 0 
                    ? ($summary['spending']['needs'] / $summary['allocation']['needs']) * 100 
                    : 0,
            ],
            'wants' => [
                'allocated' => $summary['allocation']['wants'],
                'spent' => $summary['spending']['wants'],
                'remaining' => $summary['remaining']['wants'],
                'percentage_used' => $summary['allocation']['wants'] > 0 
                    ? ($summary['spending']['wants'] / $summary['allocation']['wants']) * 100 
                    : 0,
            ],
            'savings' => [
                'allocated' => $summary['allocation']['savings'],
                'spent' => $summary['spending']['savings'],
                'remaining' => $summary['remaining']['savings'],
                'percentage_used' => $summary['allocation']['savings'] > 0 
                    ? ($summary['spending']['savings'] / $summary['allocation']['savings']) * 100 
                    : 0,
            ],
        ];
    }

    /**
     * Get spending by category type.
     */
    private function getSpendingByCategory(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $spending = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->select('categories.type', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.type')
            ->pluck('total', 'type')
            ->toArray();

        return [
            'needs' => $spending['needs'] ?? 0,
            'wants' => $spending['wants'] ?? 0,
            'savings' => $spending['savings'] ?? 0,
        ];
    }

    /**
     * Calculate budget status for each category.
     */
    private function calculateBudgetStatus(array $allocation, array $spending): array
    {
        $status = [];
        
        foreach (['needs', 'wants', 'savings'] as $category) {
            $allocated = $allocation[$category];
            $spent = $spending[$category];
            
            if ($allocated == 0) {
                $status[$category] = 'no_budget';
            } elseif ($spent > $allocated) {
                $status[$category] = 'over_budget';
            } elseif ($spent > $allocated * 0.8) {
                $status[$category] = 'approaching_limit';
            } else {
                $status[$category] = 'under_budget';
            }
        }
        
        return $status;
    }
}