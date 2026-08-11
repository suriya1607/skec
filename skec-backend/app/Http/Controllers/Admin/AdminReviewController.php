<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    use ApiResponse;

    /**
     * List all reviews with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['user.profile.course'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', $search));
        }

        $reviews = $query->paginate($request->get('per_page', 20));

        // Map items for the response
        $reviews->through(fn ($r) => $this->format($r));

        return $this->paginatedResponse($reviews);
    }

    /**
     * Approve a review.
     */
    public function approve(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'approved', 'admin_note' => null]);
        return $this->success($this->format($review), 'Review approved');
    }

    /**
     * Reject a review.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $review->update([
            'status'     => 'rejected',
            'admin_note' => $request->input('admin_note'),
        ]);
        return $this->success($this->format($review), 'Review rejected');
    }

    /**
     * Delete a review.
     */
    public function destroy(int $id): JsonResponse
    {
        Review::findOrFail($id)->delete();
        return $this->success(null, 'Review deleted');
    }

    // ── Pending count (for badge) ─────────────────────────────────────────

    public function pendingCount(): JsonResponse
    {
        return $this->success(['count' => Review::where('status', 'pending')->count()]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function format(Review $review): array
    {
        $user    = $review->user;
        $profile = $user?->profile;
        $courses = $profile?->getCourses() ?? collect();
        $batch   = $courses->isNotEmpty()
            ? $courses->pluck('name')->join(', ')
            : ($profile?->qualification ?? '');

        return [
            'id'         => $review->id,
            'rating'     => $review->rating,
            'review'     => $review->review,
            'status'     => $review->status,
            'admin_note' => $review->admin_note,
            'student'    => [
                'id'    => $user?->id,
                'name'  => $user?->name,
                'email' => $user?->email,
                'batch' => $batch,
            ],
            'created_at' => $review->created_at?->toDateTimeString(),
            'updated_at' => $review->updated_at?->toDateTimeString(),
        ];
    }
}
