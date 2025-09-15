<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'income_source_id',
        'amount',
        'currency',
        'exchange_rate',
        'received_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'received_date' => 'date',
    ];

    /**
     * Get the user that owns the income record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the income source that owns the income record.
     */
    public function incomeSource(): BelongsTo
    {
        return $this->belongsTo(IncomeSource::class);
    }
}
