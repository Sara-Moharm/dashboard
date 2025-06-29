<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Order::insert([
        //     [
        //         'total_price' => 90,
        //         'status' => 'preparing',
        //     ],
        //     [
        //         'total_price' => 150,
        //         'status' => 'ready',
        //     ],
        //     [
        //         'total_price' => 210,
        //         'status' => 'delivering',
        //     ],
        // ]);    
        Order::factory()->count(5)->create();    
    }
}
