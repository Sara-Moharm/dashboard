<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Register a new customer
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create([
             'fname' => $data['fname'] , 
             'lname' => $data['lname'] , 
             'email' => $data['email'] ,
             'phone_number' => $data['phone_number'], 
             'password' => $data['password']
        ]);
        return $user;
    }

    public function createCustomerProfile(array $data, User $user)
    {
        $customer = null;
            // Create the customer profile
            $customer =Customer::create([
                'user_id' => $user->id,
                'second_phone_number' => $data['second_phone_number'] ?? null,
            ]);

            // Create the address
            $customer->addresses()->create([
                'city' => $data['address']['city'],
                'district' => $data['address']['district'] ?? null,
                'street_address' => $data['address']['street_address'] ?? null,
            ]);

        

        if (!$customer) {
            throw new Exception('Failed to create customer profile', 500);
        }
        return $customer->load('addresses');
    }

    
    public function createStaffProfile(array $data, User $user)
    {
        return new Staff([
            'user_id' => $user->id,
            'shift_start' => $data['shift_start'] , 
            'shift_end' => $data['shift_end'] , 
            'status' => $data['status']]);
    }

    
    public function registerCustomer(array $data): User
    {
        DB::beginTransaction();
        try{
        $user = $this->createUser($data);
        $user->syncRoles('customer');
        $customer = $this->createCustomerProfile($data, $user);

        $user->customer()->save($customer);

        DB::commit();
        // Fire the Registered event
        //event(new Registered($user));

        // Send verification email
        $user->sendEmailVerificationNotification();

        activity()
            ->causedBy($user) 
            ->performedOn($user)
            ->withProperties(['email' => $user->email])
            ->log('User registered');


        return $user->load('customer');
        
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;

        }
    }

    /**
     * Create a new staff account (only admin can do this)
     *
     * @param array $data
     * @return User
     */
    public function registerStaff (array $data): User
    {
        if($data['role'] === 'kitchen_staff' || $data['role'] === 'delivery'){
            $data['status'] = 'available';
        }

        // Set email_verified_at to now for staff members
        $data['email_verified_at'] = now();

        $user = $this->createUser($data);
        $user->syncRoles($data['role']);
        $staff = $this->createStaffProfile($data , $user);

        $user->staff()->save($staff);

        return $user->load('staff');
    }

    /**
     * Login a user and return token
     *
     * @param array $credentials
     * @return User
     * @throws ValidationException
     */
    public function CustomerLogin(array $credentials): User
    {
      $user = User::withTrashed()->where('email', $credentials['email'])->first();

        if (!$user) {
            throw new ValidationException('Invalid credentials',401);
        }

        if(!$user->isCustomer())
        {
            throw new Exception('This account is not authorized for customers login', 403);
        }

            // Check if the user is soft-deleted
            if ($user->trashed()) {
                $user->restore(); 
                Log::info('User account restored', ['email' => $credentials['email']]);
            }

        // Check if the email is verified
            if(!$this->checkEmailVerification($credentials['email']))
            {
                throw new Exception('Please verify your email before logging in.');
            }

            // Check if the account is deactivated
            if($this->checkDeactivation($credentials['email']) == true)
            {
                throw new Exception('Your account has been deactivated. Please contact support.', 403);
            }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password is incorrect'],
            ]);
        }

        // Revoke all existing tokens (optional)
        $user->tokens()->delete();
        return $user;
    }

    public function StaffLogin(array $credentials): User
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email not found'],
            ]);
        }

        if(!$user->isStaff())
        {
            throw new Exception('This account is not authorized for staff login', 403);
        }

         if($this->checkDeactivation($credentials['email']) == true)
            {
                throw new Exception('Your account has been deactivated. Please contact admin.', 403);
            }

        
         if (!Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password is incorrect'],
            ]);
        }

        //Revoke all existing tokens 
        $user->tokens()->delete();

        return $user;
    }

    /**
     * check if a user has a verified email
     * @param string $email
     * @return bool
     */
    private function checkEmailVerification(string $email): bool
    {
        $user = User::where('email', $email)->firstOrFail();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email not found'],
            ]);
        }

        

        if (!$user->hasVerifiedEmail()) {
            Log::warning('Attempt to login with unverified email', ['email' => $email]);
            return false;
        }

        return true;
    }

    /**
     * Check if a user is deactivated before allowing login
     *
     * @param string $email
     * @return bool
     */
    private function checkDeactivation(string $email)
    {
        $user = User::where('email', $email)->first();

        if($user && !$user->isActive())
        {
            Log::warning('Attempt to login to deactivated account', ['email' => $email]);

            return true;
        }
        
    }
    /**
     * Logout a user
     *
     * @param User $user
     * @return bool
     */
    public function logout(): bool
    {
        
        $user = auth()->user();
        // Delete the current token
        $user->currentAccessToken()->delete();
        
        return true;
    }

    public function logoutFromAllDevices(): bool
    {
        $user = User::where('id', Auth::user()->id)->first();
        $user->tokens()->delete();
        return true;
    }
}