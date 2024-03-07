<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = \Auth::user();
        $token = $user->token();
        $tokenCreatedAt = $token->created_at;

        // Expire the token after 24 hours
        if ($tokenCreatedAt->diffInDays(now()) > 1) {
            return response()->json(['message' => 'Token has expired'], 401);
        }

        return $next($request);
    }
}
