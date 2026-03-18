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
    public function handle(Request $request, Closure $next , $role): Response
    {

    if(!auth()->check()){
         return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated'
            ], 401);
    }
    if(auth()->user()->role !== $role){
          return response()->json([
                'status'  => false,
                'message' => 'Unauthorized'
            ], 403);
    }
        return $next($request);
    }
}
