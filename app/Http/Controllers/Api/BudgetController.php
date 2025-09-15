<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetConfig;
use App\Services\Contracts\BudgetCalculationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct(
        private BudgetCalculationServiceInterface $budgetService
    ) {}

    /**
     * Get budget configuration for the authenticated user.
     */
    public function config(Request $request): JsonResponse
    {
        $user = $request->user();
        $config = $user->budgetConfig;

        if (!$config) {
            // Create default configuration if none exists
            $config = BudgetConfig::create([
                'user_id' => $user->id,
                'needs_percentage' => 50.00,
                'wants_percentage' => 30.00,
                'savings_percentage' => 20.00,
                'calculation_method' => 'adaptive',
                'currency' => 'USD',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $config,
        ]);
    }

    /**
     * Create or update budget configuration.
     */
    public function storeConfig(Request $request): JsonResponse
    {
        $request->validate([
            'needs_percentage' => 'required|numeric|min:0|max:100',
            'wants_percentage' => 'required|numeric|min:0|max:100',
            'savings_percentage' => 'required|numeric|min:0|max:100',
            'calculation_method' => 'required|in:fixed,variable,adaptive',
            'currency' => 'required|string|size:3',
        ]);

        // Validate that percentages add up to 100
        $total = $request->needs_percentage + $request->wants_percentage + $request->savings_percentage;
        if (abs($total - 100) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'The percentages must add up to exactly 100%.',
            ], 422);
        }

        $user = $request->user();
        $config = $user->budgetConfig;

        if ($config) {
            $config->update($request->only([
                'needs_percentage', 'wants_percentage', 'savings_percentage',
                'calculation_method', 'currency'
            ]));
        } else {
            $config = BudgetConfig::create([
                'user_id' => $user->id,
                'needs_percentage' => $request->needs_percentage,
                'wants_percentage' => $request->wants_percentage,
                'savings_percentage' => $request->savings_percentage,
                'calculation_method' => $request->calculation_method,
                'currency' => $request->currency,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $config,
            'message' => 'Budget configuration saved successfully.',
        ]);
    }

    /**
     * Update budget configuration.
     */
    public function updateConfig(Request $request, BudgetConfig $budgetConfig): JsonResponse
    {
        $this->authorize('update', $budgetConfig);

        $request->validate([
            'needs_percentage' => 'required|numeric|min:0|max:100',
            'wants_percentage' => 'required|numeric|min:0|max:100',
            'savings_percentage' => 'required|numeric|min:0|max:100',
            'calculation_method' => 'required|in:fixed,variable,adaptive',
            'currency' => 'required|string|size:3',
        ]);

        // Validate that percentages add up to 100
        $total = $request->needs_percentage + $request->wants_percentage + $request->savings_percentage;
        if (abs($total - 100) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'The percentages must add up to exactly 100%.',
            ], 422);
        }

        $budgetConfig->update($request->only([
            'needs_percentage', 'wants_percentage', 'savings_percentage',
            'calculation_method', 'currency'
        ]));

        return response()->json([
            'success' => true,
            'data' => $budgetConfig,
            'message' => 'Budget configuration updated successfully.',
        ]);
    }

    /**
     * Get budget summary for a specific period.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $summary = $this->budgetService->getBudgetSummary($user, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Get remaining budget for each category.
     */
    public function remaining(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $remaining = $this->budgetService->calculateRemainingBudget($user, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $remaining,
        ]);
    }
}
