<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
    $user = auth()->user();

    Log::info('RoleMiddleware: Checking user roles', [
        'user_id' => $user ? $user->id : null,
        'roles' => $roles,
    ]);

    if (!$user || !$user->hasAnyRole($roles)) {
        return response()->json(['message' => 'Unauthorized'], 403);

    }
    Log::info('RoleMiddleware: User has passed role check');

    return $next($request);
    }
}