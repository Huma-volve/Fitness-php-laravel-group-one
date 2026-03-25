<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{


    public function index()
    {
        $reviews = Review::with(['user', 'trainer'])
            ->latest()
            ->paginate(10);

        return $this->successResponse($reviews);
    }

    public function store(Request $request)
    {
        $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
            'rating' => 'required|integer|min:1|max:5',
            'reply' => 'nullable|string',
            'comment' => 'nullable|string'
        ]);

        $review = Review::create([
            'trainer_id' => $request->trainer_id,
            'user_id'    => auth()->id(),
            'reply'      => $request->reply,
            'rating'     => $request->rating,
            'comment'    => $request->comment
        ]);

        return $this->successResponse($review, 'Review added successfully');
    }

    public function trainerReviews(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTrainer()) {
            abort(403);
        }

        $trainer = $user->trainerProfile;

        if (!$trainer) {
            abort(403);
        }

        $filters = $request->only(['username', 'star', 'comment', 'date_from', 'date_to']);
        $sortBy = $request->get('sort_by', 'date');

        $service = new ReviewService($trainer->id, $filters);

        $reviews = $service->getReviews($sortBy);
        $stats = $service->getStats();

        return $this->successResponse($reviews);
    }


    private function successResponse($data, $message = null, $status = true)
    {
        $response = ['status' => $status];

        if ($message) {
            $response['message'] = $message;
        }

        $response['data'] = $data;

        return response()->json($response);
    }
}
