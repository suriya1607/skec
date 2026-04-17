<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Services\SessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidSession
{
    public function __construct(private SessionService $sessionService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = $request->header('X-Session-Token');

        if (!$rawToken) {
            return response()->json([
                'success' => false,
                'error'   => 'session_expired',
                'message' => 'Session token missing.',
            ], 401);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error'   => 'unauthenticated',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $tokenHash = hash('sha256', $rawToken);
        $isValid = $this->sessionService->validateSession($user, $tokenHash);

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'error'   => 'session_expired',
                'message' => 'Your session has expired. Please log in again.',
            ], 401);
        }

        return $next($request);
    }
}
