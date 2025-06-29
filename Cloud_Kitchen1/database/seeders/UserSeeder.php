<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory()->count(5)->withRole('customer')->create();
        // User::factory()->count(5)->withRole('admin')->create();
        // User::factory()->count(5)->withRole('kitchen_staff')->create();
        // User::factory()->count(5)->withRole('delivery')->create();
    }
}
