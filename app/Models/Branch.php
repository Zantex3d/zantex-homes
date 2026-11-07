<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id', 'company_id', 'code', 'name', 'address', 'timezone',
        'status', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'public_id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function scopeWhereCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
