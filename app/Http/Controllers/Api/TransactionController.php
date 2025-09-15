<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Get all transactions for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = $user->transactions()->with('category');

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->inDateRange($request->start_date, $request->end_date);
        }

        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Create a new transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'type' => 'required|in:expense,income',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $user = $request->user();
        
        // Verify the category belongs to the user or is default
        $category = \App\Models\Category::where('id', $request->category_id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('is_default', true);
            })
            ->firstOrFail();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'exchange_rate' => $request->exchange_rate ?? 1.0,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'type' => $request->type,
            'notes' => $request->notes,
            'metadata' => $request->metadata,
        ]);

        return response()->json([
            'success' => true,
            'data' => $transaction->load('category'),
            'message' => 'Transaction created successfully.',
        ], 201);
    }

    /**
     * Get a specific transaction.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $this->authorize('view', $transaction);

        return response()->json([
            'success' => true,
            'data' => $transaction->load('category'),
        ]);
    }

    /**
     * Update a transaction.
     */
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $this->authorize('update', $transaction);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'type' => 'required|in:expense,income',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $transaction->update($request->only([
            'category_id', 'amount', 'currency', 'exchange_rate',
            'description', 'transaction_date', 'type', 'notes', 'metadata'
        ]));

        return response()->json([
            'success' => true,
            'data' => $transaction->load('category'),
            'message' => 'Transaction updated successfully.',
        ]);
    }

    /**
     * Delete a transaction.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);
        
        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully.',
        ]);
    }

    /**
     * Bulk create transactions.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $request->validate([
            'transactions' => 'required|array|min:1|max:100',
            'transactions.*.category_id' => 'required|exists:categories,id',
            'transactions.*.amount' => 'required|numeric|min:0.01',
            'transactions.*.currency' => 'required|string|size:3',
            'transactions.*.description' => 'required|string|max:255',
            'transactions.*.transaction_date' => 'required|date',
            'transactions.*.type' => 'required|in:expense,income',
        ]);

        $user = $request->user();
        $createdTransactions = [];

        DB::transaction(function () use ($request, $user, &$createdTransactions) {
            foreach ($request->transactions as $transactionData) {
                // Verify the category belongs to the user or is default
                $category = \App\Models\Category::where('id', $transactionData['category_id'])
                    ->where(function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhere('is_default', true);
                    })
                    ->firstOrFail();

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $transactionData['category_id'],
                    'amount' => $transactionData['amount'],
                    'currency' => $transactionData['currency'],
                    'exchange_rate' => $transactionData['exchange_rate'] ?? 1.0,
                    'description' => $transactionData['description'],
                    'transaction_date' => $transactionData['transaction_date'],
                    'type' => $transactionData['type'],
                    'notes' => $transactionData['notes'] ?? null,
                    'metadata' => $transactionData['metadata'] ?? null,
                ]);

                $createdTransactions[] = $transaction->load('category');
            }
        });

        return response()->json([
            'success' => true,
            'data' => $createdTransactions,
            'message' => count($createdTransactions) . ' transactions created successfully.',
        ], 201);
    }

    /**
     * Export transactions to CSV.
     */
    public function exportCsv(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = $user->transactions()->with('category');

        // Apply filters
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->inDateRange($request->start_date, $request->end_date);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

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

        $filename = 'transactions_' . now()->format('Y-m-d') . '.csv';
        
        return response()->json([
            'success' => true,
            'data' => [
                'filename' => $filename,
                'csv_data' => $csvData,
                'total_transactions' => count($transactions),
            ],
            'message' => 'CSV data generated successfully.',
        ]);
    }
}
