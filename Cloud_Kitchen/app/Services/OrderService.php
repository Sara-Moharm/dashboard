<?php
namespace App\Services;
// use App\Services\CartService;
use Carbon\Carbon;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Order,
    CategoryDish,
    // OrderItem
    // Customer,
    // Staff
};
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function getAllOrders()
    {
        return Order::with(['orderItems.categoryDish'])->get();
    }
    
    public function createOrder(array $validated)
    {
        // Extract all model lookups BEFORE transaction
        \Log::info('Inside service');

        // Now wrap only DB writes in transaction
        return DB::transaction(function () use ($validated) {
            $orderData = [
                'status' => $validated['status'] ?? 'pending',
                'customer_id' => $validated['customer_id'] ?? null,
                'delivery_personnel_id' => $validated['delivery_personnel_id'] ?? null,
            ];

            $order = Order::create($orderData);

            foreach ($validated['order_items'] as $item) {
                $categoryDish = CategoryDish::findOrFail($item['category_dish_id']);
                $order->orderItems()->create([
                    'category_dish_id' => $categoryDish->id,
                    'price' => $categoryDish->price,
                    'quantity' => $item['quantity'] ?? 1,
                ]);
            }

            return $order->load('orderItems.categoryDish', 'customer')->toArray();
        });
    }

    public function getOrderById(string $id)
    {
        return Order::with(['orderItems.categoryDish', 'customer'])->findOrFail($id);
    }

   public function updateOrder(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $order = Order::findOrFail($id);

            $order->update(Arr::only($data, ['status', 'customer_id', 'delivery_personnel_id']));

            if (isset($data['order_items'])) {
                foreach ($data['order_items'] as $orderItem) {
                    if (isset($orderItem['id'])) {
                        $updatedItem =$order->orderItems()->findOrFail($orderItem['id']);
                        $updatedItem->update(Arr::only($orderItem, ['category_dish_id', 'quantity']));

                    } else {
                        $order->orderItems()->create([
                            'category_dish_id' => $orderItem['category_dish_id'],
                            'quantity' => $orderItem['quantity'],
                        ]);
                    }
                }
            }
            return $order->load('orderItems.categoryDish');
        });
    }

    public function deleteOrder(string $id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return $order;
    }

     public function createFromCart(\App\Models\Cart $cart)
    {
        return \DB::transaction(function () use ($cart) {

            $order = Order::create([
                'customer_id' => $cart->customer_id,
                'status' => 'pending', 
            ]);

            foreach ($cart->cartItems as $cartItem) {
                $order->orderItems()->create([
                    'category_dish_id' => $cartItem->category_dish_id,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            $order->updateTotalPrice();

            $cart->cartItems()->delete();

            return $order->load('orderItems.categoryDish');
        });
    }

     protected function updateOrderStatus(string $id, string $status, string $deliveryPersonnelStatus)
    {
        $order = Order::findOrFail($id);
        $deliveryPersonnel = auth()->user()->staff;

        $order->update([
            'status' => $status,
            'delivery_personnel_id' => $deliveryPersonnel->id,
        ]);

        $deliveryPersonnel->update([
            'status' => $deliveryPersonnelStatus,
        ]);

        return $order->fresh();
    }

    public function markAsDelivering(string $id){
        return $this->updateOrderStatus($id, 'delivering', 'busy');
    }

    public function markAsDelivered(string $id)
    {
        return $this->updateOrderStatus($id, 'delivered', 'available');
    }

    private function isInShift($start, $end)
    {
    
        $now = Carbon::now();
        // \Log::info('Current time: ' . $now->toTimeString());
        // \Log::info('Shift start: ' . $start);
        // \Log::info('Shift end: ' . $end);
       
        $shiftStart = Carbon::createFromTimeString($start);
        $shiftEnd = Carbon::createFromTimeString($end);

        // If shift does NOT pass midnight
        if ($shiftStart <= $shiftEnd) {
            return $now->between($shiftStart, $shiftEnd);
        }

        // If shift passes midnight (e.g. 22:00:00 to 06:00:00)
        return $now->gte($shiftStart) || $now->lte($shiftEnd);
    }

    public function getReadyOrdersForAvailableDeliveryShift()
    {
        // $deliveryPersonnel = auth()->user()->staff;

        // if ($deliveryPersonnel->status !== 'available') {
        //     throw new \Exception("You are not available.");
        // }

        // if (!$this->isInShift($deliveryPersonnel->shift_start, $deliveryPersonnel->shift_end)){
        //     throw new \Exception("You are not within your shift hours.");
        // }

        return Order::where('status', 'ready')->with('customer')->get()->makeHidden('delivery_personnel_id');;
    }

    public function getDeliveringOrdersForCurrentStaff()
    {
        $deliveryPersonnel = auth()->user()->staff;

        // if (!$this->isInShift($deliveryPersonnel->shift_start, $deliveryPersonnel->shift_end)){
        //     throw new \Exception("You are not within your shift hours.");
        // }

        return Order::where('status', 'delivering')->where('delivery_personnel_id', $deliveryPersonnel->id)->get()->makeHidden('delivery_personnel_id');
    }
}
