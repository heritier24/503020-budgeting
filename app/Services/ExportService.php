<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\IncomeRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * Export user's transactions to CSV.
     */
    public function exportTransactionsToCsv(User $user, Carbon $startDate, Carbon $endDate): string
    {
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'transactions_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
        $filepath = 'exports/' . $filename;

        $csvData = [];
        $csvData[] = ['Date', 'Description', 'Category', 'Type', 'Amount', 'Currency', 'Notes'];

        foreach ($transactions as $transaction) {
            $csvData[] = [
                $transaction->transaction_date->format('Y-m-d'),
                $transaction->description,
                $transaction->category->name,
                ucfirst($transaction->type),
                $transaction->amount,
                $transaction->currency,
                $transaction->notes ?? '',
            ];
        }

        $this->writeCsvFile($filepath, $csvData);

        return $filepath;
    }

    /**
     * Export user's income records to CSV.
     */
    public function exportIncomeToCsv(User $user, Carbon $startDate, Carbon $endDate): string
    {
        $incomeRecords = $user->incomeRecords()
            ->with('incomeSource')
            ->whereBetween('received_date', [$startDate, $endDate])
            ->orderBy('received_date', 'desc')
            ->get();

        $filename = 'income_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
        $filepath = 'exports/' . $filename;

        $csvData = [];
        $csvData[] = ['Date', 'Source', 'Amount', 'Currency', 'Exchange Rate', 'Notes'];

        foreach ($incomeRecords as $record) {
            $csvData[] = [
                $record->received_date->format('Y-m-d'),
                $record->incomeSource->name,
                $record->amount,
                $record->currency,
                $record->exchange_rate,
                $record->notes ?? '',
            ];
        }

        $this->writeCsvFile($filepath, $csvData);

        return $filepath;
    }

    /**
     * Export comprehensive budget report to CSV.
     */
    public function exportBudgetReportToCsv(User $user, Carbon $startDate, Carbon $endDate): string
    {
        $filename = 'budget_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
        $filepath = 'exports/' . $filename;

        // Get budget configuration
        $budgetConfig = $user->budgetConfig;
        if (!$budgetConfig) {
            throw new \Exception('No budget configuration found for user.');
        }

        // Get transactions by category type
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $needsTransactions = $transactions->where('category.type', 'needs');
        $wantsTransactions = $transactions->where('category.type', 'wants');
        $savingsTransactions = $transactions->where('category.type', 'savings');

        // Calculate totals
        $totalIncome = $user->incomeRecords()
            ->whereBetween('received_date', [$startDate, $endDate])
            ->sum('amount');

        $needsSpent = $needsTransactions->sum('amount');
        $wantsSpent = $wantsTransactions->sum('amount');
        $savingsSpent = $savingsTransactions->sum('amount');

        $needsBudget = $totalIncome * ($budgetConfig->needs_percentage / 100);
        $wantsBudget = $totalIncome * ($budgetConfig->wants_percentage / 100);
        $savingsBudget = $totalIncome * ($budgetConfig->savings_percentage / 100);

        $csvData = [];
        
        // Header
        $csvData[] = ['BUDGET REPORT'];
        $csvData[] = ['Period:', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')];
        $csvData[] = ['Generated:', now()->format('Y-m-d H:i:s')];
        $csvData[] = []; // Empty row

        // Summary
        $csvData[] = ['SUMMARY'];
        $csvData[] = ['Total Income', '$' . number_format($totalIncome, 2)];
        $csvData[] = ['Total Expenses', '$' . number_format($needsSpent + $wantsSpent + $savingsSpent, 2)];
        $csvData[] = ['Remaining Budget', '$' . number_format($totalIncome - ($needsSpent + $wantsSpent + $savingsSpent), 2)];
        $csvData[] = []; // Empty row

        // Budget Allocation
        $csvData[] = ['BUDGET ALLOCATION'];
        $csvData[] = ['Category', 'Budgeted', 'Spent', 'Remaining', 'Percentage Used'];
        $csvData[] = [
            'Needs (' . $budgetConfig->needs_percentage . '%)',
            '$' . number_format($needsBudget, 2),
            '$' . number_format($needsSpent, 2),
            '$' . number_format($needsBudget - $needsSpent, 2),
            round(($needsSpent / $needsBudget) * 100, 1) . '%'
        ];
        $csvData[] = [
            'Wants (' . $budgetConfig->wants_percentage . '%)',
            '$' . number_format($wantsBudget, 2),
            '$' . number_format($wantsSpent, 2),
            '$' . number_format($wantsBudget - $wantsSpent, 2),
            round(($wantsSpent / $wantsBudget) * 100, 1) . '%'
        ];
        $csvData[] = [
            'Savings (' . $budgetConfig->savings_percentage . '%)',
            '$' . number_format($savingsBudget, 2),
            '$' . number_format($savingsSpent, 2),
            '$' . number_format($savingsBudget - $savingsSpent, 2),
            round(($savingsSpent / $savingsBudget) * 100, 1) . '%'
        ];
        $csvData[] = []; // Empty row

        // Detailed Transactions
        $csvData[] = ['DETAILED TRANSACTIONS'];
        $csvData[] = ['Date', 'Description', 'Category', 'Type', 'Amount', 'Notes'];

        foreach ($transactions as $transaction) {
            $csvData[] = [
                $transaction->transaction_date->format('Y-m-d'),
                $transaction->description,
                $transaction->category->name,
                ucfirst($transaction->type),
                '$' . number_format($transaction->amount, 2),
                $transaction->notes ?? ''
            ];
        }

        $this->writeCsvFile($filepath, $csvData);

        return $filepath;
    }

    /**
     * Write CSV data to file.
     */
    private function writeCsvFile(string $filepath, array $data): void
    {
        $content = '';
        
        foreach ($data as $row) {
            $content .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        Storage::disk('local')->put($filepath, $content);
    }

    /**
     * Get download URL for exported file.
     */
    public function getDownloadUrl(string $filepath): string
    {
        return Storage::disk('local')->url($filepath);
    }

    /**
     * Clean up old export files.
     */
    public function cleanupOldExports(int $daysOld = 7): int
    {
        $cutoffDate = now()->subDays($daysOld);
        $files = Storage::disk('local')->files('exports');
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);
            if ($lastModified < $cutoffDate->timestamp) {
                Storage::disk('local')->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}