<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * Get the authenticated student's own review.
     */
    public function myReview(): JsonResponse
    {
        $review = Review::where('user_id', Auth::id())->first();

        if (!$review) {
            return $this->success(null, 'No review submitted yet');
        }

        return $this->success($this->format($review));
    }

    /**
     * Submit or update the authenticated student's review.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
        ]);

        $review = Review::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating' => $data['rating'],
                'review' => $data['review'],
                'status' => 'pending', // reset to pending on update
            ]
        );

        return $this->success($this->format($review), 'Review submitted successfully');
    }

    /**
     * Public endpoint — return approved reviews for the landing page.
     */
    public function publicReviews(): JsonResponse
    {
        $reviews = Review::with(['user.profile.course'])
            ->where('status', 'approved')
            ->latest()
            ->take(12)
            ->get()
            ->map(fn ($r) => $this->formatPublic($r));

        return $this->success($reviews);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function format(Review $review): array
    {
        return [
            'id'         => $review->id,
            'rating'     => $review->rating,
            'review'     => $review->review,
            'status'     => $review->status,
            'admin_note' => $review->admin_note,
            'created_at' => $review->created_at?->toDateString(),
            'updated_at' => $review->updated_at?->toDateString(),
        ];
    }

    private function formatPublic(Review $review): array
    {
        $user    = $review->user;
        $profile = $user?->profile;
        $batch   = $profile?->course?->name ?? $profile?->qualification ?? '';

        return [
            'id'         => $review->id,
            'rating'     => $review->rating,
            'review'     => $review->review,
            'name'       => $user?->name ?? 'Student',
            'batch'      => $batch,
            'created_at' => $review->created_at?->toDateString(),
        ];
    }
}
