<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentCommitment extends Model
{
    use HasFactory;

    protected $table = 'investment_commitments';

    protected $fillable = [
        'public_id', 'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
