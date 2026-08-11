<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\NoteCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponse;

    // Public: active categories with note counts
    public function indexPublic(): JsonResponse
    {
        $categories = NoteCategory::active()
            ->orderBy('sort_order')
            ->get()
            ->each(fn($c) => $c->append('published_notes_count'));

        return $this->success($categories);
    }

    // Admin: all categories
    public function index(): JsonResponse
    {
        $categories = NoteCategory::query()
            ->orderBy('sort_order')
            ->get()
            ->each(fn($c) => $c->append('notes_count'));

        return $this->success($categories);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = NoteCategory::create($request->validated());
        return $this->created($category, 'Category created');
    }

    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $category = NoteCategory::findOrFail($id);
        $category->update($request->validated());
        return $this->success($category, 'Category updated');
    }

    public function destroy(int $id, \Illuminate\Http\Request $request): JsonResponse
    {
        $category = NoteCategory::findOrFail($id);

        // ── 1. Verify security key ──────────────────────────────────────────
        $expectedKey = \App\Models\Setting::get('batch_delete_key', 'DELETE123');
        $providedKey = $request->input('security_key', '');

        if (empty($providedKey)) {
            return $this->error('Security key is required to delete a batch.', 'security_key_required', 422);
        }

        if ($providedKey !== $expectedKey) {
            return $this->error('Incorrect security key. Batch deletion denied.', 'security_key_invalid', 403);
        }

        // ── 2. Delete all notes that belong ONLY to this batch ─────────────
        //    Notes with comma-separated category_ids: only delete if this
        //    batch is the sole category; otherwise just strip this category.
        $notesInBatch = \App\Models\Note::hasCategory($id)->get();

        foreach ($notesInBatch as $note) {
            $ids = $note->getCategoryIdsArray();

            if (count($ids) <= 1) {
                // Only this batch — delete the note entirely + its file
                $this->deleteNoteFile($note);
                $note->accessLogs()->delete();
                $note->delete();
            } else {
                // Note belongs to multiple batches — just remove this batch from the list
                $remaining = array_filter($ids, fn($i) => $i !== $id);
                $note->update(['category_id' => implode(',', $remaining)]);
            }
        }

        // ── 3. Delete & deactivate all students enrolled ONLY in this batch ──────
        //    Students with multiple batches: just remove this batch from their course_id
        $profiles = \App\Models\StudentProfile::whereRaw('FIND_IN_SET(?, course_id)', [$id])->with('user')->get();

        foreach ($profiles as $profile) {
            $ids = $profile->getCourseIdsArray();

            if (count($ids) <= 1) {
                // Only enrolled in this batch — delete the student
                $user = $profile->user;
                if ($user) {
                    $user->tokens()->delete();
                    $user->userSessions()->delete();
                    \App\Models\Notification::where('user_id', $user->id)->delete();
                    $user->accessLogs()->delete();
                    \App\Models\Review::where('user_id', $user->id)->delete();
                    $user->delete();
                }
            } else {
                // Remove this batch from the student's course_id list
                $remaining = array_filter($ids, fn($i) => $i !== (int)$id);
                $profile->update(['course_id' => implode(',', $remaining)]);
            }
        }

        // ── 4. Delete the batch itself ─────────────────────────────────────
        $category->delete();

        return $this->success([
            'batch'            => $category->name,
            'notes_deleted'    => $notesInBatch->count(),
            'students_deleted' => $profiles->count(),
        ], "Batch \"{$category->name}\" and all associated data permanently deleted.");
    }

    /**
     * Remove the physical PDF file from storage.
     */
    private function deleteNoteFile(\App\Models\Note $note): void
    {
        if ($note->file_path && \Illuminate\Support\Facades\Storage::exists($note->file_path)) {
            \Illuminate\Support\Facades\Storage::delete($note->file_path);
        }
    }

    public function StudentCategories(): JsonResponse
    {
        $isstudent = auth()->user();
        $courseIds = $isstudent->profile?->getCourseIdsArray() ?? [];

        if (empty($courseIds)) {
            return $this->success([]);
        }

        $categories = NoteCategory::query()
            ->whereIn('id', $courseIds)
            ->orderBy('sort_order')
            ->get()
            ->each(fn($c) => $c->append('notes_count'));

        return $this->success($categories);
    }

    /**
     * Public: return published notes for all batches marked as is_free.
     * No authentication required. Supports pagination + search.
     */
    public function freeNotes(\Illuminate\Http\Request $request): JsonResponse
    {
        // Get IDs of free batches
        $freeCategoryIds = NoteCategory::where('is_free', true)
            ->where('is_active', true)
            ->pluck('id');

        if ($freeCategoryIds->isEmpty()) {
            return $this->paginatedResponse(
                \App\Models\Note::whereRaw('0')->paginate(12)
            );
        }

        $query = \App\Models\Note::with(['subject'])
            ->published()
            ->where(function ($q) use ($freeCategoryIds) {
                foreach ($freeCategoryIds as $id) {
                    $q->orWhereRaw('FIND_IN_SET(?, category_id)', [$id]);
                }
            })
            ->orderBy('published_at', 'desc');

        // Optional search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(
                fn($q) => $q
                    ->where('title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
            );
        }

        $perPage = (int) $request->get('per_page', 12);
        $paginator = $query->paginate($perPage);

        // Transform items to keep the same shape the frontend expects
        $transformed = $paginator->getCollection()->map(fn($note) => [
            'id' => $note->id,
            'title' => $note->title,
            'description' => $note->description,
            'total_pages' => $note->total_pages,
            'file_size_formatted' => $note->file_size_formatted,
            'published_at' => $note->published_at,
            'subject' => $note->subject ? ['name' => $note->subject->name] : null,
        ]);

        $paginator->setCollection($transformed);

        return $this->paginatedResponse($paginator);
    }
}
