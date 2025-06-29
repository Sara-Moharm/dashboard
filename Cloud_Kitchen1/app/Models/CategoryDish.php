<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
class CategoryDish extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function Category(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function orderItems():\Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(OrderItem::class);
    }
    protected static function booted(){ 
        
        static::updating(function ($categoryDish) {
            $orderItems = $categoryDish->orderItems()->get();
            foreach ($orderItems as $orderItem) {
                $orderItem->price = $categoryDish->price;
                $orderItem->save();
            }
        });


        static::deleting(function ($categoryDish) {
            $categoryDish->orderItems()->with('order')->get()->each(function ($orderItem) {
                $status = $orderItem->order?->status;

                // Only delete if status is not among the protected statuses
                if (!in_array($status, [
                    // 'confirmed',
                    'preparing',
                    'ready',
                    'delivering',
                    'delivered'
                ])) {
                    $orderItem->delete();
                }
            });
        });
    }        
}
