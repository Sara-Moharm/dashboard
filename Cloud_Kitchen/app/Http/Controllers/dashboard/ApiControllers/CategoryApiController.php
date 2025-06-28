<?php

namespace App\Http\Controllers\dashboard\ApiControllers;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Requests\Menu\ValidateCategoryRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryApiController extends Controller
{
    use AuthorizesRequests;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $this->authorize('viewAny', Category::class);
        
        $categories = $this->categoryService->getAllCategories();
        return !$categories->isEmpty()
        ?$this->successResponse([
            "count"         => $categories->count(),
            "categories"    => $categories,
        ],"All categories have been retrieved successfully.", 200)
        :$this->errorResponse(
            "There is no categories to display.",
            404, []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ValidateCategoryRequest $request)
    {
        $this->authorize('create', Category::class);
        try {
            Log::info('Category store method reached!');
            // $validated = $this->validateCategory($request);
            $validated = $request->validated();
            $category = $this->categoryService->createCategory($validated);

            return $this->successResponse([
                "category" => $category->load('CategoryDish'),
            ],
            "Category has been created successfully.",
            201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error caught in store method', [
                'errors' => $e->errors(),
            ]);
            return $this->errorResponse(
                "Validation Failed",
                422,
                $e->errors());
        } 
        catch(\Exception $e){
            return $this->errorResponse("An error occurred",
                                        500,
                                        $e->getMessage());
        }
    }

    
     /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {      
            $category = $this->categoryService->getCategoryById($id);
            //$this->authorize('view', $category);
            return $this->successResponse([
                "category_dishes_count" => $category->CategoryDish->count(),
                "category" => $category,
            ], "Category has been retrieved successfully.", 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse("Category not found.", 404, []);

        } catch(\Exception $e){
            $this->errorResponse("An error occurred", 500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ValidateCategoryRequest $request, string $id)
    {
        try {

            $theCategory = Category::findOrFail($id);
            $this->authorize('update', $theCategory);
            // $validated = $this->validateCategory($request, $id);
            $validated = $request->validated();
            $category = $this->categoryService->updateCategory($id, $validated);
            return $this->successResponse([
                "category_data_updated" => $category->load('CategoryDish'),
            ], "Category has been updated successfully.", 200);

        } catch(\Illuminate\Validation\ValidationException $e){
            return $this->errorResponse("Validation Failed",
                422, 
                $e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return  $this->errorResponse("Category not found.", 404, []);
        } catch(\Exception $e){
            return $this->errorResponse("An error occurred", 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $theCategory = Category::findOrFail($id);
            $this->authorize('delete', $theCategory);
            $category = $this->categoryService->deleteCategory($id);
            return $this->successResponse([
                "category_data_deleted" => $category->load('CategoryDish'),
            ], "Category has been deleted successfully.", 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse("Category not found.", 404, []);
        } catch(\Exception $e){
            return $this->errorResponse("An error occurred", 500, $e->getMessage());
        }
    }

    //  /**
    //  * Validation for creating/updating categories.
    //  */
    // private function validateCategory(Request $request, string $id = null)
    // {
    //     return $request->validate([
    //         'title' => $id
    //             ? "sometimes|required|string|max:255|unique:categories,title,$id"
    //             : 'required|unique:categories,title|string|max:255',
    //         'description' => 'nullable|string|max:1020',
    //         'image_url' => 'nullable|string|max:2048',
    //         'category_dishes' => 'nullable|array',
    //         'category_dishes.*.id' => 'nullable|exists:category_dishes,id',
    //         'category_dishes.*.title' => 'required_without:category_dishes.*.id|string|max:255',
    //         'category_dishes.*.price' => 'required_without:category_dishes.*.id|numeric|min:0',
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

 