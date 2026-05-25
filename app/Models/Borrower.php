<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrower extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'department',
        'position',
        'contact_number',
        'email',
    ];

    /**
     * All borrowing transactions made by this borrower.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(BorrowingTransaction::class);
    }

    /**
     * Many-to-many relationship: equipment borrowed by this borrower through transactions.
     */
    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(
            Equipment::class,
            'borrowing_transactions',
            'borrower_id',
            'equipment_id'
        )->withPivot(['borrow_date', 'expected_return_date', 'actual_return_date', 'status'])
         ->withTimestamps();
    }
}
