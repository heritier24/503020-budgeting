<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Loan;
use App\Models\IncomeSource;
use App\Models\BudgetConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        $data = [
            // User Statistics
            'total_users' => User::count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'new_users_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'active_users' => User::where('updated_at', '>=', now()->subDays(30))->count(),
            
            // Feature Usage Statistics
            'users_with_transactions' => User::whereHas('transactions')->count(),
            'users_with_goals' => User::whereHas('goals')->count(),
            'users_with_loans' => User::whereHas('loans')->count(),
            'users_with_budget_config' => User::whereHas('budgetConfig')->count(),
            
            // Transaction Statistics
            'total_transactions' => Transaction::count(),
            'transactions_this_month' => Transaction::whereMonth('created_at', now()->month)->count(),
            'total_transaction_amount' => Transaction::sum('amount'),
            'average_transaction_amount' => Transaction::avg('amount'),
            
            // Category Usage
            'most_used_categories' => Category::withCount('transactions')
                ->orderBy('transactions_count', 'desc')
                ->limit(10)
                ->get(),
            
            // Goal Statistics
            'total_goals' => Goal::count(),
            'completed_goals' => Goal::where('status', 'completed')->count(),
            'active_goals' => Goal::where('status', 'active')->count(),
            
            // Loan Statistics
            'total_loans' => Loan::count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'total_loan_amount' => Loan::sum('amount'),
            
            // User Growth Chart Data (Last 12 months)
            'user_growth' => $this->getUserGrowthData(),
            
            // Feature Adoption Chart Data
            'feature_adoption' => $this->getFeatureAdoptionData(),
            
            // Recent Activity
            'recent_users' => User::latest()->limit(10)->get(),
            'recent_transactions' => Transaction::with('user', 'category')
                ->latest()
                ->limit(10)
                ->get(),
        ];

        return inertia('Admin/Dashboard', compact('data'));
    }

    /**
     * Show user management page
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by admin status
        if ($request->has('admin_only') && $request->admin_only) {
            $query->where('is_admin', true);
        }

        $users = $query->withCount(['transactions', 'goals', 'loans'])
            ->with(['transactions' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return inertia('Admin/Users', [
            'users' => $users,
            'filters' => $request->only(['search', 'admin_only'])
        ]);
    }

    /**
     * Show individual user details with all transactions
     */
    public function userDetails($userId)
    {
        $user = User::with([
            'transactions' => function ($query) {
                $query->with('category')->latest();
            },
            'goals',
            'loans',
            'budgetConfig',
            'incomeSources'
        ])->findOrFail($userId);

        $data = [
            'user' => $user,
            'transaction_stats' => [
                'total_transactions' => $user->transactions->count(),
                'total_amount' => $user->transactions->sum('amount'),
                'this_month' => $user->transactions()->whereMonth('created_at', now()->month)->count(),
                'this_month_amount' => $user->transactions()->whereMonth('created_at', now()->month)->sum('amount'),
            ],
            'recent_activity' => $user->transactions()->with('category')->latest()->limit(20)->get(),
        ];

        return inertia('Admin/UserDetails', $data);
    }

    /**
     * Get real-time transaction data
     */
    public function realTimeTransactions()
    {
        $recentTransactions = Transaction::with(['user', 'category'])
            ->latest()
            ->limit(50)
            ->get();

        $activeUsers = User::where('updated_at', '>=', now()->subMinutes(15))->count();
        
        $data = [
            'recent_transactions' => $recentTransactions,
            'active_users' => $activeUsers,
            'timestamp' => now()->toISOString(),
        ];

        return response()->json($data);
    }

    /**
     * Show analytics page
     */
    public function analytics()
    {
        $data = [
            // User Engagement Metrics
            'daily_active_users' => $this->getDailyActiveUsers(),
            'weekly_active_users' => $this->getWeeklyActiveUsers(),
            'monthly_active_users' => $this->getMonthlyActiveUsers(),
            
            // Feature Usage Analytics
            'feature_usage_stats' => $this->getFeatureUsageStats(),
            
            // Revenue Analytics (if applicable)
            'transaction_volume_by_month' => $this->getTransactionVolumeByMonth(),
            
            // User Retention
            'user_retention' => $this->getUserRetentionData(),
            
            // Geographic Distribution (if you have location data)
            'user_distribution' => $this->getUserDistribution(),
        ];

        return inertia('Admin/Analytics', compact('data'));
    }

    /**
     * Show system health page
     */
    public function systemHealth()
    {
        $data = [
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
            'recent_errors' => $this->getRecentErrors(),
            'system_performance' => $this->getSystemPerformance(),
        ];

        return inertia('Admin/SystemHealth', compact('data'));
    }

    /**
     * Get user growth data for charts
     */
    private function getUserGrowthData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data[] = [
                'month' => $date->format('M Y'),
                'users' => $count
            ];
        }
        return $data;
    }

    /**
     * Get feature adoption data
     */
    private function getFeatureAdoptionData()
    {
        $totalUsers = User::count();
        
        return [
            'transactions' => [
                'users' => User::whereHas('transactions')->count(),
                'percentage' => $totalUsers > 0 ? round((User::whereHas('transactions')->count() / $totalUsers) * 100, 1) : 0
            ],
            'goals' => [
                'users' => User::whereHas('goals')->count(),
                'percentage' => $totalUsers > 0 ? round((User::whereHas('goals')->count() / $totalUsers) * 100, 1) : 0
            ],
            'loans' => [
                'users' => User::whereHas('loans')->count(),
                'percentage' => $totalUsers > 0 ? round((User::whereHas('loans')->count() / $totalUsers) * 100, 1) : 0
            ],
            'budget_config' => [
                'users' => User::whereHas('budgetConfig')->count(),
                'percentage' => $totalUsers > 0 ? round((User::whereHas('budgetConfig')->count() / $totalUsers) * 100, 1) : 0
            ]
        ];
    }

    /**
     * Get daily active users
     */
    private function getDailyActiveUsers()
    {
        return User::where('updated_at', '>=', now()->subDay())->count();
    }

    /**
     * Get weekly active users
     */
    private function getWeeklyActiveUsers()
    {
        return User::where('updated_at', '>=', now()->subWeek())->count();
    }

    /**
     * Get monthly active users
     */
    private function getMonthlyActiveUsers()
    {
        return User::where('updated_at', '>=', now()->subMonth())->count();
    }

    /**
     * Get feature usage statistics
     */
    private function getFeatureUsageStats()
    {
        return [
            'transactions_per_user' => User::whereHas('transactions')->withCount('transactions')->get()->avg('transactions_count'),
            'goals_per_user' => User::whereHas('goals')->withCount('goals')->get()->avg('goals_count'),
            'loans_per_user' => User::whereHas('loans')->withCount('loans')->get()->avg('loans_count'),
        ];
    }

    /**
     * Get transaction volume by month
     */
    private function getTransactionVolumeByMonth()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $amount = Transaction::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            
            $data[] = [
                'month' => $date->format('M Y'),
                'amount' => $amount
            ];
        }
        return $data;
    }

    /**
     * Get user retention data
     */
    private function getUserRetentionData()
    {
        // This is a simplified version - you might want to implement more sophisticated retention tracking
        $cohorts = [];
        for ($i = 6; $i >= 0; $i--) {
            $cohortDate = now()->subMonths($i)->startOfMonth();
            $cohortUsers = User::whereMonth('created_at', $cohortDate->month)
                ->whereYear('created_at', $cohortDate->year)
                ->get();
            
            $retained = $cohortUsers->filter(function ($user) use ($cohortDate) {
                return $user->updated_at > $cohortDate->addMonth();
            })->count();
            
            $cohorts[] = [
                'month' => $cohortDate->format('M Y'),
                'total' => $cohortUsers->count(),
                'retained' => $retained,
                'retention_rate' => $cohortUsers->count() > 0 ? round(($retained / $cohortUsers->count()) * 100, 1) : 0
            ];
        }
        return $cohorts;
    }

    /**
     * Get user distribution (simplified)
     */
    private function getUserDistribution()
    {
        // This would be more sophisticated with actual location data
        return [
            'total_users' => User::count(),
            'active_users' => User::where('updated_at', '>=', now()->subDays(30))->count(),
            'inactive_users' => User::where('updated_at', '<', now()->subDays(30))->count(),
        ];
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        try {
            $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ?", [config('database.connections.mysql.database')]);
            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get storage usage
     */
    private function getStorageUsage()
    {
        $storagePath = storage_path();
        $size = 0;
        
        if (is_dir($storagePath)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storagePath));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        
        return round($size / 1024 / 1024, 2); // Convert to MB
    }

    /**
     * Get recent errors (simplified)
     */
    private function getRecentErrors()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return [];
        }
        
        $lines = file($logFile);
        $recentErrors = [];
        
        foreach (array_reverse(array_slice($lines, -100)) as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'CRITICAL') !== false) {
                $recentErrors[] = $line;
                if (count($recentErrors) >= 10) break;
            }
        }
        
        return $recentErrors;
    }

    /**
     * Get system performance metrics
     */
    private function getSystemPerformance()
    {
        return [
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'execution_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4),
        ];
    }
}