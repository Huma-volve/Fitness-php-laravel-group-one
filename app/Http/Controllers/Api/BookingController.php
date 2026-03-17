<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CancelBookingRequest;
use App\Http\Requests\Api\RescheduleBookingRequest;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\TraineeSession;
use App\Models\TrainerPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
//    public function __construct()
//    {
//        $this->authorizeResource(Booking::class, 'booking');
//    }

    /**
     * GET /api/bookings
     * List all bookings for the authenticated trainee.
     */
    public function index(): AnonymousResourceCollection
    {
        $user_id = 10; // should be auth id
        $bookings = Booking::with(['trainerPackage.package', 'trainer.user', 'sessions'])
            ->where('user_id', $user_id)
            ->latest('created_at')
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    /**
     * POST /api/bookings
     * Create a new booking and initial session.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $trainerPackage = TrainerPackage::with('package')->findOrFail(
            $request->trainer_package_id
        );

        if (! $trainerPackage->is_active) {
            return response()->json([
                'message' => 'This package is no longer available.',
            ], 422);
        }

        $booking = DB::transaction(function () use ($request, $trainerPackage) {
            $user_id = 10;// should be auth id
            $booking = Booking::create([
                'user_id'               => $user_id,
                'trainer_id'            => $trainerPackage->trainer_id,
                'trainer_package_id'    => $trainerPackage->id,
                'status'                => 'pending',
                'payment_status'        => 'pending',
                'cancellation_deadline' => now()->addHours(24),
            ]);

            // Calculate session end based on package duration
            $sessionStart = $request->session_start;
            $sessionEnd   = date('Y-m-d H:i:s', strtotime($sessionStart . ' +1 hour'));

            TraineeSession::create([
                'booking_id'     => $booking->id,
                'trainer_id'     => $trainerPackage->trainer_id,
                'client_id'      => $user_id,
                'session_start'  => $sessionStart,
                'session_end'    => $sessionEnd,
                'session_status' => 'scheduled',
            ]);

            return $booking;
        });

        return response()->json([
            'message' => 'Booking created successfully.',
            'data'    => new BookingResource(
                $booking->load(['trainerPackage.package', 'trainer.user', 'sessions'])
            ),
        ], 201);
    }

    /**
     * GET /api/bookings/{booking}
     * Show booking details.
     */
    public function show(Booking $booking): BookingResource
    {
        return new BookingResource(
            $booking->load(['trainerPackage.package', 'trainer.user', 'sessions'])
        );
    }

    /**
     * PUT /api/bookings/{booking}/reschedule
     * Reschedule the first upcoming session (same trainer, same package).
     */
    public function reschedule(RescheduleBookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('reschedule', $booking);

        $session = $booking->sessions()
            ->where('session_status', 'scheduled')
            ->orderBy('session_start')
            ->first();

        if (! $session) {
            return response()->json([
                'message' => 'No scheduled session found to reschedule.',
            ], 422);
        }

        $sessionStart = $request->session_start;
        $sessionEnd   = date('Y-m-d H:i:s', strtotime($sessionStart . ' +1 hour'));

        $session->update([
            'session_start' => $sessionStart,
            'session_end'   => $sessionEnd,
        ]);

        return response()->json([
            'message' => 'Session rescheduled successfully.',
            'data'    => new BookingResource(
                $booking->load(['trainerPackage.package', 'trainer.user', 'sessions'])
            ),
        ]);
    }

    /**
     * DELETE /api/bookings/{booking}/cancel
     * Cancel a booking and all its upcoming sessions.
     */
    public function cancel(CancelBookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('cancel', $booking);

        DB::transaction(function () use ($request, $booking) {
            $booking->update([
                'status'        => 'canceled',
                'cancelled_at'  => now(),
                'cancel_reason' => $request->cancel_reason,
            ]);

            // Cancel all upcoming scheduled sessions
            $booking->sessions()
                ->where('session_status', 'scheduled')
                ->update(['session_status' => 'cancelled']);
        });

        return response()->json([
            'message' => 'Booking cancelled successfully.',
            'data'    => new BookingResource(
                $booking->load(['trainerPackage.package', 'trainer.user', 'sessions'])
            ),
        ]);
    }
}
