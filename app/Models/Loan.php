<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'lender_name',
        'lender_type',
        'loan_type',
        'original_amount',
        'current_balance',
        'interest_rate',
        'monthly_payment',
        'payment_frequency',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = [
        'total_paid',
        'progress_percentage',
        'remaining_payments',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanTransactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

    // Accessors
    public function getTotalPaidAttribute(): float
    {
        return $this->loanTransactions()
            ->where('type', 'payment')
            ->sum('amount');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->original_amount <= 0) {
            return 0;
        }
        
        $paid = $this->original_amount - $this->current_balance;
        return ($paid / $this->original_amount) * 100;
    }

    public function getRemainingPaymentsAttribute(): int
    {
        if ($this->monthly_payment <= 0) {
            return 0;
        }
        
        return ceil($this->current_balance / $this->monthly_payment);
    }
}
