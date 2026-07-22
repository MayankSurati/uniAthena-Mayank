<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => [
                    'auth' => ['Unauthenticated.'],
                ],
            ], 401);
        }

        $allowedRoles = array_map('trim', explode('|', $roles));

        if (! in_array($user->role->value, $allowedRoles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to perform this action.',
                'errors' => [
                    'authorization' => ['You are not authorized to perform this action.'],
                ],
            ], 403);
        }   

        return $next($request);
    }
}
