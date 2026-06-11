<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminNoteController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Note::with('subject', 'uploader');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->hasCategory($request->category_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $notes = $query->orderBy('created_at', 'desc')->paginate(15);

        return $this->paginatedResponse($notes);
    }

    public function store(UploadNoteRequest $request): JsonResponse
    {
        $file = $request->file('file');

        // Check for duplicates via hash
        $fileContents = file_get_contents($file->getRealPath());
        $fileHash = hash('sha256', $fileContents);

        $duplicate = Note::where('file_hash', $fileHash)->first();
        if ($duplicate) {
            return $this->error('A note with identical content already exists: ' . $duplicate->title, 'duplicate_file', 409);
        }

        // Generate UUID filename & store file
        $uuid = Str::uuid()->toString();
        $storagePath = env('NOTES_STORAGE_PATH', 'private/notes');
        $filePath = "{$storagePath}/{$uuid}.pdf";
        Storage::disk('local')->put($filePath, $fileContents);

        // Try to get page count via pdfinfo
        $totalPages = null;
        $pdfInfoPath = $file->getRealPath();
        if (function_exists('shell_exec')) {
            $output = shell_exec("pdfinfo " . escapeshellarg($pdfInfoPath) . " 2>/dev/null | grep 'Pages:' | awk '{print $2}'");
            if ($output && is_numeric(trim($output))) {
                $totalPages = (int) trim($output);
            }
        }

        // Generate slug
        $title = $request->title ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($title) . '-' . substr($uuid, 0, 8);

        // Build comma-separated category_id from category_ids[] or single category_id
        $categoryIds = $request->category_ids ?? [];
        if (empty($categoryIds) && $request->category_id) {
            $categoryIds = [$request->category_id];
        }
        $categoryIdStr = !empty($categoryIds) ? implode(',', $categoryIds) : null;

        $note = Note::create([
            'title'       => $title,
            'slug'        => $slug,
            'description' => $request->description,
            'category_id' => $categoryIdStr,
            'subject_id'  => $request->subject_id,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $filePath,
            'file_size'   => $file->getSize(),
            'file_hash'   => $fileHash,
            'mime_type'   => $file->getMimeType(),
            'total_pages' => $totalPages,
            'status'      => $request->status ?? 'draft',
            'uploaded_by' => $request->user()->id,
            'published_at'=> ($request->status === 'published') ? now() : null,
        ]);

        // Create in-app notifications for students in selected categories if note is published
        if ($request->status === 'published' && !empty($categoryIds)) {
            $this->createNoteNotifications($note, $categoryIds);
        }

        return $this->created($note->load('subject', 'uploader'), 'Note uploaded successfully');
    }

    public function show(int $id): JsonResponse
    {
        $note = Note::with('subject', 'uploader')->findOrFail($id);
        return $this->success($note);
    }

    public function update(UpdateNoteRequest $request, int $id): JsonResponse
    {
        $note = Note::findOrFail($id);
        $data = $request->validated();

        $wasPublished = $note->status === 'published';

        // Handle category_ids array → comma-separated string
        if (isset($data['category_ids'])) {
            $data['category_id'] = !empty($data['category_ids']) ? implode(',', $data['category_ids']) : null;
            unset($data['category_ids']);
        }

        if (isset($data['status']) && $data['status'] === 'published' && $note->status === 'draft') {
            $data['published_at'] = now();
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . substr($note->slug, -8);
        }

        $note->update($data);

        // Create in-app notifications when note is being published for the first time
        if (!$wasPublished && $note->status === 'published') {
            $categoryIds = $note->getCategoryIdsArray();
            if (!empty($categoryIds)) {
                $this->createNoteNotifications($note, $categoryIds);
            }
        }

        return $this->success($note->fresh('subject', 'uploader'), 'Note updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $note = Note::findOrFail($id);
        Storage::disk('local')->delete($note->file_path);
        $note->delete();
        return $this->noContent();
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $note = Note::findOrFail($id);
        $status = $note->status === 'published' ? 'draft' : 'published';

        $note->update([
            'status' => $status,
            'published_at' => $status === 'published' ? now() : $note->published_at,
        ]);

        // Create in-app notifications when publishing a note
        if ($status === 'published') {
            $categoryIds = $note->getCategoryIdsArray();
            if (!empty($categoryIds)) {
                $this->createNoteNotifications($note, $categoryIds);
            }
        }

        return $this->success($note, "Note status changed to {$status}");
    }

    /**
     * Create in-app notification records for students in the selected categories.
     * Uses updateOrInsert to avoid duplicates if a note is toggled draft→published multiple times.
     */
    private function createNoteNotifications(Note $note, array $categoryIds): void
    {
        // Find all active students whose course_id matches any of the selected categories
        $students = User::students()
            ->active()
            ->whereHas('profile', function ($query) use ($categoryIds) {
                $query->whereIn('course_id', $categoryIds);
            })
            ->get();

        $message = "New note available: {$note->title}";
        $now     = now();

        foreach ($students as $student) {
            Notification::updateOrCreate(
                ['user_id' => $student->id, 'note_id' => $note->id],
                [
                    'message'  => $message,
                    'is_read'  => false,
                    'read_at'  => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function view($id)
    {
        $note = Note::findOrFail($id);

        $path = storage_path('app/private/' . $note->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
