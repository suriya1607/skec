<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use ApiResponse;

    /**
     * List all announcements (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with('creator:id,name')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('message', 'like', "%{$s}%"));
        }

        $announcements = $query->paginate(15);

        return $this->paginatedResponse($announcements);
    }

    /**
     * Create and immediately send an announcement to targeted students.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'message'             => ['required', 'string', 'max:500'],
            'type'                => ['required', 'in:info,warning,success,danger'],
            'target_category_ids' => ['nullable', 'array'],
            'target_category_ids.*' => ['integer'],
        ]);

        $categoryIds   = $data['target_category_ids'] ?? [];
        $categoryIdStr = !empty($categoryIds) ? implode(',', $categoryIds) : null;

        $announcement = Announcement::create([
            'created_by'          => $request->user()->id,
            'title'               => $data['title'],
            'message'             => $data['message'],
            'type'                => $data['type'],
            'target_category_ids' => $categoryIdStr,
            'sent_count'          => 0,
        ]);

        // Fan out notification records to targeted students
        $sentCount = $this->sendAnnouncement($announcement, $categoryIds);
        $announcement->update(['sent_count' => $sentCount]);

        return $this->created(
            $announcement->load('creator:id,name'),
            "Announcement sent to {$sentCount} student(s)"
        );
    }

    /**
     * Delete an announcement and all its notification records.
     */
    public function destroy(int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        // Cascade deletes notifications (due to FK cascade)
        $announcement->delete();

        return $this->noContent();
    }

    /**
     * Re-send an announcement to students who haven't received it yet.
     */
    public function resend(int $id): JsonResponse
    {
        $announcement = Announcement::findOrFail($id);
        $categoryIds  = $announcement->getTargetCategoryIdsArray();

        $sentCount = $this->sendAnnouncement($announcement, $categoryIds);
        $announcement->increment('sent_count', $sentCount);

        return $this->success($announcement, "Re-sent to {$sentCount} student(s)");
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    /**
     * Create notification records for the announcement.
     * Returns the number of students notified.
     */
    private function sendAnnouncement(Announcement $announcement, array $categoryIds): int
    {
        $query = User::students()->active();

        if (!empty($categoryIds)) {
            $query->whereHas('profile', function ($q) use ($categoryIds) {
                $q->where(function ($inner) use ($categoryIds) {
                    foreach ($categoryIds as $catId) {
                        $inner->orWhereRaw('FIND_IN_SET(?, course_id)', [$catId]);
                    }
                });
            });
        }

        $students = $query->get();
        $now      = now();

        foreach ($students as $student) {
            Notification::updateOrCreate(
                [
                    'user_id'         => $student->id,
                    'announcement_id' => $announcement->id,
                ],
                [
                    'type'              => 'announcement',
                    'announcement_type' => $announcement->type,
                    'message'           => $announcement->message,
                    'note_id'           => null,
                    'is_read'           => false,
                    'read_at'           => null,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]
            );
        }

        return $students->count();
    }
}
