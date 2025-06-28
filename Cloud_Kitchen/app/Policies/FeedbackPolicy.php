<?php

namespace App\Policies;

use App\Models\User;

class FeedbackPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    public function view(User $user)
    {
        return $user->isAdmin();
    }
    
    public function create(User $user)
    {
        return $user->isCustomer();
    }
    public function update(User $user): bool 
    { 
        return false; 
    }
    public function delete(User $user): bool 
    { 
        return false; 
    }

    public function process(User $user)
    {
        return $user->isManager();
    }
}
