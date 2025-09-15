<?php

namespace App\Http\Controllers;

use App\Models\BudgetConfig;
use App\Models\IncomeSource;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    /**
     * Show the analytics page with data
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's budget configuration
        $budgetConfig = $user->budgetConfig;
        
        // Get user's income sources
        $incomeSources = $user->incomeSources()->where('is_active', true)->get();
        
        // Get current month transactions for actual income
        $currentMonth = now()->format('Y-m');
        $currentMonthTransactions = $user->transactions()
            ->where('transaction_date', 'like', $currentMonth . '%')
            ->get();
        
        $currentIncome = $currentMonthTransactions->where('type', 'income')->sum('amount');
        $monthlyIncome = $currentIncome > 0 ? $currentIncome : $incomeSources->sum('expected_monthly');
        
        // Get last 6 months of transactions
        $sixMonthsAgo = now()->subMonths(6)->format('Y-m');
        $transactions = $user->transactions()
            ->where('transaction_date', '>=', $sixMonthsAgo)
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        // Calculate spending trends by month
        $spendingTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthTransactions = $transactions->filter(function ($transaction) use ($month) {
                return str_starts_with($transaction->transaction_date, $month);
            });
            
            $spendingTrends[] = [
                'month' => now()->subMonths($i)->format('M Y'),
                'expenses' => $monthTransactions->where('type', 'expense')->sum('amount'),
                'income' => $monthTransactions->where('type', 'income')->sum('amount'),
            ];
        }
        
        // Get user's active loans and goals
        $activeLoans = $user->loans()->where('status', 'active')->get();
        $totalMonthlyLoanPayments = $activeLoans->sum('monthly_payment');
        $availableIncome = $monthlyIncome - $totalMonthlyLoanPayments;
        
        $activeGoals = $user->goals()->where('status', 'active')->get();
        $savingsGoals = $activeGoals->where('type', 'savings');
        $debtPayoffGoals = $activeGoals->where('type', 'debt_payoff');
        
        // Calculate budget allocations based on available income (after loan payments)
        $needsAmount = $availableIncome * ($budgetConfig->needs_percentage / 100);
        $wantsAmount = $availableIncome * ($budgetConfig->wants_percentage / 100);
        $savingsAmount = $availableIncome * ($budgetConfig->savings_percentage / 100);
        
        // Calculate category-wise spending for current month
        $currentMonthExpenses = $currentMonthTransactions->where('type', 'expense');
        $needsSpent = $currentMonthExpenses->filter(function($transaction) {
            return $transaction->category && $transaction->category->type === 'needs';
        })->sum('amount');
        
        $wantsSpent = $currentMonthExpenses->filter(function($transaction) {
            return $transaction->category && $transaction->category->type === 'wants';
        })->sum('amount');
        
        $savingsSpent = $currentMonthExpenses->filter(function($transaction) {
            return $transaction->category && $transaction->category->type === 'savings';
        })->sum('amount');
        
        // Calculate spending by category for insights
        $categorySpending = $currentMonthExpenses->groupBy('category_id')->map(function($transactions) {
            $category = $transactions->first()->category;
            return [
                'name' => $category ? $category->name : 'Uncategorized',
                'type' => $category ? $category->type : 'other',
                'amount' => $transactions->sum('amount'),
                'count' => $transactions->count(),
            ];
        })->sortByDesc('amount')->take(10);
        
        // Prepare analytics data
        $analyticsData = [
            'user' => $user,
            'budget_config' => $budgetConfig,
            'monthly_income' => $monthlyIncome,
            'available_income' => $availableIncome,
            'loan_info' => [
                'total_monthly_payments' => $totalMonthlyLoanPayments,
                'active_loans_count' => $activeLoans->count(),
            ],
            'goals_info' => [
                'active_goals_count' => $activeGoals->count(),
                'savings_goals' => [
                    'count' => $savingsGoals->count(),
                    'target_amount' => $savingsGoals->sum('target_amount'),
                    'current_amount' => $savingsGoals->sum('current_amount'),
                    'progress_percentage' => $savingsGoals->sum('target_amount') > 0 ? ($savingsGoals->sum('current_amount') / $savingsGoals->sum('target_amount')) * 100 : 0,
                ],
                'debt_payoff_goals' => [
                    'count' => $debtPayoffGoals->count(),
                    'target_amount' => $debtPayoffGoals->sum('target_amount'),
                    'current_amount' => $debtPayoffGoals->sum('current_amount'),
                    'progress_percentage' => $debtPayoffGoals->sum('target_amount') > 0 ? ($debtPayoffGoals->sum('current_amount') / $debtPayoffGoals->sum('target_amount')) * 100 : 0,
                ],
            ],
            'budget_allocation' => [
                'needs' => [
                    'percentage' => $budgetConfig->needs_percentage,
                    'amount' => $needsAmount,
                    'spent' => $needsSpent,
                    'remaining' => $needsAmount - $needsSpent,
                ],
                'wants' => [
                    'percentage' => $budgetConfig->wants_percentage,
                    'amount' => $wantsAmount,
                    'spent' => $wantsSpent,
                    'remaining' => $wantsAmount - $wantsSpent,
                ],
                'savings' => [
                    'percentage' => $budgetConfig->savings_percentage,
                    'amount' => $savingsAmount,
                    'spent' => $savingsSpent,
                    'remaining' => $savingsAmount - $savingsSpent,
                ],
            ],
            'spending_trends' => $spendingTrends,
            'category_spending' => $categorySpending->values(),
            'insights' => [
                'total_expenses' => $currentMonthExpenses->sum('amount'),
                'average_transaction' => $currentMonthExpenses->count() > 0 ? $currentMonthExpenses->sum('amount') / $currentMonthExpenses->count() : 0,
                'most_spent_category' => $categorySpending->first(),
                'budget_health' => $this->calculateBudgetHealth($needsSpent, $wantsSpent, $savingsSpent, $needsAmount, $wantsAmount, $savingsAmount),
            ],
            'categories' => Category::where('user_id', $user->id)
                ->orWhereNull('user_id')
                ->get(),
        ];

        return Inertia::render('Analytics', [
            'data' => $analyticsData
        ]);
    }

    /**
     * Calculate budget health score
     */
    private function calculateBudgetHealth($needsSpent, $wantsSpent, $savingsSpent, $needsAmount, $wantsAmount, $savingsAmount)
    {
        $needsHealth = $needsAmount > 0 ? min(100, ($needsAmount - $needsSpent) / $needsAmount * 100) : 100;
        $wantsHealth = $wantsAmount > 0 ? min(100, ($wantsAmount - $wantsSpent) / $wantsAmount * 100) : 100;
        $savingsHealth = $savingsAmount > 0 ? min(100, ($savingsAmount - $savingsSpent) / $savingsAmount * 100) : 100;
        
        $overallHealth = ($needsHealth + $wantsHealth + $savingsHealth) / 3;
        
        if ($overallHealth >= 80) {
            return ['score' => $overallHealth, 'status' => 'excellent', 'message' => 'Great job! You\'re staying well within your budget.'];
        } elseif ($overallHealth >= 60) {
            return ['score' => $overallHealth, 'status' => 'good', 'message' => 'Good progress! You\'re mostly on track with your budget.'];
        } elseif ($overallHealth >= 40) {
            return ['score' => $overallHealth, 'status' => 'warning', 'message' => 'Be careful! You\'re approaching your budget limits.'];
        } else {
            return ['score' => $overallHealth, 'status' => 'danger', 'message' => 'Attention needed! You\'ve exceeded your budget in some categories.'];
        }
    }
}