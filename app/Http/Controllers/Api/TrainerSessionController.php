<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\TraineeSessionResource;
use App\Models\Booking;
use App\Models\TraineeSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TrainerSessionController extends Controller
{
    /**
     * GET /api/trainer/sessions
     * List upcoming / filtered sessions for the authenticated trainer.
     *
     * Query params:
     *   ?status=scheduled|completed|cancelled|no_show  (optional)
     *   ?date=2024-06-01                               (optional, filter by date)
     *   ?per_page=15                                   (optional, default 15)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $trainer = auth()->user()->trainerProfile;

        if (! $trainer) {
            abort(403, 'Trainer profile not found.');
        }

        $query = TraineeSession::with(['client', 'booking.trainerPackage.package'])
            ->where('trainer_id', $trainer->id);

        // Filter by status
        if ($request->filled('status')) {
            $request->validate([
                'status' => 'in:scheduled,completed,cancelled,no_show',
            ]);
            $query->where('session_status', $request->status);
        } else {
            // Default: only upcoming scheduled sessions
            $query->where('session_status', 'scheduled')
                ->where('session_start', '>=', now());
        }

        // Filter by specific date
        if ($request->filled('date')) {
            $request->validate(['date' => 'date']);
            $query->whereDate('session_start', $request->date);
        }

        $sessions = $query->orderBy('session_start')
            ->paginate($request->input('per_page', 15));

        return TraineeSessionResource::collection($sessions);
    }

    /**
     * GET /api/trainer/sessions/{session}
     * Show a single session detail.
     */
    public function show(TraineeSession $session): JsonResponse
    {
        $trainer = auth()->user()->trainerProfile;

        if (! $trainer || $session->trainer_id !== $trainer->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'data' => new TraineeSessionResource(
                $session->load(['client', 'booking.trainerPackage.package'])
            ),
        ]);
    }

    /**
     * GET /api/trainer/bookings
     * List all bookings assigned to the authenticated trainer.
     *
     * Query params:
     *   ?status=pending|confirmed|canceled  (optional)
     *   ?per_page=15                        (optional, default 15)
     */
    public function bookings(Request $request): AnonymousResourceCollection
    {
        $trainer = auth()->user()->trainerProfile;

        if (! $trainer) {
            abort(403, 'Trainer profile not found.');
        }

        $query = Booking::with(['user', 'trainerPackage.package', 'sessions'])
            ->where('trainer_id', $trainer->id);

        if ($request->filled('status')) {
            $request->validate([
                'status' => 'in:pending,confirmed,canceled',
            ]);
            $query->where('status', $request->status);
        }

        $bookings = $query->latest('created_at')
            ->paginate($request->input('per_page', 15));

        return BookingResource::collection($bookings);
    }
}
