<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class EmailController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['verify']);
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    
    /**
     * Verify the user's email address using the signed URL.
     *
     * @param  int  $id
     * @return string
     */
    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!hash_equals(
            (string) $request->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            return response()->json([
                'message' => 'Invalid verification link'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // If the user is already logged in and verifying after email change, invalidate old token
        if($user->currentAccessToken() != null){
             $user->currentAccessToken()->delete();         }

        return response()->json([
            'message' => 'Email has been verified',
            'user_id' => $user->id,
            'customer_id' => $user->customer->id,
            'role' => 'customer',
            'fname' => $user->fname,
        ]);
    }

    /**
     * Resend the email verification link to the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent, check your email'
        ]);
    }

    /**
     * Update the user's email address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmail(Request $request)
    {
        $this->authorize('updateEmail', Auth::user(),Auth::user());
        try{
        $request->validate([
            'email' => 'required|email|confirmed|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                'message' => 'Invalid password'
            ], 400);
        }

        $user = Auth::user();
        $user->email = $request->email;
        DB::table('users')->where('id', $user->id)->update(['email' => $request->email , 'email_verified_at' => null]);

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email updated successfully. Please verify your new email.']);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating email: ' . $e->getMessage()], 500);
        }
    }
} 