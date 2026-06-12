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

    public function destroy(int $id): JsonResponse
    {
        $category = NoteCategory::findOrFail($id);
        $category->delete();
        return $this->noContent();
    }

    public function StudentCategories(): JsonResponse
    {
        $isstudent = auth()->user();
        $student_course_id = $isstudent->profile->course_id;
        $categories = NoteCategory::query()
            ->where('id', $student_course_id)
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
