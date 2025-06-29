<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'category_dish_id', 'quantity'];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function categoryDish()
    {
        return $this->belongsTo(CategoryDish::class, 'category_dish_id');
    }

}
