<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use App\Http\Requests\ReplyReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function index()
    {
        $reviews = Review::with(['user', 'trainer'])
            ->latest()
            ->paginate(10);

        return view('dashboard.layout.review', compact('reviews'));
    }
    public function trainerReviews(Request $request)
    {
        $user = Auth::user();

        // تحقق من الدور فقط
        if (!$user->isTrainer()) {
            abort(403); // فقط لمن ليسوا مدربين
        }

        // إذا كان لديه ملف مدرب، استخدم الـID، وإذا لم يكن لديه، استخدم ID المستخدم نفسه
        $trainerId = $user->trainerProfile?->id ?? $user->id;

        $filters = $request->only(['username', 'star', 'comment', 'date_from', 'date_to']);
        $sortBy = $request->get('sort_by', 'date');

        $service = new ReviewService($trainerId, $filters);

        $reviews = $service->getReviews($sortBy);
        $stats = $service->getStats();

        return view('dashboard.layout.review', array_merge($stats, [
            'reviews' => $reviews,
            'sortBy' => $sortBy,
        ]));
    }
    public function reply(ReplyReviewRequest $request, Review $review)
    {
        $user = Auth::user();

        if (!$user->isTrainer()) {
            abort(403);
        }

        $trainer = $user->trainerProfile;

        if (!$trainer || $review->trainer_id != $trainer->id) {
            abort(403);
        }

        $review->update([
            'reply' => $request->validated()['reply']
        ]);

        return response()->json(['success' => true]);
    }
}
