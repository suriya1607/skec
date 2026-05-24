<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\Note;
use App\Models\User;
use App\Models\UserSession;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $totalStudents   = User::students()->count();
        $totalNotes      = Note::count();
        $activeSessions  = UserSession::active()->count();
        $notesThisMonth  = Note::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $recentUploads = Note::with('uploader')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentRegistrations = User::students()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'status', 'created_at']);

        return $this->success([
            'stats' => [
                'total_students'  => $totalStudents,
                'total_notes'     => $totalNotes,
                'active_sessions' => $activeSessions,
                'notes_this_month'=> $notesThisMonth,
            ],
            'recent_uploads'       => $recentUploads,
            'recent_registrations' => $recentRegistrations,
        ]);
    }
}
