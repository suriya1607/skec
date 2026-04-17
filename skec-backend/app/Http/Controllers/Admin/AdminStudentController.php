<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\User;
use App\Services\SessionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    use ApiResponse;

    public function __construct(private SessionService $sessionService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = User::students();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(15);

        return $this->paginatedResponse($students);
    }

    public function show(int $id): JsonResponse
    {
        $student = User::students()
            ->with(['accessLogs' => fn($q) => $q->orderBy('created_at', 'desc')->limit(20)])
            ->findOrFail($id);

        $activeSessions = $this->sessionService->getActiveSessions($student);

        return $this->success([
            'student' => $student,
            'active_sessions' => $activeSessions,
        ]);
    }

    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        $student = User::students()->findOrFail($id);
        $student->update($request->validated());
        return $this->success($student, 'Student updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $student = User::students()->findOrFail($id);
        $this->sessionService->terminateAllUserSessions($student);
        $student->tokens()->delete();
        $student->delete();
        return $this->noContent();
    }

    public function forceLogout(int $id): JsonResponse
    {
        $student = User::students()->findOrFail($id);
        $this->sessionService->terminateAllUserSessions($student);
        $student->tokens()->delete();
        return $this->success(null, 'Student logged out from all sessions');
    }
}
