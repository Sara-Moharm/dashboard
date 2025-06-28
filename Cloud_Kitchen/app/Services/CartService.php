<?php

namespace App\Services;
use App\Models\{Cart};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class CartService
{
    // This service can be used to handle cart-related logic, such as adding items, removing items, and calculating totals.
    // You can implement methods like addItem, removeItem, getCartItems, etc. based on your application's requirements.

    public function getCart()
    {
        $customer = Auth::user()?->customer;

        if (!$customer) {
            throw new \Exception("This user doesn't have a customer profile.");
        }

        return Cart::firstOrCreate([
            'customer_id' => $customer->id,
        ]);
    }

    public function addItem($categoryDishId, $quantity = 1)
    {
        Log::info("🔥 Inside CartService.addItem, Quantity: $quantity");
        // Logic to add item to the cart
        $cart = $this->getCart();

        $item = $cart->cartItems()->where('category_dish_id', $categoryDishId)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->cartItems()->create([
                // 'cart_id' => $cart->id,
                'category_dish_id' => $categoryDishId,
                'quantity' => $quantity,
            ]);
        }

        return $cart->fresh('cartItems.categoryDish');
        
    }

    // Increment the quantity when clicking + icon.
    public function IncrementItemQuantity($itemId)
    {
        // Logic to update the quantity of an item in the cart
        $item = $this->getCart()->cartItems()->where('id', $itemId)->firstOrFail();

        $item->increment('quantity');

        return $item->fresh('categoryDish');
            
    }

    // Decrement the quantity when clicking - icon.

    public function DecrementItemQuantity($itemId)
    {
        // Logic to update the quantity of an item in the cart
        $item = $this->getCart()->cartItems()->where('id', $itemId)->firstOrFail();
         if ($item->quantity > 1) {
            $item->decrement('quantity');
            return $item->fresh('categoryDish');
        } else {
            $item->delete();
            return null;
        }
    }

    public function removeItem($itemId)
    {
        // Logic to remove item from the cart
        $item = $this->getCart()->cartItems()->where('id', $itemId)->firstOrFail();
        $item->delete();
        return $item;
    }

    public function getCartItems()
    {
        // Logic to retrieve all items in the cart
        return $this->getCart()->cartItems()->with('categoryDish')->get();
    }

    public function clearCart()
    {
        // Logic to clear the cart
        $this->getCart()->cartItems()->delete();
    }

    // This method updates the quantity of an item in the cart.
    public function updateItemQuantity($itemId, $quantity)
    {
        // Logic to update the quantity of an item in the cart
        $item = $this->getCart()->cartItems()->where('id', $itemId)->firstOrFail();
        $item->update(['quantity' => $quantity]);
        return $item;
    }
    public function getCartCount()
    {
        // Logic to get the count of items in the cart
        return $this->getCart()->cartItems()->sum('quantity');
    }
    public function getCartTotal()
    {
        // Logic to get the total price of items in the cart
        return round(
                $this->getCart()->cartItems()
                            ->with('categoryDish')
                            ->get()
                            ->sum(fn($item) => $item->quantity * $item->categoryDish->price),
                    3
                );
    }

    public function ensureCartNotEmpty()
    {
        $cart = $this->getCart();
        
        if($cart->cartItems()->count() === 0){ {
            throw new \Exception("Cart is empty.");
            }
        }
        return $cart;
    }

}