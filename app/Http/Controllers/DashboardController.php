<?php

namespace App\Http\Controllers;

use App\Models\BudgetConfig;
use App\Models\IncomeSource;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with data
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's budget configuration
        $budgetConfig = $user->budgetConfig;
        
        // Get user's income sources
        $incomeSources = $user->incomeSources()->where('is_active', true)->get();
        
        // Get current month transactions
        $currentMonth = now()->format('Y-m');
        $transactions = $user->transactions()
            ->where('transaction_date', 'like', $currentMonth . '%')
            ->get();
        
        // Calculate current month income and expenses
        $currentIncome = $transactions->where('type', 'income')->sum('amount');
        $currentExpenses = $transactions->where('type', 'expense')->sum('amount');
        
        // Get user's active loans
        $activeLoans = $user->loans()->where('status', 'active')->get();
        $totalMonthlyLoanPayments = $activeLoans->sum('monthly_payment');
        $totalDebt = $activeLoans->sum('current_balance');
        
        // Get user's active goals
        $activeGoals = $user->goals()->where('status', 'active')->get();
        $savingsGoals = $activeGoals->where('type', 'savings');
        $debtPayoffGoals = $activeGoals->where('type', 'debt_payoff');
        $expenseReductionGoals = $activeGoals->where('type', 'expense_reduction');
        $incomeGoals = $activeGoals->where('type', 'income');
        
        // Calculate goal-related metrics
        $totalSavingsGoalTarget = $savingsGoals->sum('target_amount');
        $totalSavingsGoalCurrent = $savingsGoals->sum('current_amount');
        $totalDebtPayoffGoalTarget = $debtPayoffGoals->sum('target_amount');
        $totalDebtPayoffGoalCurrent = $debtPayoffGoals->sum('current_amount');
        
        // Calculate total goal contributions (amounts deducted from savings budget)
        $totalGoalContributions = $activeGoals->sum('total_contributions');
        
        // Use actual monthly income for budget calculations (50/30/20 rule)
        $monthlyIncome = $currentIncome > 0 ? $currentIncome : $incomeSources->sum('expected_monthly');
        
        // Calculate available income after loan payments
        $availableIncome = $monthlyIncome - $totalMonthlyLoanPayments;
        
        // Calculate budget allocations based on available income (after loan payments)
        $needsAmount = $availableIncome * ($budgetConfig->needs_percentage / 100);
        $wantsAmount = $availableIncome * ($budgetConfig->wants_percentage / 100);
        $savingsAmount = $availableIncome * ($budgetConfig->savings_percentage / 100);
        
        // Calculate remaining budget (after loan payments)
        $remainingBudget = $availableIncome - $currentExpenses;
        
        // Calculate actual spending by category type using database queries
        $needsSpent = $user->transactions()
            ->where('transaction_date', 'like', $currentMonth . '%')
            ->where('type', 'expense')
            ->whereHas('category', function($query) {
                $query->where('type', 'needs');
            })
            ->sum('amount');
            
        $wantsSpent = $user->transactions()
            ->where('transaction_date', 'like', $currentMonth . '%')
            ->where('type', 'expense')
            ->whereHas('category', function($query) {
                $query->where('type', 'wants');
            })
            ->sum('amount');
            
        $savingsSpent = $user->transactions()
            ->where('transaction_date', 'like', $currentMonth . '%')
            ->where('type', 'expense')
            ->whereHas('category', function($query) {
                $query->where('type', 'savings');
            })
            ->sum('amount');
            
        // Add goal contributions to savings spent (since they're deducted from savings budget)
        $savingsSpentWithGoals = $savingsSpent + $totalGoalContributions;
        
        // Prepare dashboard data
        $dashboardData = [
            'user' => $user,
            'budget_config' => $budgetConfig,
            'income_sources' => $incomeSources,
            'monthly_income' => $monthlyIncome,
            'available_income' => $availableIncome,
            'current_expenses' => $currentExpenses,
            'current_income' => $currentIncome,
            'remaining_budget' => $remainingBudget,
            'loan_info' => [
                'total_monthly_payments' => $totalMonthlyLoanPayments,
                'total_debt' => $totalDebt,
                'active_loans_count' => $activeLoans->count(),
            ],
            'goals_info' => [
                'active_goals_count' => $activeGoals->count(),
                'total_contributions' => $totalGoalContributions,
                'savings_goals' => [
                    'count' => $savingsGoals->count(),
                    'target_amount' => $totalSavingsGoalTarget,
                    'current_amount' => $totalSavingsGoalCurrent,
                    'progress_percentage' => $totalSavingsGoalTarget > 0 ? ($totalSavingsGoalCurrent / $totalSavingsGoalTarget) * 100 : 0,
                ],
                'debt_payoff_goals' => [
                    'count' => $debtPayoffGoals->count(),
                    'target_amount' => $totalDebtPayoffGoalTarget,
                    'current_amount' => $totalDebtPayoffGoalCurrent,
                    'progress_percentage' => $totalDebtPayoffGoalTarget > 0 ? ($totalDebtPayoffGoalCurrent / $totalDebtPayoffGoalTarget) * 100 : 0,
                ],
                'expense_reduction_goals_count' => $expenseReductionGoals->count(),
                'income_goals_count' => $incomeGoals->count(),
            ],
            'budget_allocation' => [
                'needs' => [
                    'percentage' => $budgetConfig->needs_percentage,
                    'amount' => $needsAmount,
                    'spent' => $needsSpent,
                ],
                'wants' => [
                    'percentage' => $budgetConfig->wants_percentage,
                    'amount' => $wantsAmount,
                    'spent' => $wantsSpent,
                ],
                'savings' => [
                    'percentage' => $budgetConfig->savings_percentage,
                    'amount' => $savingsAmount,
                    'spent' => $savingsSpent,
                    'spent_with_goals' => $savingsSpentWithGoals,
                    'goal_contributions' => $totalGoalContributions,
                ],
            ],
            'recent_transactions' => $transactions->sortByDesc('transaction_date')->take(5),
            'categories' => Category::where('user_id', $user->id)
                ->orWhereNull('user_id')
                ->get(),
        ];

        return Inertia::render('Dashboard', [
            'data' => $dashboardData
        ]);
    }
}