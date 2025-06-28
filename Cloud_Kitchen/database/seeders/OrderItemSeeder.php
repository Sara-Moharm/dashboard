<?php

namespace Database\Seeders;

use App\Models\{
    OrderItem,
    Order,
    CategoryDish
};

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Order::count() && CategoryDish::count()) {
            OrderItem::factory()->count(5)->create();
        }
    }
}
