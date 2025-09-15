<?php

namespace App\Services;

use App\Models\User;
use App\Services\Contracts\IncomeServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeService implements IncomeServiceInterface
{
    /**
     * Calculate total income for a user in a specific period.
     */
    public function calculateTotalIncome(User $user, Carbon $startDate, Carbon $endDate): float
    {
        return $user->incomeRecords()
            ->whereBetween('received_date', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Get income sources for a user.
     */
    public function getIncomeSources(User $user): array
    {
        return $user->incomeSources()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Get income records for a user in a specific period.
     */
    public function getIncomeRecords(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return $user->incomeRecords()
            ->with('incomeSource')
            ->whereBetween('received_date', [$startDate, $endDate])
            ->orderBy('received_date', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Calculate average monthly income.
     */
    public function calculateAverageMonthlyIncome(User $user, int $months = 6): float
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMonths($months);

        $totalIncome = $this->calculateTotalIncome($user, $startDate, $endDate);
        
        return $months > 0 ? $totalIncome / $months : 0;
    }

    /**
     * Get income trends for analytics.
     */
    public function getIncomeTrends(User $user, int $months = 12): array
    {
        $trends = [];
        $endDate = Carbon::now();

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = $endDate->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $endDate->copy()->subMonths($i)->endOfMonth();
            
            $monthlyIncome = $this->calculateTotalIncome($user, $monthStart, $monthEnd);
            
            $trends[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'income' => $monthlyIncome,
            ];
        }

        return $trends;
    }

    /**
     * Get income by source for a specific period.
     */
    public function getIncomeBySource(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return DB::table('income_records')
            ->join('income_sources', 'income_records.income_source_id', '=', 'income_sources.id')
            ->where('income_records.user_id', $user->id)
            ->whereBetween('income_records.received_date', [$startDate, $endDate])
            ->select('income_sources.name', 'income_sources.type', DB::raw('SUM(income_records.amount) as total'))
            ->groupBy('income_sources.id', 'income_sources.name', 'income_sources.type')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();
    }
}