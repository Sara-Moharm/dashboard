<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Order,CategoryDish,OrderItem};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $customer_id = \App\Models\Customer::inRandomOrder()->first();
        // $address_id =  \App\Models\Address::inRandomOrder()->where('customer_id', $customer_id?->id)->inRandomOrder()->first();
        return [
            'customer_id' => $customer_id?->id,
            // 'customer_address_id' => $address_id?->id,
            'total_price' => 0,
            'status' => $this->faker->randomElement([
                'pending', 'preparing', 'ready', 'delivering', 'delivered', 'cancelled', 'failed'
            ]),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            $total = 0;

            $itemsCount = $this->faker->numberBetween(1, 2);
            $categoryDishes = CategoryDish::inRandomOrder()->take(5)->get();

            for ($i = 0; $i < $itemsCount; $i++) {
                $categoryDish = $categoryDishes->random();

                if ($categoryDish) {
                    $quantity = $this->faker->numberBetween(1, 1000);
                    $price = $categoryDish->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'category_dish_id' => $categoryDish->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);

                    $total += $quantity * $price;
                }
            }
            $order->update(['total_price' => $total]);
        });
    }   
}
