<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Users\GetUsersByRoleRequest;
use App\Http\Requests\Users\GetUserWithRoleRequest;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Requests\Users\UpdateUserByAdminRequest;
class UserController extends Controller
{
    use AuthorizesRequests;

    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (GetUsersByRoleRequest $request)
    {
        $this->authorize('viewAny');

        try{
            $roles = $request->input('roles');
            $is_active = $request->input('is_active');
            $users = $this->userService->getAll($roles, $is_active);
            return response()->json($users)->load('customer', 'staff');
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'failed to get users',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GetUserWithRoleRequest $request)
    {

        try{
            // Validate the request
            $validated = $request->validated();
            $role = $validated['role'];
            $id = $validated['id'];
            $user = $this->userService->getById($role, $id);
            $this->authorize('view', $user);
            return response()->json($user);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'failed to get user',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the authenticated user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {

        try {
            $user = Auth::user();
            $this->authorize('viewProfile', $user);
            $userProfile = $this->userService->getProfile($user->id);
            $response = $this->userService->formatUserProfileResponse($userProfile);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get profile',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    /**
     * Update the authenticated user's profile.
     *
     * @param  \App\Http\Requests\Users\UpdateOwnProfileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $this->authorize('updateProfile', Auth::user());
        try{
            $data = $request->validated();
            $this->userService->updateOwnProfile($data);
            return response()->json(['message' => 'Information updated successfully']);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update information',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update a user's information by an admin.
     *
     * @param  \App\Http\Requests\Users\UpdateUserByAdminRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserByAdmin(UpdateUserByAdminRequest $request)
    {
        try{    
            $data = $request->validated();
            $user = User::findOrFail($data['id']);
            $this->authorize('update', $user);
            $this->userService->updateUserByAdmin($data);
            return response()->json(['message' => 'Information updated successfully']);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update information',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    
    /**
     * Toggle the activation status of a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggleUserActivationStatus(Request $request)
    {
        try{
            $id = $request->route('id');
            if (!$id) {
                return response()->json(['message' => 'User ID is required'], 400);
            }
            
            validator(['id' => $id], [
                'id' => 'required|integer|exists:users,id'
            ])->validate();

            $user = User::findOrFail($id);
            $this->authorize('toggleActivationStatus', $user);

            $this->userService->toggleActivationStatus($id);
            return response()->json(['message' => 'User activation status updated successfully']);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong or user not found',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete a user (soft delete).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete (Request $request)
    {
        try {
            $id = $request->route('id');
            if (!$id) {
                return response()->json(['message' => 'User ID is required'], 400);
            }
            
            validator(['id' => $id], [
                'id' => 'required|integer|exists:users,id'
            ])->validate();

            $user = User::findOrFail($id);
            $this->authorize('delete', $user);

            if($this->userService->delete($id)){
                if (Auth::user()->id === $id) {
                    Auth::logout();
                }
                Auth::user()->tokens->delete();

            return response()->json(['message' => 'User deleted successfully.']);
            } else {
                return response()->json(['message' => 'User is not deleted.'], 400);
            }
        } 
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong or user not found',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    /**
     * Restore a soft-deleted user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore (Request $request)
    {
        try {
            $id = $request->route('id');
            if (!$id) {
                return response()->json(['message' => 'User ID is required'], 400);
            }
            
            validator(['id' => $id], [
                'id' => 'required|integer|exists:users,id'
            ])->validate();

            $user = User::withTrashed()->findOrFail($id);
            $this->authorize('restore', $user);

            if($this->userService->restore($id)){
                return response()->json(['message' => 'User restored successfully']);
            } else {
                return response()->json(['message' => 'User is not restored.'], 400);
            }
        } 
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found.', 404]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong or user not found',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Force delete a user (hard delete).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function forceDelete(Request $request)
    {
        try {
            $id = $request->route('id');
            if (!$id) {
                return response()->json(['message' => 'User ID is required'], 400);
            }
            
            validator(['id' => $id], [
                'id' => 'required|integer|exists:users,id'
            ])->validate();

            $user = User::withTrashed()->findOrFail($id);
            $this->authorize('forceDelete', $user);

            if($this->userService->forceDelete($id)){
                return response()->json(['message' => 'User hard deleted successfully']);
            } else {
                return response()->json(['message' => 'User is not hard deleted.'], 400);
            }
        } 
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong or user not found',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
