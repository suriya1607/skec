<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessLogController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = AccessLog::with('user', 'note', 'session')
            ->orderBy('created_at', 'desc');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('note_id')) {
            $query->where('note_id', $request->note_id);
        }
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(25);
        return $this->paginatedResponse($logs);
    }
}
