<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalLine extends Model
{
    use HasFactory;

    protected $table = 'journal_lines';

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
        'description',
        'public_id',
    ];

    protected $casts = [
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
