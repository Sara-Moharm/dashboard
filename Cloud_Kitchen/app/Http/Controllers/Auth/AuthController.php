<?php

namespace App\Http\Controllers\Auth;

use  App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Requests\Auth\StaffRegisterRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    use AuthorizesRequests;

    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    /**
     * Register a new customer or staff
     */
    public function CustomerRegister(CustomerRegisterRequest $request)
    {
        try{
        $result = $this->authService->registerCustomer($request->validated());
        return response()->json([
            'message' => 'Registered successfully. Please verify your email.',
        ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong while registeration!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register a new staff member
     */
    public function StaffRegister(StaffRegisterRequest $request)
    {
        $this->authorize('createOperationalStaff', User::class);

        try{
        $result = $this->authService->registerStaff($request->validated());
        return response()->json([
            'message' => 'Staff registered successfully',
            'user_id' => $result->id,
            'staff_id' => $result->staff->id
        ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
   
    /**
     * Log in a user
     */
    public function CustomerLogin(LoginRequest $request)
    {
        try{
            $validated = $request->validated();
            $user = $this->authService->CustomerLogin($validated);
            $token = $user->createToken('customer_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'user_id' => $user->id,
            'customer_id' => $user->customer->id,
            'role' => 'customer',
            'fname' => $user->fname,
            'token' => $token
        ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() == 403 ? 403 : 401);
        }
    }

    /**
     * Log in a staff member
     */
    public function StaffLogin(LoginRequest $request)
    {
        try{
            $validated = $request->validated();
            $user = $this->authService->StaffLogin($validated);
            $token = $user->createToken('staff_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'user_id' => $user->id,
            'staff_id' => $user->staff->id,
            'role' => $user->getRoleNames()->first(),
            'first_name' => $user->fname,
            'token' => $token
        ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() == 403 ? 403 : 401);
        }
    }
    
    /**
     * Log out a user
     */
    public function logout()
    {
        try{
        $user = $this->authService->logout();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Log out a user from all devices
     */
    public function logoutFromAllDevices() {
        try{
        $user = $this->authService->logoutFromAllDevices();
        return response()->json([
            'message' => 'Successfully logged out from all devices'
        ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
   }
}