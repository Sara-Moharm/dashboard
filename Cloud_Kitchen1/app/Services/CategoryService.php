<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Arr;

class CategoryService
{

    public function getAllCategories()
    {
        $categories = Category::with('CategoryDish')->get();
        $categories->transform(function ($category) {
            $category->image_url = asset($category->image_url); // ✅ رابط كامل
            return $category;
        });
        return $categories;
    }

    public function createCategory(array $validatedData)
    {
        \Log::info('Category create method reached!');
        $category = Category::create(Arr::except($validatedData, 'category_dishes'));

        if (isset($validatedData['category_dishes'])) {
            foreach ($validatedData['category_dishes'] as $dish) {
                $category->CategoryDish()->create($dish);
            }
        }
        return $category->load('CategoryDish');
    }

    public function getCategoryById(string $id)
    {
        return Category::with('CategoryDish')->findOrFail($id);
    }

    public function updateCategory(string $id, array $validatedData)
    {
        $category = Category::findOrFail($id);

        $category->update(Arr::except($validatedData, 'category_dishes'));

        if (isset($validatedData["category_dishes"])) {
            foreach ($validatedData["category_dishes"] as $dish) {
                if (isset($dish["id"])) {
                    $existingDish = $category->CategoryDish()->find($dish["id"]);
                    if ($existingDish) {
                        $existingDish->update(Arr::only($dish, ['title', 'price']));
                    }
                } else {
                    $category->CategoryDish()->create([
                        'title' => $dish['title'],
                        'price' => $dish['price'],
                    ]);
                }
            }
        }
        return $category->load('CategoryDish');
    }

    public function deleteCategory(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return $category->load('CategoryDish');
    }
}