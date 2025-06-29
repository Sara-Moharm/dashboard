<?php

namespace App\Http\Controllers\dashboard\ApiControllers;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Services\{CartService, OrderService};
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ValidateMealRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class CartController extends Controller
{

     use AuthorizesRequests;
    protected $cartService;
    protected $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function show()
    {
        $this->authorize('view', auth()->user()->customer->cart);
        // Retrieve the cart items for the authenticated user
        $cartItems = $this->cartService->getCartItems();
        return !$cartItems->isEmpty()
        ?$this->successResponse([
            "total"        => $this->cartService->getCartTotal(),
            "cartItems"    => $cartItems,
        ],"All cart items have been retrieved successfully.", 200)
        :$this->errorResponse(
            "There is no cart items to display.",
            404, []);
    }

    /**
     * Store a newly cart item in storage.
     */
    public function addItem(\App\Http\Requests\ValidateCartRequest $request)
    {
        $this->authorize('update', auth()->user()->customer->cart);
        try {
            Log::info('addItem method reached!');
            // $validated = $this->validateCategory($request);
            $validated = $request->validated();
            Log::info('🎯 category_dish_id: ' . $validated['category_dish_id']);
            Log::info('🎯 quantity: ' . json_encode($validated['quantity'] ?? 'not set'));

            // $quantity = $validated['quantity'];
            $cart = $this->cartService->addItem(
                $validated["category_dish_id"],
                      $validated["quantity"] ?? 1
            );

            return $this->successResponse([
                "cart" => $cart,
                "carttotal" => $this->cartService->getCartTotal(),
                "cartCount" => $this->cartService->getCartCount(),
            ],
            "cart item has been created successfully.",
            201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error caught in add item method', [
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

    public function addMeal (ValidateMealRequest $request)
    {
        $this->authorize('update', auth()->user()->customer->cart);
        try {
            Log::info('addMeal method reached!');
            $validated = $request->validated();

            foreach ($validated['meal_dishes'] as $mealDish) {
                $cart = $this->cartService->addItem(
                    $mealDish['category_dish_id']
                );
            }

            return $this->successResponse([
                "cart" => $cart,
                "carttotal" => $this->cartService->getCartTotal(),
                "cartCount" => $this->cartService->getCartCount(),
            ],
            "Meal has been added to cart successfully.",
            201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error caught in add meal method', [
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
    
    public function incrementItem($id)
    {
        $this->authorize('update', auth()->user()->customer->cart);
        try {
            $item = $this->cartService->incrementItemQuantity($id);
            return $this->successResponse([
                'data' => $item
            ],'Quantity incremented');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return  $this->errorResponse("Item not found.", 404, []);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to increment quantity.', 400, $e->getMessage());
        }
    }

    public function decrementItem($id)
    {
        $this->authorize('update', auth()->user()->customer->cart);
        try {
            $item = $this->cartService->decrementItemQuantity($id);
            return $this->successResponse([
                'data' => $item
            ], $item ? 'Quantity decremented' : 'Item removed from cart');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return  $this->errorResponse("Item not found.", 404, []);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to decrement quantity.', 400, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource.
     */
    public function removeItem($itemId)
    {
        $this->authorize('update', auth()->user()->customer->cart);
        try {
           $cartItem = $this->cartService->removeItem($itemId);

            return $this->successResponse([
                "removed cartItem" => $cartItem,
            ],
            "cart item has been removed successfully.",
            201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error caught in remove item method', [
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
     * Update the specified resource in storage.
     */
    public function updateItemQuantity(\App\Http\Requests\ValidateCartRequest $request, string $id)
    {
        $this->authorize('update', auth()->user()->customer->cart);
        try {
            Log::info('updateItemQuantity method reached!');
            // $validated = $this->validateCategory($request);
            $validated = $request->validated();
            $quantity = $validated['quantity'] ?? 1;

            $cartItem = $this->cartService->updateItemQuantity(
                $id,
                $quantity
            );

            return $this->successResponse(["cartItem" => $cartItem],
            "cart item has been updated successfully.",
            201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error caught in update item quantity method', [
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
     * Remove the specified resource from storage.
     */
    public function clearCart()
    {
        $this->authorize('update', auth()->user()->customer->cart);
        //
        try {
            $this->cartService->clearCart();

            return $this->successResponse([],
            "cart item has been removed successfully.",
            201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error caught in clear cart method', [
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

    public function count()
    {
        $this->authorize('view', auth()->user()->customer->cart);

        return response()->json(['count' => $this->cartService->getCartCount()]);
    }

    public function total()
    {
        $this->authorize('view', auth()->user()->customer->cart);

        return response()->json(['total' => $this->cartService->getCartTotal()]);
    }

    public function checkoutCart()
    {
        $this->authorize('update', auth()->user()->customer->cart);
        try {
            $cart = $this->cartService->ensureCartNotEmpty();
            $order = $this->orderService->createFromCart($cart);
        
            return $this->successResponse([
                'success' => true,
                'message' => 'Order placed successfully.',
                'order'   => $order
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse(
                'An error occurred while retrieving the cart.',
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
