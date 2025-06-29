<?php

namespace App\Http\Controllers\dashboard\ApiControllers;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Services\OrderItemService;
use App\Http\Requests\Orders\ValidateOrderItemRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderItemApiController extends Controller
{
    use AuthorizesRequests;
    protected $orderItemService;

    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', OrderItem::class);
        $orderItems = $this->orderItemService->getAllOrderItems();
        return !$orderItems->isEmpty()
        ?$this->successResponse([
            "count"       => $orderItems->count(),
            "order_items" => $orderItems
        ],"Order Items retrieved successfully", 200)
        :$this->errorResponse(
            "No Order Items found",
         404, []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ValidateOrderItemRequest $request)
    {
        $this->authorize('create', OrderItem::class);
        try {
            // $validated = $this->validateOrderItem($request);
            $validated = $request->validated();
            $orderItem = $this->orderItemService->createOrderItem($validated);

            return $this->successResponse([
                 "orderItem" => $orderItem
            ], "Order Item created successfully", 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                    "validation failed",
                    422, $e->errors());
        } catch(\Exception $e){
            return $this->errorResponse(
                "An error occured",
                422, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $orderItem = $this->orderItemService->getOrderItemById($id);
            $this->authorize('view', $orderItem);
            return $this->successResponse([
                "orderItem" => $orderItem
            ], "Order Item retrieved successfully", 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                "No Order Items found",   
                404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                        "An error occured",                
                        500, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ValidateOrderItemRequest $request, string $id)
    {
        try {

           $orderItem = OrderItem::findOrFail($id);
            $this->authorize('update', $orderItem);
            // $validated = $this->validateOrderItem($request, true);
            $validated = $request->validated();
             
            $orderItem = $this->orderItemService->updateOrderItem($id, $validated);

            return $this->successResponse([                
                "orderItem" => $orderItem
            ], "Order Item updated successfully",200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                        "Order Item not found",
             404,   []);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                            "validation failed",
                            422, $e->errors());
        } catch(\Exception $e){
            return $this->errorResponse(
                "An error occured",
             422, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(string $id)
    {
        try {
            $orderItem = $this->orderItemService->deleteOrderItem($id);
            $this->authorize('delete', $orderItem);
            return $this->successResponse([                         
                'orderItem'    => $orderItem->load(['CategoryDish','Order']),
            ], 'Order item deleted successfully.', 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Order item Not Found',
             404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                'An error occured',
                500, $e->getMessage());
        }
    }

    // private function validateOrderItem(Request $request, bool $isUpdate = false)
    // {
    //     return $request->validate([
    //         "quantity" => "sometimes|required|integer|min:1",
    //         "order_id" => $isUpdate ? "sometimes|required|integer|min:1|exists:orders,id"
    //                                 : "required|integer|min:1|exists:orders,id",
    //         "category_dish_id" => $isUpdate ? "sometimes|required|integer|min:1|exists:category_dishes,id"
    //                                         : "required|integer|min:1|exists:category_dishes,id"
    //     ]);
    // }

    public function markAsPending(string $id)
    {
        try {
            
            $updatedItem = $this->orderItemService->markAsPending($id);
            return $this->successResponse([                         
                'data' => $updatedItem,
            ], 'Order item marked as pending.', 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Order item Not Found',
             404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                'An error occured',
                500, $e->getMessage());
        }
    }
    public function markAsPreparing(string $id)
    {
        try {
            
            $updatedItem = $this->orderItemService->markAsPreparing($id);
            return $this->successResponse([                         
                'data' => $updatedItem,
            ], 'Order item marked as preparing.', 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Order item Not Found',
             404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                'An error occured',
                500, $e->getMessage());
        }
    }

    public function markAsReady(string $id)
    {
        try {
            
            $updatedItem = $this->orderItemService->markAsReady($id);
            return $this->successResponse([                         
                'data' => $updatedItem,
            ], 'Order item marked as ready.', 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Order item Not Found',
             404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                'An error occured',
                500, $e->getMessage());
        }
    }

    public function getPendingOrderItems()
    {
        try {
            $orderItems = $this->orderItemService->getPendingItemsForCurrentKitchenShift();
            return !$orderItems->isEmpty()
                ? $this->successResponse([
                    "count"   => $orderItems->count(),
                    "order_items"  => $orderItems->load(
                        'CategoryDish'),
                    ""
                ],   "All pending order items have been retrieved successfully.", 200)
                : $this->errorResponse(
                                "There is no pending order items to display.",
                                404, []);
        } catch (\Exception $e) {
            return $this->errorResponse(
                                "An error occurred.",
                                500, $e->getMessage());
        }
    }

    public function getPreparingOrderItems()
    {
        try {
            $orderItems = $this->orderItemService->getPreparingItemsForCurrentKitchenStaff();
            return !$orderItems->isEmpty()
                ? $this->successResponse([
                    "count"   => $orderItems->count(),
                    "order_items"  => $orderItems->load('CategoryDish'),
                ],   "All preparing order items have been retrieved successfully.", 200)
                : $this->errorResponse(
                                "There is no preparing order items to display.",
                                404, []);
        } catch (\Exception $e) {
            return $this->errorResponse(
                                "An error occurred.",
                                500, $e->getMessage());
        }
    }

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
