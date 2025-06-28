<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ResetPasswordController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
         // Routes that require the user NOT to be logged in
    $this->middleware('guest')->only(['forgotPassword', 'reset', 'resetPassword']);

    // Route that requires the user to be logged in
    $this->middleware('auth')->only(['updatePassword']);

    // Optional throttling to limit requests to forgot/reset
    $this->middleware('throttle:6,1')->only('forgotPassword');
    }

    /**
     * Send a password reset link to the user's email.
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        
        $response = Password::sendResetLink($request->only('email'));

        if ($response === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent'
            ]);
        }

        return response()->json([
            'message' => 'Password reset link not sent'
        ], 400);
    }


    public function reset(Request $request, $token)
    {
        return response()->json([
            'token' => $token,
            'email' => $request->email
        ]);
    }
    
    /**
     * Reset the user's password using the token.
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!Password::tokenExists($user, $request->token)) {
            return response()->json([
                'message' => 'Token is invalid'
            ], 400);
        }

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successfully'
            ]);
        }

        return response()->json([
            'message' => 'Unable to reset password'
        ], 400);
    }

    /**
     * Update the authenticated user's password.
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        //$this->authorize('updateUserPassword', Auth::user(),Auth::user());   
        Log::info('updatePassword method was hit');

        $request->validate([
            'new_password' => 'required|min:8|confirmed',
            'current_password' => 'required|min:8',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }

        DB::table('users')->where('id', $user->id)->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
        
    }
}