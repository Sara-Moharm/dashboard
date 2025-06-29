<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class DeliveryUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $delivery_personnel = User::create([
            'fname' => 'lolo',
            'lname' => 'Ali',
            'email' => 'lolololo@gmail.com',
            'phone_number' => '01008633257',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        // Assign the super admin role to the user
        $delivery_personnel->assignRole('delivery');

        // 2. Add to staff table using the admin's user_id
        Staff::create([
            'user_id' => $delivery_personnel->id,
        ]);
    }
}
