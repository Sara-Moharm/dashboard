<?php

namespace App\Http\Controllers\dashboard\ApiControllers;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Services\CategoryDishService;
use App\Http\Requests\Menu\ValidateCategoryDishRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\CategoryDish;
use Illuminate\Support\Facades\Auth;
class CategoryDishApiController extends Controller
{
    use AuthorizesRequests;
    protected $categoryDishService;

    public function __construct(CategoryDishService $categoryDishService)
    {
        $this->categoryDishService = $categoryDishService;
    }

    public function index()
    {
        // $this->authorize('viewAny', CategoryDish::class);
        $categoryDishes = $this->categoryDishService->getAllCategoryDishes();
        return !$categoryDishes->isEmpty()
        ?$this->successResponse([
            "count"             => $categoryDishes->count(),
            'category_dishes'   => $categoryDishes
        ], 'All category Dishes have been retrieved successfully.', 200)
        :$this->errorResponse(
            'There is no category dishes to display.',
         404, []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ValidateCategoryDishRequest $request)
    {
        $this->authorize('create', CategoryDish::class);
        try {
            // $validated = $this->validateCategoryDish($request);
            $validated = $request->validated();

    
            $categoryDish = $this->categoryDishService->createCategoryDish($validated);
    
            return $this->successResponse([
                'category_dish'    => $categoryDish->load('category')
            ],'Category dish has been created successfully.', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation Failed',
             422, $e->errors());
        } catch(\Exception $e){
            return $this->errorResponse(
                 'An error occured',
                 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $categoryDish = $this->categoryDishService->getCategoryDishById($id);

            //$this->authorize('view', $categoryDish);
            return $this->successResponse([
                'category_id'    => $categoryDish->Category->id,
                'category_dish'  => $categoryDish
            ], 'Category dish has been retrieved successfully.',200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Category dish not found',
                404, []);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'An error occured',
                500 , $e->getMessage(),);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ValidateCategoryDishRequest $request, string $id)
    {
        try {

            // $validated = $this->validateCategoryDish($request, $id);
            $validated = $request->validated();
            $dish = CategoryDish::findOrFail($id);
            $this->authorize('update', $dish);
            $categoryDish = $this->categoryDishService->updateCategoryDish($id, $validated);
            return $this->successResponse([
                'category_dish'    => $categoryDish->load('Category')
            ], 'Category dish has been updated successfully.', 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                                'Validation Failed',
                                422, $e->errors());
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            return $this->errorResponse(
                                'CategoryDish Not Found',
                                404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                                    'An error occured',
                                    500, $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $dish = CategoryDish::findOrFail($id);
            $this->authorize('delete', $dish);
            $categoryDish = $this->categoryDishService->deleteCategoryDish($id);
            return $this->successResponse([
                'category_dish_data_deleted'    => $categoryDish,
            ], 'CategoryDish deleted successfully.',200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                                'CategoryDish Not Found',
                                404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                                'An error occured',
                                500,   $e->getMessage());
        }
    }

    
     /**
     * Validation for creating/updating categoryDishes.
     */
    // private function validateCategoryDish(Request $request, string $id = null)
    // {
    //     return $request->validate([
    //         'title' => $id
    //             ? "sometimes|required|string|max:255|unique:category_dishes,title,$id"
    //             : 'required|string|max:255|unique:category_dishes,title',
    //         'price'=> $id
    //             ? "sometimes|required|numeric|min:0.01|max:9999.99"
    //             : "required|numeric|min:0.01|max:9999.99",
    //         'category_id' => $id
    //             ? "sometimes|required|integer|exists:categories,id"
    //             : "required|integer|exists:categories,id",
    //         'description' => 'nullable|string|max:1020',
    //         'image_url' => 'nullable|string|max:2048',
    //         "meal_rate"   => 'nullable|numeric|between:0,5.00',
    //         "availability"=> 'nullable|integer|min:0',
    //         "calories"    => 'nullable|numeric|min:0',
    //     ]);
    // }

    /**
     * Success response format.
     */
    private function successResponse(array $data = [], string $message = "Success", int $status = 200)
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            ...$data,
        ], $status);
    }
    /**
     * Error response format.
     */
    private function errorResponse(string $message, int $status = 500, $errors = null)
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "errors" => $errors,
        ], $status);
    }
}
