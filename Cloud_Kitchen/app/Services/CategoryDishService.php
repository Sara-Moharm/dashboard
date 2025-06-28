<?php

namespace App\Services;

use App\Models\CategoryDish;
use Illuminate\Support\Arr;

class CategoryDishService
{
    public function getAllCategoryDishes()
    {
        return CategoryDish::with('Category')->get();
    }

    public function createCategoryDish(array $data)
    {
        return CategoryDish::create($data)->load('category');
    }

    public function getCategoryDishById(string $id)
    {
        return CategoryDish::with('category')->findOrFail($id);
    }

    public function updateCategoryDish(string $id, array $data)
    {
        $categoryDish = CategoryDish::findOrFail($id);
        $categoryDish->update($data);
        return $categoryDish->load('Category');
    }

    public function deleteCategoryDish(string $id)
    {
        $categoryDish = CategoryDish::findOrFail($id);
        $categoryDish->delete();
        return $categoryDish;
    }
}