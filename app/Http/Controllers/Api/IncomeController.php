<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncomeRecord;
use App\Models\IncomeSource;
use App\Services\Contracts\IncomeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function __construct(
        private IncomeServiceInterface $incomeService
    ) {}

    /**
     * Get all income sources for the authenticated user.
     */
    public function sources(Request $request): JsonResponse
    {
        $user = $request->user();
        $sources = $this->incomeService->getIncomeSources($user);

        return response()->json([
            'success' => true,
            'data' => $sources,
        ]);
    }

    /**
     * Create a new income source.
     */
    public function storeSource(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:salary,freelance,business,investment,other',
            'expected_monthly' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
        ]);

        $user = $request->user();
        $source = IncomeSource::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'expected_monthly' => $request->expected_monthly,
            'currency' => $request->currency,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'data' => $source,
            'message' => 'Income source created successfully.',
        ], 201);
    }

    /**
     * Update an income source.
     */
    public function updateSource(Request $request, IncomeSource $incomeSource): JsonResponse
    {
        $this->authorize('update', $incomeSource);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:salary,freelance,business,investment,other',
            'expected_monthly' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $incomeSource->update($request->only([
            'name', 'type', 'expected_monthly', 'currency', 'description', 'is_active'
        ]));

        return response()->json([
            'success' => true,
            'data' => $incomeSource,
            'message' => 'Income source updated successfully.',
        ]);
    }

    /**
     * Delete an income source.
     */
    public function destroySource(IncomeSource $incomeSource): JsonResponse
    {
        $this->authorize('delete', $incomeSource);
        
        $incomeSource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Income source deleted successfully.',
        ]);
    }

    /**
     * Get income records for a specific period.
     */
    public function records(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $records = $this->incomeService->getIncomeRecords($user, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $records,
        ]);
    }

    /**
     * Create a new income record.
     */
    public function storeRecord(Request $request): JsonResponse
    {
        $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        
        // Verify the income source belongs to the user
        $incomeSource = IncomeSource::where('id', $request->income_source_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $record = IncomeRecord::create([
            'user_id' => $user->id,
            'income_source_id' => $request->income_source_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'exchange_rate' => $request->exchange_rate ?? 1.0,
            'received_date' => $request->received_date,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'data' => $record->load('incomeSource'),
            'message' => 'Income record created successfully.',
        ], 201);
    }

    /**
     * Update an income record.
     */
    public function updateRecord(Request $request, IncomeRecord $incomeRecord): JsonResponse
    {
        $this->authorize('update', $incomeRecord);

        $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $incomeRecord->update($request->only([
            'income_source_id', 'amount', 'currency', 'exchange_rate', 'received_date', 'notes'
        ]));

        return response()->json([
            'success' => true,
            'data' => $incomeRecord->load('incomeSource'),
            'message' => 'Income record updated successfully.',
        ]);
    }

    /**
     * Delete an income record.
     */
    public function destroyRecord(IncomeRecord $incomeRecord): JsonResponse
    {
        $this->authorize('delete', $incomeRecord);
        
        $incomeRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Income record deleted successfully.',
        ]);
    }

    /**
     * Get income analytics.
     */
    public function analytics(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $analytics = [
            'average_monthly_income' => $this->incomeService->calculateAverageMonthlyIncome($user, 6),
            'income_trends' => $this->incomeService->getIncomeTrends($user, 12),
        ];

        if ($request->has(['start_date', 'end_date'])) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);

            $analytics['period_income'] = $this->incomeService->calculateTotalIncome($user, $startDate, $endDate);
            $analytics['income_by_source'] = $this->incomeService->getIncomeBySource($user, $startDate, $endDate);
        }

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }
}
