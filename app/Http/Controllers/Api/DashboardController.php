<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\AnalyticsServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private AnalyticsServiceInterface $analyticsService
    ) {}

    /**
     * Get dashboard overview data.
     */
    public function overview(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $this->analyticsService->generateDashboardData($user);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get spending trends for a specific period.
     */
    public function spendingTrends(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $trends = $this->analyticsService->getSpendingTrends($user, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $trends,
        ]);
    }

    /**
     * Get category breakdown for spending.
     */
    public function categoryBreakdown(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $breakdown = $this->analyticsService->getCategoryBreakdown($user, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $breakdown,
        ]);
    }

    /**
     * Get budget performance metrics.
     */
    public function budgetPerformance(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $performance = $this->analyticsService->getBudgetPerformance($user, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }
}
