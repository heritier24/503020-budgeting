<?php

namespace App\Services;

use App\Models\User;
use App\Services\Contracts\AnalyticsServiceInterface;
use App\Services\Contracts\BudgetCalculationServiceInterface;
use App\Services\Contracts\IncomeServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService implements AnalyticsServiceInterface
{
    public function __construct(
        private BudgetCalculationServiceInterface $budgetService,
        private IncomeServiceInterface $incomeService
    ) {}

    /**
     * Generate dashboard overview data.
     */
    public function generateDashboardData(User $user): array
    {
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $budgetSummary = $this->budgetService->getBudgetSummary($user, $startOfMonth, $endOfMonth);
        $totalIncome = $this->incomeService->calculateTotalIncome($user, $startOfMonth, $endOfMonth);
        $averageIncome = $this->incomeService->calculateAverageMonthlyIncome($user, 6);

        return [
            'current_month' => [
                'budget_summary' => $budgetSummary,
                'total_income' => $totalIncome,
                'average_income' => $averageIncome,
            ],
            'quick_stats' => [
                'total_spent' => array_sum($budgetSummary['spending']),
                'total_allocated' => array_sum($budgetSummary['allocation']),
                'total_remaining' => array_sum($budgetSummary['remaining']),
                'budget_utilization' => $budgetSummary['allocation']['total_income'] > 0 
                    ? (array_sum($budgetSummary['spending']) / array_sum($budgetSummary['allocation'])) * 100 
                    : 0,
            ],
            'recent_transactions' => $this->getRecentTransactions($user, 5),
            'income_sources' => $this->incomeService->getIncomeSources($user),
        ];
    }

    /**
     * Get spending trends for a specific period.
     */
    public function getSpendingTrends(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $trends = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            if ($monthEnd->gt($endDate)) {
                $monthEnd = $endDate;
            }

            $monthlySpending = $this->getMonthlySpending($user, $monthStart, $monthEnd);
            
            $trends[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'spending' => $monthlySpending,
                'total' => array_sum($monthlySpending),
            ];

            $current->addMonth();
        }

        return $trends;
    }

    /**
     * Get category breakdown for spending.
     */
    public function getCategoryBreakdown(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $breakdown = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->select(
                'categories.name',
                'categories.type',
                'categories.color',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.type', 'categories.color', 'categories.icon')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();

        $totalSpending = array_sum(array_column($breakdown, 'total'));

        return array_map(function ($category) use ($totalSpending) {
            $category->percentage = $totalSpending > 0 ? ($category->total / $totalSpending) * 100 : 0;
            return $category;
        }, $breakdown);
    }

    /**
     * Get budget performance metrics.
     */
    public function getBudgetPerformance(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $budgetSummary = $this->budgetService->getBudgetSummary($user, $startDate, $endDate);
        $remaining = $this->budgetService->calculateRemainingBudget($user, $startDate, $endDate);

        return [
            'overall_performance' => $this->calculateOverallPerformance($budgetSummary),
            'category_performance' => $remaining,
            'recommendations' => $this->generateRecommendations($budgetSummary, $remaining),
        ];
    }

    /**
     * Get recent transactions for dashboard.
     */
    private function getRecentTransactions(User $user, int $limit = 5): array
    {
        return $user->transactions()
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get monthly spending by category.
     */
    private function getMonthlySpending(User $user, Carbon $startDate, Carbon $endDate): array
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
     * Calculate overall budget performance.
     */
    private function calculateOverallPerformance(array $budgetSummary): array
    {
        $totalAllocated = array_sum($budgetSummary['allocation']);
        $totalSpent = array_sum($budgetSummary['spending']);
        $totalRemaining = array_sum($budgetSummary['remaining']);

        $utilizationRate = $totalAllocated > 0 ? ($totalSpent / $totalAllocated) * 100 : 0;

        return [
            'total_allocated' => $totalAllocated,
            'total_spent' => $totalSpent,
            'total_remaining' => $totalRemaining,
            'utilization_rate' => $utilizationRate,
            'status' => $this->getOverallStatus($utilizationRate),
        ];
    }

    /**
     * Get overall budget status.
     */
    private function getOverallStatus(float $utilizationRate): string
    {
        if ($utilizationRate > 100) {
            return 'over_budget';
        } elseif ($utilizationRate > 80) {
            return 'approaching_limit';
        } else {
            return 'on_track';
        }
    }

    /**
     * Generate budget recommendations.
     */
    private function generateRecommendations(array $budgetSummary, array $remaining): array
    {
        $recommendations = [];

        foreach (['needs', 'wants', 'savings'] as $category) {
            $percentageUsed = $remaining[$category]['percentage_used'];
            
            if ($percentageUsed > 100) {
                $recommendations[] = [
                    'type' => 'warning',
                    'category' => $category,
                    'message' => "You've exceeded your {$category} budget by " . number_format($percentageUsed - 100, 1) . "%",
                    'action' => 'Consider reducing expenses in this category or adjusting your budget allocation.',
                ];
            } elseif ($percentageUsed > 80) {
                $recommendations[] = [
                    'type' => 'info',
                    'category' => $category,
                    'message' => "You're approaching your {$category} budget limit ({$percentageUsed}% used)",
                    'action' => 'Monitor your spending closely in this category.',
                ];
            }
        }

        return $recommendations;
    }
}