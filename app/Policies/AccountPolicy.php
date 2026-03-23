<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
class AccountPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Account $account): bool
    {
        $child = $account->child;
        $isFamilyMatch = $user->family_id !== null && $child?->family_id === $user->family_id;
        $isLegacyOwner = $user->family_id === null && $child?->parent_id === $user->id;

        return $account->child_user_id === $user->id
            || ($user->isParent() && ($isFamilyMatch || $isLegacyOwner));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isParent();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Account $account): bool
    {
        $child = $account->child;
        $isFamilyMatch = $user->family_id !== null && $child?->family_id === $user->family_id;
        $isLegacyOwner = $user->family_id === null && $child?->parent_id === $user->id;

        return $user->isParent()
            && ($isFamilyMatch || $isLegacyOwner);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Account $account): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Account $account): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Account $account): bool
    {
        return false;
    }
}
