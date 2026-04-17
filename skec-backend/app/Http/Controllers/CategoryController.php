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
            ->withCount(['notes' => fn($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        return $this->success($categories);
    }

    // Admin: all categories
    public function index(): JsonResponse
    {
        $categories = NoteCategory::withCount('notes')
            ->orderBy('sort_order')
            ->get();

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
}
