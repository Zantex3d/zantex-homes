<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'company_id',
        'estate_id',
        'property_code',
        'name',
        'property_type_id',
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

    public function estate(): BelongsTo
    {
        return $this->belongsTo(Estate::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
