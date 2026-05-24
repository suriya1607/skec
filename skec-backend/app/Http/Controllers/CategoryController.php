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
}
