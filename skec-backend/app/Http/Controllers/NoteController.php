<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\AccessLog;
use App\Models\Note;
use App\Models\UserSession;
use App\Models\User;
use App\Services\NoteStreamService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use ApiResponse;

    public function __construct(private NoteStreamService $streamService)
    {
    }

    public function index(Request $request): JsonResponse
    {

        $isstudent = auth()->user();
        $courseIds = $isstudent->profile?->getCourseIdsArray() ?? [];

        $query = Note::with('subject', 'uploader')
            ->published()
            ->orderBy('published_at', 'desc');

        // Filter notes that belong to any of the student's enrolled batches
        if (!empty($courseIds)) {
            $query->where(function ($q) use ($courseIds) {
                foreach ($courseIds as $cid) {
                    $q->orWhereRaw('FIND_IN_SET(?, category_id)', [$cid]);
                }
            });
        } else {
            // No batch assigned — return empty result
            $query->whereRaw('0');
        }

        if ($request->has('category_id')) {
            $query->hasCategory($request->category_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $notes = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($notes);
    }

    public function getStreamToken(Request $request, int $id): JsonResponse
    {
        $note = Note::published()->findOrFail($id);
        $user = $request->user();

        // Verify student access to batch
        if (!$user->isAdmin()) {
            $courseIds = $user->profile?->getCourseIdsArray() ?? [];
            $noteCatIds = $note->getCategoryIdsArray();
            $hasAccess = !empty(array_intersect($courseIds, $noteCatIds));
            abort_unless($hasAccess, 403, 'You do not have access to this note.');
        }

        return $this->success([
            'stream_url'  => url('/api/v1/notes/' . $note->id . '/stream'),
            'note_id'     => $note->id,
            'title'       => $note->title,
            'total_pages' => $note->total_pages,
        ]);
    }

    public function stream(Request $request, int $id): mixed
    {
        $note = Note::published()->findOrFail($id);
        $user = $request->user();

        // Verify student access to batch
        if (!$user->isAdmin()) {
            $courseIds = $user->profile?->getCourseIdsArray() ?? [];
            $noteCatIds = $note->getCategoryIdsArray();
            $hasAccess = !empty(array_intersect($courseIds, $noteCatIds));
            abort_unless($hasAccess, 403, 'You do not have access to this note.');
        }

        $sessionToken = $request->header('X-Session-Token', '');

        // Log access
        $sessionId = null;
        if ($sessionToken) {
            $hash = hash('sha256', $sessionToken);
            $session = UserSession::where('session_token', $hash)->first();
            $sessionId = $session?->id;
        }

        AccessLog::create([
            'user_id'    => $user->id,
            'note_id'    => $note->id,
            'session_id' => $sessionId,
            'action'     => 'opened',
            'ip_address' => $request->ip(),
        ]);

        $note->increment('view_count');

        return $this->streamService->streamNote($note, $user, $sessionToken);
    }

    public function logAccess(Request $request, int $id): JsonResponse
    {
        $note = Note::findOrFail($id);
        $user = $request->user();

        $validated = $request->validate([
            'action' => ['required', 'in:opened,closed,page_changed,screenshot_attempt,capture_attempt,print_attempt,copy_attempt'],
            'page_number' => ['nullable', 'integer'],
            'duration_seconds' => ['nullable', 'integer'],
        ]);

        $sessionToken = $request->header('X-Session-Token');
        $sessionId = null;
        if ($sessionToken) {
            $hash = hash('sha256', $sessionToken);
            $session = UserSession::where('session_token', $hash)->first();
            $sessionId = $session?->id;
        }

        AccessLog::create([
            'user_id' => $user->id,
            'note_id' => $note->id,
            'session_id' => $sessionId,
            'action' => $validated['action'],
            'page_number' => $validated['page_number'] ?? null,
            'duration_seconds' => $validated['duration_seconds'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        return $this->success(null, 'Access logged');
    }

    /**
     * Public stream for free-batch notes (no authentication required).
     * Verifies the note belongs to a batch marked is_free before serving.
     */
    public function streamFree(Request $request, int $id): mixed
    {
        $note = Note::published()->findOrFail($id);

        // Ensure the note belongs to at least one free batch
        $categoryIds = $note->getCategoryIdsArray();
        $isFree = !empty($categoryIds) && \App\Models\NoteCategory::whereIn('id', $categoryIds)
            ->where('is_free', true)
            ->where('is_active', true)
            ->exists();

        if (!$isFree) {
            abort(403, 'This note is not publicly accessible.');
        }

        $note->increment('view_count');

        return $this->streamService->streamNoteFree($note);
    }
}
