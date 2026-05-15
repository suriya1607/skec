<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\User;
use App\Services\SessionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminStudentController extends Controller
{
    use ApiResponse;

    public function __construct(private SessionService $sessionService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = User::students()->with('profile.course');

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
            ->with([
                'profile.course',
                'accessLogs' => fn($q) => $q->orderBy('created_at', 'desc')->limit(20),
            ])
            ->findOrFail($id);

        $activeSessions = $this->sessionService->getActiveSessions($student);

        return $this->success([
            'student' => $student,
            'active_sessions' => $activeSessions,
        ]);
    }

    public function downloadPhoto(int $id): BinaryFileResponse|JsonResponse
    {
        $student = User::students()->findOrFail($id);
        $media = $student->getFirstMedia('student_photo');

        if (!$media) {
            return $this->notFound('Student photo not found.');
        }

        return response()->download(
            $media->getPath(),
            "{$student->name}-photo.{$media->extension}"
        );
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
    
    public function profileupdate(UpdateStudentProfileRequest $request, int $id): JsonResponse
    {
        $student = User::with('profile')->students()->findOrFail($id);

        // update users table
        $student->update([
            'name' => $request->name,
            'status' => $request->status ?? $student->status,
        ]);

        // update profile table
        $student->profile()->update([
            'father_name' => $request->father_name,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'address' => $request->address,
            'community_category' => $request->community_category,
            'contact_phone' => $request->contact_phone,
            'qualification' => $request->qualification,
            'course_id' => $request->course_id,
            'medium_of_studying' => $request->medium_of_studying,
        ]);
        return $this->success(null, 'Student profile updated');
    }
}
