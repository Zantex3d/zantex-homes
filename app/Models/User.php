<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'public_id',
        'name',
        'email',
        'phone',
        'password',
        'email_verified_at',
        'phone_verified_at',
        'mfa_enabled',
        'mfa_secret',
        'preferred_company_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'mfa_enabled' => 'boolean',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_scopes')
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

    public function preferredCompany()
    {
        return $this->belongsTo(Company::class, 'preferred_company_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function getActiveCompany()
    {
        return $this->preferredCompany ?? $this->companies()->first();
    }

    public function hasRole($role, $companyId = null)
    {
        if (!$companyId) {
            $companyId = $this->getActiveCompany()?->id;
        }

        return $this->userRoles()
            ->whereHas('role', fn($q) => $q->where('name', $role))
            ->where('company_id', $companyId)
            ->exists();
    }

    public function hasPermission($permission, $companyId = null)
    {
        if (!$companyId) {
            $companyId = $this->getActiveCompany()?->id;
        }

        return $this->userRoles()
            ->whereHas('role.permissions', fn($q) => $q->where('name', $permission))
            ->where('company_id', $companyId)
            ->exists();
    }
}
