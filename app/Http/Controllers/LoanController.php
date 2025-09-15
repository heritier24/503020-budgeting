<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $loans = Loan::where('user_id', $user->id)
            ->with('loanTransactions')
            ->orderBy('created_at', 'desc')
            ->get();

        // Computed attributes are automatically included via $appends in Loan model

        // Calculate total monthly payments
        $totalMonthlyPayments = $loans->where('status', 'active')->sum('monthly_payment');
        $totalDebt = $loans->where('status', 'active')->sum('current_balance');

        return Inertia::render('Loans/Index', [
            'data' => [
                'user' => $user,
                'loans' => $loans,
                'totalMonthlyPayments' => $totalMonthlyPayments,
                'totalDebt' => $totalDebt,
            ]
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        
        return Inertia::render('Loans/Create', [
            'data' => [
                'user' => $user,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lender_name' => 'required|string|max:255',
            'lender_type' => 'required|in:bank,personal,credit_card,business,other',
            'loan_type' => 'required|in:personal,business,education,mortgage,auto,other',
            'original_amount' => 'required|numeric|min:0',
            'current_balance' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'monthly_payment' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:weekly,bi-weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        $loan = Loan::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'lender_name' => $request->lender_name,
            'lender_type' => $request->lender_type,
            'loan_type' => $request->loan_type,
            'original_amount' => $request->original_amount,
            'current_balance' => $request->current_balance,
            'interest_rate' => $request->interest_rate,
            'monthly_payment' => $request->monthly_payment,
            'payment_frequency' => $request->payment_frequency,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        return redirect()->route('loans.index')->with('success', 'Loan created successfully!');
    }

    public function show(Loan $loan)
    {
        $user = Auth::user();
        
        // Ensure user owns this loan
        if ($loan->user_id !== $user->id) {
            abort(403);
        }

        $loan->load('loanTransactions');
        
        // Computed attributes are automatically included via $appends in Loan model
        
        return Inertia::render('Loans/Show', [
            'data' => [
                'user' => $user,
                'loan' => $loan,
            ]
        ]);
    }

    public function edit(Loan $loan)
    {
        $user = Auth::user();
        
        // Ensure user owns this loan
        if ($loan->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Loans/Edit', [
            'data' => [
                'user' => $user,
                'loan' => $loan,
            ]
        ]);
    }

    public function payment(Loan $loan)
    {
        $user = Auth::user();
        
        // Ensure user owns this loan
        if ($loan->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Loans/Payment', [
            'data' => [
                'user' => $user,
                'loan' => $loan,
            ]
        ]);
    }

    public function update(Request $request, Loan $loan)
    {
        $user = Auth::user();
        
        // Ensure user owns this loan
        if ($loan->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'lender_name' => 'required|string|max:255',
            'lender_type' => 'required|in:bank,personal,credit_card,business,other',
            'loan_type' => 'required|in:personal,business,education,mortgage,auto,other',
            'original_amount' => 'required|numeric|min:0',
            'current_balance' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'monthly_payment' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:weekly,bi-weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,paid_off,defaulted,paused',
            'notes' => 'nullable|string',
        ]);

        $loan->update($request->all());

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully!');
    }

    public function destroy(Loan $loan)
    {
        $user = Auth::user();
        
        // Ensure user owns this loan
        if ($loan->user_id !== $user->id) {
            abort(403);
        }

        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully!');
    }

    public function recordPayment(Request $request, Loan $loan)
    {
        $user = Auth::user();
        
        // Ensure user owns this loan
        if ($loan->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'principal_amount' => 'nullable|numeric|min:0',
            'interest_amount' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Create loan transaction
        $loanTransaction = LoanTransaction::create([
            'loan_id' => $loan->id,
            'user_id' => $user->id,
            'type' => 'payment',
            'amount' => $request->amount,
            'principal_amount' => $request->principal_amount,
            'interest_amount' => $request->interest_amount,
            'transaction_date' => $request->transaction_date,
            'notes' => $request->notes,
        ]);

        // Create regular transaction for loan payment
        $category = Category::where('user_id', $user->id)
            ->where('name', 'Loan Payment')
            ->where('type', 'needs')
            ->first();

        // If no loan payment category exists, create one
        if (!$category) {
            $category = Category::create([
                'user_id' => $user->id,
                'name' => 'Loan Payment',
                'type' => 'needs',
                'color' => '#ef4444',
                'icon' => '🏦',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 100,
            ]);
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => $request->amount,
            'description' => "Loan Payment - {$loan->name}",
            'transaction_date' => $request->transaction_date,
            'notes' => $request->notes ? "Loan Payment: {$request->notes}" : "Loan Payment to {$loan->lender_name}",
            'currency' => 'RWF',
        ]);

        // Update loan balance
        $principalPaid = $request->principal_amount ?? $request->amount;
        $loan->current_balance = max(0, $loan->current_balance - $principalPaid);
        
        // Mark as paid off if balance is zero
        if ($loan->current_balance <= 0) {
            $loan->status = 'paid_off';
        }
        
        $loan->save();

        return redirect()->route('loans.show', $loan)->with('success', 'Payment recorded successfully!');
    }
}
