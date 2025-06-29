<?php   

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\dashboard\ApiControllers\{ CategoryApiController,CategoryDishApiController,
    OrderApiController,OrderItemApiController,CartController};
use App\Http\Controllers\Users\AddressController;
use App\Http\Controllers\MealSuggestionController;
use App\Http\Controllers\DashboardController;

// Public routes
//when customer clicks on register, this route is called
Route::post('/register', [AuthController::class, 'CustomerRegister']);

//when customer clicks on login, this route is called
Route::post('/login', [AuthController::class, 'CustomerLogin'])
    ->middleware('throttle:5,1');
//when staff clicks on login, this route is called
Route::post('/staff/login', [AuthController::class, 'StaffLogin'])
    ->middleware('throttle:5,1');
// Email verification routes

//when user clicks on verification link, this route is called 
Route::get('/email/verify/{id}/{hash}', [EmailController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

//when user clicks on resend verification link, this route is called
Route::post('/email/verification-notification', [EmailController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

// Password reset routes

//when user clicks on forgot password, this route is called
Route::post('/forgot-password', [ResetPasswordController::class, 'forgotPassword'])
    ->middleware('throttle:3,1');

//when user clicks on reset password link, this route is called(for api testing)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'reset'])
    ->name('password.reset');

//when user clicks on reset password link, this route is called(for frontend redirection) 
// Route::get('/reset-password/{token}', function (Request $request, $token) {
//     return redirect("https://frontend-domain.com/reset-password?token={$token}&email={$request->email}");
// })->middleware('signed')->name('password.reset');

//when user clicks on reset password button, this route is called
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);


//Menu routes
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{category}', [CategoryApiController::class, 'show'])->where(['category' => '[0-9]+']);
Route::get('/category_dishes', [CategoryDishApiController::class, 'index']);
Route::get('/category_dishes/{category_dish}', [CategoryDishApiController::class, 'show'])->where(['category_dish' => '[0-9]+']);
Route::post('/suggest-meals', [MealSuggestionController::class, 'suggestMeal']);


//=======================================================================================

// General protected routes
Route::middleware('auth:sanctum')->group(function () {
    //when user clicks on logout, this route is called
    Route::post('/logout', [AuthController::class, 'logout']);
    //when user clicks on profile, this route is called
    Route::get('/profile', [UserController::class, 'getProfile']);

    //when user clicks on update email, this route is called
    Route::post('/email/update', [EmailController::class, 'updateEmail']);

    //when user clicks on update password, this route is called
    Route::post('/password/update', [ResetPasswordController::class, 'updatePassword']);

    //when user clicks on update own profile, this route is called
    Route::put('/profile/update', [UserController::class, 'updateProfile']);
    
    //when user clicks on deactivate own account, this route is called
    Route::post('/profile/deactivate', [UserController::class, 'deactivateUser']);

    Route::prefix("dashboard")->group(function(){
    
    // This is the api to get all orders: http://localhost:8000/api/dashboard/orders
    Route::apiResource("/orders",OrderApiController::class);
    // This is the api to get all order items: http://localhost:8000/api/dashboard/order_items
    Route::apiResource("/order_items",OrderItemApiController::class);
});

    Route::prefix("dashboard")->group(function(){
        // This is the api to get all categories: http://localhost:8000/api/dashboard/categories
        Route::post('/categories', [CategoryApiController::class, 'store']); 
        Route::put('/categories/{category}', [CategoryApiController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryApiController::class, 'destroy']);

        // This is the api to get all category dishes: http://localhost:8000/api/dashboard/category_dishes
        Route::post('/category_dishes', [CategoryDishApiController::class, 'store']);
        Route::put('/category_dishes/{category_dish}', [CategoryDishApiController::class, 'update']);
        Route::delete('/category_dishes/{category_dish}', [CategoryDishApiController::class, 'destroy']);

        // This is the api to get all orders: http://localhost:8000/api/dashboard/orders
        Route::get('/orders', [OrderApiController::class, 'index']);
        Route::post('/orders', [OrderApiController::class, 'store']);
        Route::get('/orders/{order}', [OrderApiController::class, 'show'])->where(['order' => '[0-9]+']);
        Route::put('/orders/{order}', [OrderApiController::class, 'update']);
        Route::delete('/orders/{order}', [OrderApiController::class, 'destroy']);

        // This is the api to get all order items: http://localhost:8000/api/dashboard/order_items
        Route::get('/order_items', [OrderItemApiController::class, 'index']);
        Route::post('/order_items', [OrderItemApiController::class, 'store']);
        Route::get('/order_items/{order_item}', [OrderItemApiController::class, 'show'])->where(['order_item' => '[0-9]+']);
        Route::put('/order_items/{order_item}', [OrderItemApiController::class, 'update']);
        Route::delete('/order_items/{order_item}', [OrderItemApiController::class, 'destroy']);
        
    });
});
Route::get('/toprated_dishes', [DashboardController::class, 'topRatedDishes']);


// Customer routes
Route::middleware(['auth:sanctum', 'role:customer', 'verified','throttle:120,1'])->prefix('customer')->group(function () {
    //when customer submits feedback, this route is called
    Route::post('/feedback', [FeedbackController::class, 'store']);

    Route::get("/addresses", [AddressController::class, 'index']);
    Route::post("/addresses", [AddressController::class, 'store']);
    Route::get("/addresses/{address}", [AddressController::class, 'show'])->where(['address' => '[0-9]+']);
    Route::put("/addresses/{address}", [AddressController::class, 'update']);
    Route::delete("/addresses/{address}", [AddressController::class, 'destroy']);

    
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'show']);
        Route::post('/add', [CartController::class, 'addItem']);
        Route::post('add-meal', [CartController::class, 'addMeal']);
        Route::put('/update/{item}', [CartController::class, 'updateItemQuantity']);
        Route::delete('/remove/{item}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clearCart']);
        Route::get('/count', [CartController::class, 'count']);
        Route::get('/total', [CartController::class, 'total']);
        Route::post('/checkout', [CartController::class, 'checkoutCart']);
        Route::post('items/{id}/increment', [CartController::class, 'incrementItem']);
        Route::post('items/{id}/decrement', [CartController::class, 'decrementItem']);
    });
});


