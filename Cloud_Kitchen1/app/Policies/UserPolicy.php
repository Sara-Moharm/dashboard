<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     * Only admins can view other users' information.
     */
    public function view(User $user, User $target): bool
    {
        return ($user->isSuperAdmin()&& !$target->isSuperAdmin())  || 
               $user->isAdmin() && !$target->isManager();
    }

    /**
     * Determine whether the user can view the model.
     * Only the user can view their own profile.
     */
    public function viewProfile(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    /**
     * Determine whether the user can create admin users.
     * Only a super admin can create an account for an admin.
     */
     public function createAdmin(User $user): bool 
     { 
        return $user->isSuperAdmin(); 
    }

    /**
     * Determine whether the user can create operational staff users.
     * Only admins can create an account for an operational staff. 
     * Operational staff can't create an account for themselves.
     */
    public function createOperationalStaff(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Determine whether the user can update their own profile.
     * Everyone can update their own profile.
     */
    public function updateProfile(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    /**
     * Determine whether the user can update other users.
     * Only admins can update other non-admin users' information.
     */
    public function updateUser(User $user, User $model): bool
    {
        return $user->isSuperAdmin() && $user->id !== $model->isSuperAdmin() ||
        $model->isAdmin()&& !$model->isManager();
    }

    /**
     * Determine whether the user can delete the model.
     * Customers can delete their own accounts. 
     * Admins can delete other non-admin users' accounts.
     */
    public function toggleActivationStatus(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && !$target->isSuperAdmin() ||
        $user->isAdmin() && !$target->isManager();
    }

    /**
     * Determine whether the user can soft delete the model.
     */
    public function delete(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && !$target->isCustomer() && !$target->isSuperAdmin() ||
        $user->isCustomer() && $user->id === $target->id;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && !$target->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */ 
    public function forceDelete(User $user, User $target): bool
    {
        return $user->isSuperAdmin() && !$target->isSuperAdmin();
    }

}
