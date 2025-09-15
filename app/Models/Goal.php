<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
        'target_amount',
        'current_amount',
        'total_contributions',
        'contribution_notes',
        'target_date',
        'start_date',
        'status',
        'priority',
        'color',
        'icon',
        'notes'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'total_contributions' => 'decimal:2',
        'target_date' => 'date',
        'start_date' => 'date',
    ];

    protected $appends = [
        'progress_percentage',
        'days_remaining',
        'days_elapsed',
        'is_overdue',
        'monthly_target',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class);
    }

    // Accessors
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        
        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    public function getDaysRemainingAttribute(): int
    {
        $targetDate = $this->target_date;
        $today = now()->startOfDay();
        
        if ($targetDate < $today) {
            return 0; // Goal is overdue
        }
        
        return $today->diffInDays($targetDate);
    }

    public function getDaysElapsedAttribute(): int
    {
        $startDate = $this->start_date;
        $today = now()->startOfDay();
        
        if ($startDate > $today) {
            return 0; // Goal hasn't started yet
        }
        
        return $startDate->diffInDays($today);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->target_date < now()->startOfDay() && $this->status === 'active';
    }

    public function getMonthlyTargetAttribute(): float
    {
        $totalDays = $this->start_date->diffInDays($this->target_date);
        
        if ($totalDays <= 0) {
            return 0;
        }
        
        $remainingAmount = $this->target_amount - $this->current_amount;
        $daysPerMonth = 30.44; // Average days per month
        $monthsRemaining = max(1, $totalDays / $daysPerMonth);
        
        return $remainingAmount / $monthsRemaining;
    }
}
