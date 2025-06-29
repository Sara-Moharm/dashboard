<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\CategoryDish;
use Illuminate\Support\Facades\Cache;

class MealSuggestionService
{

    public function checkEmptyCategories(array $categories)
    {
        foreach ($categories as $category => $dishes) {
            if (empty($dishes)) {
                Log::warning("Category '$category' has no dishes.");
                unset($categories[$category]);
            }
        }
        return $categories;
    }

    public function getOneCategoryDishes($categoryTitle , $calories)
    {
        try
        {
            $categoryDishes = CategoryDish::whereHas('category', function ($query) use ($categoryTitle) {
            $query->where('title', $categoryTitle);
            })
            ->where('calories', '<=', $calories)
            ->select('id', 'title', 'calories')
            ->get();

            $result = $categoryDishes->shuffle()->take(10);
            if ($result->isEmpty()) {
                Log::warning("No dishes found for category '$categoryTitle' with calories <= $calories.");
                return [];
            }
            return $result->toArray();

        }
        catch (Exception $e)
        {
            Log::error('Failed to fetch dishes for category ' . $categoryTitle . ': ' . $e->getMessage());
            throw new Exception('Failed to fetch dishes for category ' . $categoryTitle);
        }
    }
    /**
     * Fetch dishes grouped by category.
     *
     * @return array
     * @throws Exception
     */
    public function fetchDishesGroupedByCategory(): \Illuminate\Support\Collection
{
    try {
        return Cache::remember('dishes_grouped_by_category', now()->addMinutes(30), function () {
            return CategoryDish::with('category')
                ->get()
                ->groupBy('category.title');
        });
    } catch (Exception $e) {
        Log::error('Failed to fetch dishes grouped by category: ' . $e->getMessage());
        throw new Exception('Failed to fetch dishes grouped by category');
    }
}

    public function selectCategories($allDishes, $preferredCategories)
    {
        $selected = [];

        if (!empty($preferredCategories)) {
            foreach ($preferredCategories as $pref) {
                if (isset($allDishes[$pref])) {
                    $selected[$pref] = $allDishes[$pref]->all();
                }
            }
        }

        if (empty($selected)) {
            $randomCount = rand(3, min(5, $allDishes->count()));
            $random = $allDishes->random($randomCount);
            foreach ($random as $catName => $dishes) {
                $selected[$catName] = $dishes->all();
            }
        }

        return $selected;
    }


    public function shuffleCategories(array $categories): array
    {
    return array_map(function ($dishes) {
            shuffle($dishes);
            return $dishes;
        }, $categories);
    }

public function generateSingleCombination(array $categories): ?array
{
    $combo = [];
    $comboKeyParts = [];
    $totalCalories = 0;

    foreach ($categories as $dishes) {
        if (empty($dishes)) continue;

        $dish = $dishes[array_rand($dishes)];
        $combo[] = $dish;
        $comboKeyParts[] = $dish['id'];
        $totalCalories += $dish['calories'];
    }
    if (empty($combo)) return null;

    sort($comboKeyParts);
    $key = implode('-', $comboKeyParts);    
    
    return [
        'combo' => $combo,
        'key' => $key,
        'totalCalories' => $totalCalories,
    ];
}

public function isCaloriesInRange(int $totalCalories, int $desiredCalories): bool
{
    if ($desiredCalories <= 0) return true;

    $min = $desiredCalories * 0.9;
    $max = $desiredCalories * 1.1;
    return $totalCalories >= $min && $totalCalories <= $max;
}

//  public function paginateResults(array $results, int $perPage, int $page): array
//     {
//         $total = count($results);
//         $offset = ($page - 1) * $perPage;
//         $paginated = array_slice($results, $offset, $perPage);

//         return [
//             'data' => $paginated,
//             'pagination' => [
//                 'total' => $total,
//                 'per_page' => $perPage,
//                 'current_page' => $page,
//                 'last_page' => ceil($total / $perPage),
//                 'from' => $offset + 1,
//                 'to' => $offset + count($paginated),
//             ],
//         ];
//     }
}
