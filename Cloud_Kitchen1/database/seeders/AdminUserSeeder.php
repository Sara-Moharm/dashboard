<?php

namespace Database\Seeders;

use Illuminate\Database\ConsoleCSeeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the admin user
        $admin = User::create([
            'fname' => 'Ahmed',
            'lname' => 'Ali',
            'email' => 'ahmedali@gmail.com',
            'phone_number' => '01008633257',
            'password' => Hash::make('ahmedali123'),
            'email_verified_at' => now(),
        ]);
        // Assign the super admin role to the user
        $admin->assignRole('super_admin');

        // 2. Add to staff table using the admin's user_id
        Staff::create([
            'user_id' => $admin->id,
        ]);
    }
}