Route::middleware(['auth:sanctum', 'role:kitchen_staff', 'throttle:120,1'])->prefix('kitchen_staff')->group(function () {
    // Add staff-specific routes here
    Route::post('/order_items/{orderItem}/prepare', [OrderItemApiController::class, 'markAsPreparing']);
    Route::patch('/order_items/{orderItem}/ready', [OrderItemApiController::class, 'markAsReady']);
    Route::patch('/order_items/{orderItem}/pending', [OrderItemApiController::class, 'markAsPending']);
    Route::get('/order_items/pending', [OrderItemApiController::class, 'getPendingOrderItems']);
    Route::get('/order_items/preparing', [OrderItemApiController::class, 'getPreparingOrderItems']);
});

Route::middleware(['auth:sanctum', 'role:delivery', 'throttle:120,1'])->prefix('delivery')->group(function () {
    // Add delivery-specific routes here
    Route::get('/orders/ready', [OrderApiController::class, 'getReadyOrders']);
    Route::get('/orders/delivering', [OrderApiController::class, 'getDeliveringOrders']);
    Route::patch('/orders/{order}/delivering', [OrderApiController::class, 'markAsDelivering']);
    Route::patch('/orders/{order}/delivered', [OrderApiController::class, 'markAsDelivered']);
});

//Super Admin routes
Route::middleware(['auth:sanctum', 'role:super_admin','throttle:120,1'])->prefix('super_admin')->group(function () {

});

//  Admin routes
Route::middleware(['auth:sanctum', 'role:admin,super_admin','throttle:120,1'])->prefix('admin')->group(function () {

    //when admin registers a staff, this route is called
    Route::post('/staff/register', [AuthController::class, 'StaffRegister']);
    
    //when admin views all users, this route is called
    Route::get('/users', [UserController::class, 'index']);
    
    //when admin views a user, this route is called
    Route::get('/users/{id}', [UserController::class, 'show']);
    
    //when admin deactivates a user, this route is called
    Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivateUser']);

    //when admin activates a user, this route is called
    Route::patch('/users/{id}/activate', [UserController::class, 'activateUser']);
    
    //when admin views all feedback, this route is called
    Route::get('/feedback', [FeedbackController::class, 'index']);
    
    //when admin processes feedback, this route is called
    Route::post('/feedback/process', [FeedbackController::class, 'processFeedback']);

    //when admin updates user information, this route is called
    Route::post('/users/update/{id}', [UserController::class, 'updateUserByAdmin']);

    
    //analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('/general-sentiment', [DashboardController::class, 'GeneralSentimentDistribution']);
        Route::get('/trend-over-time', [DashboardController::class, 'TrendOverTime']);
        Route::get('/aspect-sentiment', [DashboardController::class, 'AspectSentimentBreakdown']);
        Route::get('/top-complained-aspects', [DashboardController::class, 'TopComplainedAspects']);
        Route::get('/menu_analytics', [DashboardController::class, 'getMenuAnalytics']);
        Route::get('/operkpis', [DashboardController::class, 'getCoreOperationalKPIs']);
        Route::get('/saleskpis', [DashboardController::class, 'getSalesPerformanceKPIs']);
        Route::get('/teamkpis', [DashboardController::class, 'getTeamEfficienyKPIs']);
    });
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json(['message' => 'Not Found.'], 404);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user()->load('staff');

    return response()->json([
        "id"         => $user->id,
        "first_name" => $user->fname,
        "role"        => $user->getRoleNames()->first(),
        "shift_start" => $user->staff?->shift_start,
        "shift_end"   => $user->staff?->shift_end,
    ]);
});
