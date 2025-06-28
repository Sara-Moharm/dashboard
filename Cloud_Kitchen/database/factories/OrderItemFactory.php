<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryDish = \App\Models\CategoryDish::inRandomOrder()->first();
        return [
            "order_id" => \App\Models\Order::inRandomOrder()->first()?->id,
            "quantity" => $this->faker->numberBetween(1,100),
            "category_dish_id" =>  $categoryDish?->id,
            "price" =>  $categoryDish?->price,
            'status' => $this->faker->randomElement([
                'pending', 'preparing', 'ready', 'delivered'
            ]),
        ];
    }
}
