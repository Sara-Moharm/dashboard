<?php

namespace App\Http\Controllers\dashboard\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\Orders\ValidateOrderRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderApiController extends Controller
{
    use AuthorizesRequests;
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $this->authorize('viewAny', Order::class);
        $orders = $this->orderService->getAllOrders();

        return !$orders->isEmpty()
            ? $this->successResponse([
                "count"   => $orders->count(),
                "orders"  => $orders,
            ],   "All orders have been retrieved successfully.", 200)
            : $this->errorResponse(
                            "There is no orders to display.",
                            404, []);
    }

      /**
     * Store a newly created resource in storage.
     */
    public function store(ValidateOrderRequest $request)
    {
        $validated = $request->validated();
        $this->authorize('create', Order::class);
        try {
             Log::info('Controller hit');

            $order = $this->orderService->createOrder($validated);
    
            return $this->successResponse([
                        'orders'  => $order
            ], 'Order created successfully', 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Caught in controller catch');
            Log::error('Caught in ModelNotFoundException');
            $model = method_exists($e, 'getModel') && $e->getModel()
            ? class_basename($e->getModel()) 
            : 'Resource';

            return $this->errorResponse(
                "$model not found.",
                404,
                []); 

        }catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed.', ['errors' => $e->errors()]);
            return $this->errorResponse(
                    'Validation Failed',
                    422, $e->errors());
        } catch(\Exception $e){
            return $this->errorResponse(
                "An error occurred.", 
                500, $e->getMessage());
        }
    }

       /**
     * Display the specified resource.
     */

     public function show(string $id)
     {
        try {
            $order = $this->orderService->getOrderById($id);
            $this->authorize('view', $order);
            return $this->successResponse([
                "order_items_count" => $order->orderItems->count(),
                "order" => $order,
            ],"Order has been retrieved successfully.", 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return $this->errorResponse(
                                'Order not found.',
                                404, []); 
           
        } catch (\Exception $e) {
            return $this->errorResponse(
                                "An error occurred.",
                                500, $e->getMessage());
        }
     }

    /**
     * Update the specified resource in storage.
     */
    public function update(ValidateOrderRequest $request, string $id)
    {
        try {
            $order = Order::findOrFail($id);
            $this->authorize('update', $order);

            $validated = $request->validated();
            $updatedOrder = $this->orderService->updateOrder($id, $validated);
    
            return $this->successResponse([
                'data' => $updatedOrder
            ],'Order updated successfully.', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             $model = method_exists($e, 'getModel') 
            ? class_basename($e->getModel()) 
            : 'Resource';

            return $this->errorResponse(
                "$model not found.",
                404,
                []
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                                'Validation failed.',
                                422, $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(
                                 'An error occurred.',
                                 500, $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $theOrder = Order::findOrFail($id);
            $this->authorize('delete', $theOrder);
            
            $order = $this->orderService->deleteOrder($id);
    
            return $this->successResponse([
                'success' => true,
                'message' => 'Order deleted successfully.',
                'order'   => $order->load('orderItems')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                                'Order not found.',
                                404, []);
        } catch (\Exception $e) {
            return $this->errorResponse(
                                'An error occurred.',
                                500, $e->getMessage());
        }
    }

    public function markAsDelivering(string $id)
    {
        try {
            
            $updatedItem = $this->orderService->markAsDelivering($id);
            return $this->successResponse([                         
                'data' => $updatedItem,
            ], 'Order marked as delivering.', 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Order Not Found',
             404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                'An error occured',
                500, $e->getMessage());
        }
    }

    public function markAsDelivered(string $id)
    {
        try {
            
            $updatedItem = $this->orderService->markAsDelivered($id);
            return $this->successResponse([                         
                'data' => $updatedItem,
            ], 'Order marked as delivered.', 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Order Not Found',
             404, []);
        } catch(\Exception $e){
            return $this->errorResponse(
                'An error occured',
                500, $e->getMessage());
        }
    }

    public function getReadyOrders()
    {
        try {
            $orders = $this->orderService->getReadyOrdersForAvailableDeliveryShift();
            return !$orders->isEmpty()
                ? $this->successResponse([
                    "count"   => $orders->count(),
                    "orders"  => $orders->load(['customer', 'orderItems.categoryDish', 'address']),
                ],   "All ready orders have been retrieved successfully.", 200)
                : $this->errorResponse(
                                "There is no ready orders to display.",
                                404, []);
        } catch (\Exception $e) {
            return $this->errorResponse(
                                "An error occurred.",
                                500, $e->getMessage());
        }
    }

    public function getDeliveringOrders()
    {
        try {
            $orders = $this->orderService->getDeliveringOrdersForCurrentStaff();
            return !$orders->isEmpty()
                ? $this->successResponse([
                    "count"   => $orders->count(),
                    "orders"  => $orders->load('customer.user', 'address'),
                ],   "All delivering orders have been retrieved successfully.", 200)
                : $this->errorResponse(
                                "There is no delivering orders to display.",
                                404, []);
        } catch (\Exception $e) {
            return $this->errorResponse(
                                "An error occurred.",
                                500, $e->getMessage());
        }
    }

    // private function validateOrder(Request $request, bool $isUpdate = false)
    // {
    //     return $request->validate([
    //         'status' => 'sometimes|required|in:pending,confirmed,preparing,ready,delivering,delivered,cancelled,failed',
    //         'order_items' => $isUpdate ? 'sometimes|required|array' : 'required|array|min:1',
    //         'order_items.*.id' => 'sometimes|required|exists:order_items,id',
    //         'order_items.*.category_dish_id' => $isUpdate
    //             ? 'required_without:items.*.id|exists:category_dishes,id'
    //             : 'required|exists:category_dishes,id',
    //         'order_items.*.quantity' => $isUpdate
    //             ? 'required_without:items.*.id|integer|min:1'
    //             : 'sometimes|required|integer|min:1',
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
