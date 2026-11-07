<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'company_id',
        'property_id',
        'unit_number',
        'unit_type',
        'bedrooms',
        'bathrooms',
        'public_id',
        'status',
    ];

    protected $casts = [
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
