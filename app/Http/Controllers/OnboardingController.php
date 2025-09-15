<?php

namespace App\Http\Controllers;

use App\Models\BudgetConfig;
use App\Models\IncomeSource;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding page
     */
    public function index()
    {
        return Inertia::render('Onboarding');
    }

    /**
     * Complete the onboarding process
     */
    public function complete(Request $request)
    {
        $request->validate([
            'budget_config' => 'required|array',
            'budget_config.needs' => 'required|numeric|min:0|max:100',
            'budget_config.wants' => 'required|numeric|min:0|max:100',
            'budget_config.savings' => 'required|numeric|min:0|max:100',
            'income_sources' => 'required|array|min:1',
            'income_sources.*.name' => 'required|string|max:255',
            'income_sources.*.type' => 'required|string|in:salary,freelance,business,investment,other',
            'income_sources.*.expected_monthly' => 'required|numeric|min:0',
            'first_transaction' => 'nullable|array',
            'first_transaction.description' => 'nullable|string|max:255',
            'first_transaction.amount' => 'nullable|numeric',
            'first_transaction.type' => 'nullable|string|in:income,expense',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Step 1: Save budget configuration
            $budgetConfig = BudgetConfig::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'needs_percentage' => $request->budget_config['needs'],
                    'wants_percentage' => $request->budget_config['wants'],
                    'savings_percentage' => $request->budget_config['savings'],
                    'currency' => 'RWF',
                    'calculation_method' => 'adaptive'
                ]
            );

            // Step 2: Save income sources
            foreach ($request->income_sources as $sourceData) {
                if (!empty($sourceData['name']) && !empty($sourceData['expected_monthly'])) {
                    IncomeSource::create([
                        'user_id' => $user->id,
                        'name' => $sourceData['name'],
                        'type' => $sourceData['type'],
                        'expected_monthly' => $sourceData['expected_monthly'],
                        'currency' => 'RWF',
                        'is_active' => true
                    ]);
                }
            }

            // Step 3: Save first transaction if provided
            if (!empty($request->first_transaction['description']) && !empty($request->first_transaction['amount'])) {
                // Get the first available category
                $category = Category::where('user_id', $user->id)
                    ->orWhereNull('user_id') // Default categories
                    ->first();

                Transaction::create([
                    'user_id' => $user->id,
                    'description' => $request->first_transaction['description'],
                    'amount' => $request->first_transaction['amount'],
                    'type' => $request->first_transaction['type'] ?? 'expense',
                    'category_id' => $category ? $category->id : 1,
                    'currency' => 'RWF',
                    'transaction_date' => now()->toDateString(),
                    'notes' => 'First transaction from onboarding'
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Onboarding completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'setup' => 'Failed to complete setup: ' . $e->getMessage()
            ]);
        }
    }
}