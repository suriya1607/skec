<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Services\SessionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    use ApiResponse;

    public function __construct(private SessionService $sessionService) {}

    public function index(Request $request): JsonResponse
    {
        $sessions = UserSession::with('user')
            ->active()
            ->orderBy('last_activity', 'desc')
            ->paginate(20);

        return $this->paginatedResponse($sessions);
    }

    public function destroy(int $id): JsonResponse
    {
        $session = UserSession::findOrFail($id);
        $session->update(['is_active' => false]);

        return $this->noContent();
    }
}
