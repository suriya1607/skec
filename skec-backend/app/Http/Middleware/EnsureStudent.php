<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'student') {
            return response()->json([
                'success' => false,
                'error'   => 'forbidden',
                'message' => 'Student access only.',
            ], 403);
        }

        return $next($request);
    }
}
