<?php

namespace App\Http\Controllers;

use App\Http\Requests\Menu\MealSuggestionRequest;
use App\Services\MealSuggestionService;
use Illuminate\Support\Facades\Log;

class MealSuggestionController extends Controller
{
    protected $mealSuggestionService;

    public function __construct(MealSuggestionService $mealSuggestionService)
    {
        $this->mealSuggestionService = $mealSuggestionService;
    }
    /**
     * Suggest a meal based on user preferences.
     *
     * @param MealSuggestionRequest $request
     * @return array
     */
    public function suggestMeal(MealSuggestionRequest $request)
    {
    
        $validated = $request->validated();
        
        // Check if categories are empty and remove them
        if (!empty($validated['categories']))
        {
            $validated['categories'] = $this->mealSuggestionService->checkEmptyCategories($validated['categories']);
            if($validated['categories'] == []) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No available dishes found in the selected categories.',
                ], 400);
            }
        } 
        
        
        if(count($validated['categories'] ?? []) == 1) {
            $meals = $this->mealSuggestionService->getOneCategoryDishes($validated['categories'][0], $validated['calories']);
            if (empty($meals)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No dishes available within the specified calorie range.',
                ], 400);
            }
            return response()->json([
                'status' => 'success',
                'message' =>  'Meal suggestions generated successfully.',
                'data' => $meals,
            ], 200);
        }
        
        $results = [];
        $seen = [];
        $attempts = 0;
        $maxResults = 10;

        
        $all_dishes = $this->mealSuggestionService->fetchDishesGroupedByCategory();
        if(collect($all_dishes)->flatten()->min('calories') > $validated['calories']) {
            return response()->json([
                'status' => 'error',
                'message' => 'No dishes available within the specified calorie range1.',
            ], 400);
        }
        $selected_categories = $this->mealSuggestionService->selectCategories($all_dishes, $validated['categories'] ?? []);
        $categories = $this->mealSuggestionService->shuffleCategories($selected_categories, $validated['calories']);

        $minFromEach = collect($categories)->map(function ($dishes) {
            return collect($dishes)->sortBy('calories')->first();
        });

        $totalCalories = $minFromEach->sum('calories');

        if ($totalCalories > $validated['calories']) {            
            return response()->json([
                'status' => 'error',
                'message' => 'No dishes available within the specified calorie range2.',
            ], 400);
        }
        $totalCombinations = array_reduce($categories, function ($carry, $dishes) {
        return $carry * count($dishes);
        }, 1);

        $maxAttempts = min(1000, $totalCombinations * 2);

        while (count($results) < $maxResults &&  $attempts < $maxAttempts && count($seen) < $totalCombinations) {
            
            $attempts++;
            
            // Generate a single combination of dishes from the selected categories
        
            $shuffled_categories = $this->mealSuggestionService->shuffleCategories($selected_categories);

            $generated = $this->mealSuggestionService->generateSingleCombination($shuffled_categories);
            if (!$generated) continue;

            $key = $generated['key'];
            $calories = $generated['totalCalories'];

            if (isset($seen[$key])) continue;
            if (!$this->mealSuggestionService->isCaloriesInRange($calories, $validated['calories'])) continue;

            $seen[$key] = true;

            $dishes = $generated['combo'];
            $dishes = array_map(fn($dish) => [
                'id' => $dish['id'],
                'title' => $dish['title'],
                'calories' => $dish['calories'],
                'image_url' => asset($dish['image_url']), // ← أضفنا ده
            ], $dishes);

            $results[] = [
            'dishes' => $dishes,
             'key' => $key,
            'total_calories' => $calories,
             ]; 
        }

        Log::info('Meal suggestion attempts: ' . $attempts);
        
        return response()->json([
        'status' => 'success',
        'message' => 'Meal suggestions generated successfully.',
        'total' => count($results), 
        'data' => $results,
        ], 200);

     
        
    }
}
