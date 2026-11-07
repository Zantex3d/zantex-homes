<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Property;
use Illuminate\Auth\Access\Response;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_properties');
    }

    public function view(User $user, Property $property): bool
    {
        return $user->preferred_company_id === $property->company_id 
            && $user->hasPermission('view_properties');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_property');
    }

    public function update(User $user, Property $property): bool
    {
        return $user->preferred_company_id === $property->company_id
            && $user->hasPermission('edit_property');
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->preferred_company_id === $property->company_id
            && $user->hasPermission('delete_property');
    }
}
