<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'company_id',
        'account_code',
        'account_name',
        'account_type',
        'normal_balance',
        'public_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
