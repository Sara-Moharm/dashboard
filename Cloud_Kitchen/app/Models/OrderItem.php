<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use App\Models\{CategoryDish,Order, Staff};

class OrderItem extends CategoryDish
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'category_dish_id',
        'staff_id',
        'quantity',
        'price',
        'status', 
    ];
    protected $guarded = [];
    
    // In OrderItem model

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Order::class);
    }

    public function categoryDish():\Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(CategoryDish::class);
    }

    public function preparedBy(){
        return $this->belongsTo(Staff::class);
    }

    protected static function booted(){
        
        static::creating(function ($orderItem) {
            $orderItem->price = $orderItem->categoryDish->price;
            if ($orderItem->order) {
                $orderItem->order->updateTotalPrice();
            }
        });

        static::saved(function ($orderItem) {
            $orderItem->price = $orderItem->categoryDish->price;
            if ($orderItem->order) {
                $orderItem->order->updateTotalPrice();
            }
        });

        static::updating(function ($orderItem) {
            if ($orderItem->isDirty('status')) {
                switch ($orderItem->status) {
                    case 'preparing':
                        $orderItem->preparing_at = now();
                        break;
                    case 'ready':
                        $orderItem->ready_at = now();
                        break;
                    case 'delivered':
                        $orderItem->delivered_at = now();
                        break;
                }
            }
        });

        static::updated(function ($orderItem) {
            // Update order_item price
            $orderItem->price = $orderItem->categoryDish->price;
            // Update order total price
            if ($orderItem->order) {
                $orderItem->order->updateTotalPrice();
            }

            // If any item becomes preparing, set order as preparing
            if ($orderItem->isDirty('status') && $orderItem->status === 'preparing') {
                $order = $orderItem->order;

                if ($order && $order->status !== 'preparing') {
                    $order->update(['status' => 'preparing']);
                }
            }

            // If all items are ready, mark order as ready
            $orderItem->order->updateStatusIfAllItemsAre('ready');

            // If all items are delivered, mark order as delivered
            $orderItem->order->updateStatusIfAllItemsAre('delivered');

        });

        static::deleted(function ($orderItem) {
            if ($orderItem->order) {
                $orderItem->order->updateTotalPrice();
            }
        });
    }
}
