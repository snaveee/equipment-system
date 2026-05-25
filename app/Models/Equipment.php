<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'description',
        'category',
        'serial_number',
        'condition',
        'status',
        'photo_path',
    ];

    /**
     * All borrowing transactions referencing this equipment.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(BorrowingTransaction::class);
    }

    /**
     * Currently active borrowing transaction (not yet returned).
     */
    public function activeTransaction()
    {
        return $this->hasOne(BorrowingTransaction::class)->whereIn('status', ['active', 'overdue']);
    }
}
