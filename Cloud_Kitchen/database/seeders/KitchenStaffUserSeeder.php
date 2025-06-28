<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class KitchenStaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // 1. Create the admin user
        $kitchen_staff = User::create([
            'fname' => 'Sara',
            'lname' => 'Ali',
            'email' => 'sara@gmail.com',
            'phone_number' => '01008633257',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        // Assign the super admin role to the user
        $kitchen_staff->assignRole('kitchen_staff');

        // 2. Add to staff table using the admin's user_id
        Staff::create([
            'user_id' => $kitchen_staff->id,
        ]);
    }
}
