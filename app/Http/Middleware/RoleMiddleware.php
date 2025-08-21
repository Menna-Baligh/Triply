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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if(!auth()->check()){
            return response()->json(['error' => 'Unauthenticated'], 401) ; // not authenticated
        }
        if(!auth()->user()->roles()->where('name', $role)->exists()){
            return response()->json(['error' => 'Unauthorized'], 403) ; // not authorized
        }
        return $next($request);
    }
}
