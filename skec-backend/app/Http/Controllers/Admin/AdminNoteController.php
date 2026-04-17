<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
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
        $query = Note::with('category', 'uploader');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $notes = $query->orderBy('created_at', 'desc')->paginate(15);

        return $this->paginatedResponse($notes);
    }

    public function store(UploadNoteRequest $request): JsonResponse
    {
        $file = $request->file('file');

        // Generate UUID filename
        $uuid = Str::uuid()->toString();
        $storagePath = env('NOTES_STORAGE_PATH', 'private/notes');
        $filePath = "{$storagePath}/{$uuid}.pdf";

        // Check for duplicates via hash
        $fileContents = file_get_contents($file->getRealPath());
        $fileHash = hash('sha256', $fileContents);

        $duplicate = Note::where('file_hash', $fileHash)->first();
        if ($duplicate) {
            return $this->error('A note with identical content already exists: ' . $duplicate->title, 'duplicate_file', 409);
        }

        // Store file
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

        $note = Note::create([
            'title' => $title,
            'slug' => $slug,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'file_hash' => $fileHash,
            'mime_type' => $file->getMimeType(),
            'total_pages' => $totalPages,
            'status' => $request->status ?? 'draft',
            'uploaded_by' => $request->user()->id,
            'published_at' => ($request->status === 'published') ? now() : null,
        ]);

        return $this->created($note->load('category', 'uploader'), 'Note uploaded successfully');
    }

    public function show(int $id): JsonResponse
    {
        $note = Note::with('category', 'uploader')->findOrFail($id);
        return $this->success($note);
    }

    public function update(UpdateNoteRequest $request, int $id): JsonResponse
    {
        $note = Note::findOrFail($id);
        $data = $request->validated();

        if (isset($data['status']) && $data['status'] === 'published' && $note->status === 'draft') {
            $data['published_at'] = now();
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . substr($note->slug, -8);
        }

        $note->update($data);
        return $this->success($note->fresh('category', 'uploader'), 'Note updated');
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

        return $this->success($note, "Note status changed to {$status}");
    }
}
