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

        // Filter by specific user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by specific note
        if ($request->filled('note_id')) {
            $query->where('note_id', $request->note_id);
        }

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Search by student name or note title
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('note', fn($n) => $n->where('title', 'like', "%{$s}%"));
            });
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25);
        return $this->paginatedResponse($logs);
    }
}
