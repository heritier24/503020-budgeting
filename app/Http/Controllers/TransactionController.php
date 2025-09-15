<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->transactions()->with('category');
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $categories = Category::where('user_id', $user->id)
            ->orWhereNull('user_id')
            ->get();
        
        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'filters' => $request->only(['type', 'category_id', 'date_from', 'date_to'])
        ]);
    }

    /**
     * Show the form for creating a new transaction
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Get categories for the user
        $categories = Category::where('user_id', $user->id)
            ->orWhereNull('user_id')
            ->get();

        return Inertia::render('Transactions/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created transaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Verify category belongs to user or is a default category
        $category = Category::where('id', $request->category_id)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereNull('user_id');
            })
            ->firstOrFail();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'currency' => 'RWF',
            'transaction_date' => $request->transaction_date,
            'notes' => $request->notes,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'transaction' => $transaction->load('category')
            ]);
        }

        return redirect()->back()->with('success', 'Transaction created successfully');
    }

    /**
     * Update the specified transaction
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Ensure transaction belongs to user
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Verify category belongs to user or is a default category
        $category = Category::where('id', $request->category_id)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereNull('user_id');
            })
            ->firstOrFail();

        $transaction->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'transaction_date' => $request->transaction_date,
            'notes' => $request->notes,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'transaction' => $transaction->load('category')
            ]);
        }

        return redirect()->back()->with('success', 'Transaction updated successfully');
    }

    /**
     * Remove the specified transaction
     */
    public function destroy(Transaction $transaction)
    {
        // Ensure transaction belongs to user
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Transaction deleted successfully');
    }

    /**
     * Get transaction statistics
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        
        $currentMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');
        
        // Current month statistics
        $currentMonthTransactions = $user->transactions()
            ->where('transaction_date', 'like', $currentMonth . '%')
            ->get();
        
        $currentMonthIncome = $currentMonthTransactions->where('type', 'income')->sum('amount');
        $currentMonthExpenses = $currentMonthTransactions->where('type', 'expense')->sum('amount');
        
        // Last month statistics
        $lastMonthTransactions = $user->transactions()
            ->where('transaction_date', 'like', $lastMonth . '%')
            ->get();
        
        $lastMonthIncome = $lastMonthTransactions->where('type', 'income')->sum('amount');
        $lastMonthExpenses = $lastMonthTransactions->where('type', 'expense')->sum('amount');
        
        // Category breakdown
        $categoryBreakdown = $user->transactions()
            ->where('transaction_date', 'like', $currentMonth . '%')
            ->where('type', 'expense')
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($transactions) {
                return [
                    'name' => $transactions->first()->category->name,
                    'amount' => $transactions->sum('amount'),
                    'count' => $transactions->count()
                ];
            })
            ->sortByDesc('amount')
            ->values();
        
        return response()->json([
            'current_month' => [
                'income' => $currentMonthIncome,
                'expenses' => $currentMonthExpenses,
                'net' => $currentMonthIncome - $currentMonthExpenses
            ],
            'last_month' => [
                'income' => $lastMonthIncome,
                'expenses' => $lastMonthExpenses,
                'net' => $lastMonthIncome - $lastMonthExpenses
            ],
            'category_breakdown' => $categoryBreakdown
        ]);
    }
}