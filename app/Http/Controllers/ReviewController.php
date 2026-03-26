<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewService;
use App\Http\Requests\ReplyReviewRequest;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'trainer'])->latest()->paginate(10);
        return view('dashboard.layout.review', compact('reviews'));
    }

    public function trainerReviews($trainerId, Request $request)
    {
        $filters = $request->only(['username', 'star', 'comment', 'date_from', 'date_to']);
        $sortBy = $request->get('sort_by', 'date');

        $service = new ReviewService($trainerId, $filters);

        $reviews = $service->getReviews($sortBy);
        $stats = $service->getStats();

        return view('dashboard.layout.review', array_merge($stats, [
            'reviews' => $reviews,
            'sortBy' => $sortBy,
            'trainerId' => $trainerId,
        ]));
    }

    public function reply(ReplyReviewRequest $request, Review $review)
    {
        $review->update(['reply' => $request->validated()['reply']]);

        return response()->json(['success' => true]);
    }
}
