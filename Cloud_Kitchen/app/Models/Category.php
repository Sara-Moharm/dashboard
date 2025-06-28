<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryDish;
class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function CategoryDish(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CategoryDish::class);
    }

    
    protected static function booted(){ 

        static::deleting(function ($category) {
            $categoryDishes = $category->CategoryDish;
            foreach ($categoryDishes as $dish) {
                $dish->delete(); // triggers custom logic in CategoryDish
            }
        });

    }        
}

