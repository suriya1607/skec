<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\NoteSubject;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    use ApiResponse;

    // Public: active subjects with published note counts
    public function indexPublic(): JsonResponse
    {
        $subjects = NoteSubject::active()
            ->withCount(['notes' => fn($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        return $this->success($subjects);
    }

    // Admin: all subjects
    public function index(): JsonResponse
    {
        $subjects = NoteSubject::withCount('notes')
            ->orderBy('sort_order')
            ->get();

        return $this->success($subjects);
    }

    public function store(SubjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $subject = NoteSubject::create($data);
        return $this->created($subject, 'Subject created');
    }

    public function update(SubjectRequest $request, int $id): JsonResponse
    {
        $subject = NoteSubject::findOrFail($id);
        $subject->update($request->validated());
        return $this->success($subject, 'Subject updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $subject = NoteSubject::findOrFail($id);
        $subject->delete();
        return $this->noContent();
    }
}
