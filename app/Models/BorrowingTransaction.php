<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'equipment_id',
        'purpose',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'return_condition',
        'damage_remarks',
        'follow_up_actions',
        'status',
        'processed_by',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Days overdue (0 if not yet overdue or already returned on time).
     */
    public function getDaysOverdueAttribute(): int
    {
        if ($this->actual_return_date) {
            return 0;
        }
        $today = now()->startOfDay();
        if ($today->lessThanOrEqualTo($this->expected_return_date)) {
            return 0;
        }
        return $this->expected_return_date->diffInDays($today);
    }

    /**
     * Query scope: only overdue active transactions.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('actual_return_date')
            ->whereDate('expected_return_date', '<', now()->toDateString());
    }
}
