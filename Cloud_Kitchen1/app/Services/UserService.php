<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class UserService
{
    

    /**
     * Get the profile of a user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function getProfile(int $id)
    {
        return User::where('id', $id)->with('customer', 'staff')->first();
    }

    public function getAll(?array $roles, ?bool $is_active = null)
    {
        if($roles == null){
            $roles = ['admin', 'customer', 'kitchen_staff', 'delivery'];
        }

        if($is_active == null){
            return User::whereIn('role', $roles)->get()->with('customer', 'staff');
        }
        else{
            return User::whereIn('role', $roles)->where('is_active', $is_active)->get()->with('customer', 'staff');
        }
    }

    /**
     * Get a user by role and ID
     *
     * @param string $role
     * @param int $id
     * @return User
     */
    public function getById(string $role , int $id)
    {
        return User::where('role', $role)->with('customer', 'staff')->findOrFail($id);
    }


    /**
     * Format the get profile response of the currently authenticated user based on their role.
     *
     * @return array
     */
    function formatUserProfileResponse($userProfile): array {
        $response = [
            'fname' => $userProfile->fname,
            'lname' => $userProfile->lname,
            'email' => $userProfile->email,
            'phone_number' => $userProfile->phone_number,
        ];
    
        return match (true) {
            $userProfile->isCustomer() => array_merge($response, [
                'second_phone_number' => $userProfile->customer->second_phone_number,
                'address' => $userProfile->customer->address,
            ]),
            $userProfile->isOperationalStaff() => array_merge($response, [
                'shift_start' => $userProfile->staff->shift_start,
                'shift_end' => $userProfile->staff->shift_end,
            ]),
            default => $response,
        };
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param array $data
     * @return User
     */
    public function updateOwnProfile(array $data)
    {
        
        $user = User::where('id', Auth::user()->id)->with('customer', 'staff')->first();

    
    DB::transaction(function () use ($user, $data) {
        // Update user table
        $user->update([
            'fname' => $data['fname'] ?? $user->fname,
            'lname' => $data['lname'] ?? $user->lname,
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
        ]);

        // Update customer table (if relationship exists)
        if ($user->customer) {
            $user->customer->update([
                'second_phone_number' => $data['second_phone_number'] ?? $user->customer->second_phone_number,
                'address' => $data['address'] ?? $user->customer->address,
            ]);
        }
        
    });

    return $user->fresh();
    }

    /**
     * Update a user by admin
     *
     * @param array $data
     * @return User
     */
    public function updateUserByAdmin(array $data)
    {
        DB::beginTransaction();
        try {
            $user = User::with('customer', 'staff')->findOrFail($data['id']);
            
            $user->update([
                'fname' => $data['fname'] ?? $user->fname,
                'lname' => $data['lname'] ?? $user->lname,
                'phone_number' => $data['phone_number'] ?? $user->phone_number,
            ]);

            if ($user->staff) {
                $user->staff->update([
                    'shift_start' => $data['shift_start'] ?? $user->staff->shift_start,
                    'shift_end' => $data['shift_end'] ?? $user->staff->shift_end,
                    'status' => $data['status'] ?? $user->staff->status
                ]);
            }
            
            if ($user->customer) {
                $user->customer->update([
                    'address' => $data['address'] ?? $user->customer->address,
                    'second_phone_number' => $data['second_phone_number'] ?? $user->customer->second_phone_number
                ]);
            }
            
            DB::commit();
            return $user->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Toggle the activation status of a user
     *
     * @param int $id
     * @return User
     * @throws ModelNotFoundException
     */
    public function toggleActivationStatus (int $id)
    {$user = User::find($id);
        if(!$user) {
            throw new ModelNotFoundException('User not found');
        }
            $user->is_active = !$user->is_active; 
            $user->save();
        return $user;
    }


    /**
     * Delete a user by ID
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id)
    {
        $user = User::find($id);
        if(!$user) {
            throw new ModelNotFoundException('User not found');
        }
        $user->delete();
        return true;
    }


    /**
     * Restore a soft-deleted user by ID
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function restore(int $id)
    {
        $user = User::withTrashed()->find($id);
        if(!$user) {
            throw new ModelNotFoundException('User not found');
        }
        $user->restore();
        return true;
    }

    /**
     * Force delete a user by ID
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function forceDelete(int $id)
    {
        $user = User::withTrashed()->find($id);
        if(!$user) {
            throw new ModelNotFoundException('User not found');
        }
        $user->forceDelete();
        return true;
    }

    


}