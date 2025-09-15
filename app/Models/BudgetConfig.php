<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'needs_percentage',
        'wants_percentage',
        'savings_percentage',
        'calculation_method',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'needs_percentage' => 'decimal:2',
        'wants_percentage' => 'decimal:2',
        'savings_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the budget configuration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the total percentage (should always equal 100).
     */
    public function getTotalPercentageAttribute(): float
    {
        return $this->needs_percentage + $this->wants_percentage + $this->savings_percentage;
    }
}
