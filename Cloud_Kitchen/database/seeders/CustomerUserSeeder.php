<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;

class CustomerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerUser = User::create([
            'fname' => 'lolo',
            'lname' => 'Ali',
            'email' => 'lolo@gmail.com',
            'phone_number' => '01008633257',
            'password' => Hash::make('ahmedali123'),
            'email_verified_at' => now(),
        ]);

        $customerUser->assignRole('customer');

        // Create the Customer record
        $customer = Customer::create([
            'user_id' => $customerUser->id,
        ]);

        // ✅ Use addresses() from the Customer model (not from User)
        $customer->addresses()->create([
            'city' => 'Cairo',
            'district' => 'Nasr City',
            'street_address' => '123 Main Street',
        ]);

    }
}
