<?php

namespace App\Http\Middleware\Middleware\Api\V1_0;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateUserDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()->currentAccessToken();
        if ($token->tokenable_id !== $request->user()->id) {//
            return response()->json([
                'message' => 'Invalid token usage detected. Please re-authenticate.',
                'token' => $token,
                'user' => $request->user()->id,

            ], 401);
        }

        return $next($request);
    }
}
