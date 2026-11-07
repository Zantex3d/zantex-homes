<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'legal_name',
        'trading_name',
        'registration_no',
        'tax_id',
        'base_currency',
        'timezone',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'public_id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'company_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_scopes')
            ->withPivot(['branch_id', 'estate_id', 'role_id'])
            ->withTimestamps();
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function userScopes(): HasMany
    {
        return $this->hasMany(UserScope::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CompanySetting::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTimezone()
    {
        return $this->timezone ?? config('app.timezone');
    }

    public function getBaseCurrency()
    {
        return $this->base_currency ?? 'NGN';
    }
}
