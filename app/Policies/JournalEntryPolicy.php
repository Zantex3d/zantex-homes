<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JournalEntry;

class JournalEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_accounting');
    }

    public function view(User $user, JournalEntry $entry): bool
    {
        return $user->preferred_company_id === $entry->company_id
            && $user->hasPermission('view_accounting');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_journal');
    }

    public function update(User $user, JournalEntry $entry): bool
    {
        return $user->preferred_company_id === $entry->company_id
            && ($entry->status === 'draft' && $user->hasPermission('create_journal') 
                || $entry->status === 'posted' && $user->hasPermission('reverse_journal'));
    }

    public function delete(User $user, JournalEntry $entry): bool
    {
        return $user->preferred_company_id === $entry->company_id
            && $entry->status === 'draft'
            && $user->hasPermission('create_journal');
    }
}
