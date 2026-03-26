<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CancelBookingRequest;
use App\Http\Requests\Api\PayBookingRequest;
use App\Http\Requests\Api\RescheduleBookingRequest;
use App\Http\Requests\Api\ScheduleBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Request;

class BookingController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private readonly BookingService      $bookingService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * GET /api/bookings
     */
    public function index(): AnonymousResourceCollection
    {
        $bookings = Booking::with(['trainerPackage.package', 'trainer.user', 'sessions'])
            ->where('user_id', auth()->id())
            ->whereNotIn('status', ['draft'])
            ->latest('created_at')
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    /**
     * GET /api/bookings/{booking}
     */
    public function show(Booking $booking): BookingResource
    {
        $this->authorize('view', $booking);

        return new BookingResource(
            $booking->load(['trainerPackage.package', 'trainer.user', 'sessions', 'payment'])
        );
    }

    /**
     * POST /api/bookings/schedule
     * Body: {
     *   "trainer_package_id": 3,
     *   "sessions": ["2024-06-15 09:00:00", "2024-06-22 09:00:00"]
     * }
     */
    public function schedule(ScheduleBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->schedule(
            userId:           auth()->id(),
            trainerPackageId: $request->trainer_package_id,
            sessionTimes:     $request->sessions,
        );

        return response()->json([
            'message'    => 'Sessions scheduled. Proceed to payment within 30 minutes.',
            'data'       => new BookingResource(
                $booking->load(['trainerPackage.package', 'trainer.user', 'sessions', 'payment'])
            ),
            'amount'     => $booking->trainerPackage->price,
            'expires_at' => $booking->expires_at->toDateTimeString(),
        ], 201);
    }

    /**
     * POST /api/bookings/{booking}/pay
     *    { "payment_method": "paypal" }
     * }
     */
    public function pay(PayBookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('pay', $booking);

        try {
            $result = $this->bookingService->generatePaymentUrl(
                booking: $booking,
                method:  $request->payment_method,
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message'     => 'Redirect the user to the payment URL to complete payment.',
            'payment_url' => $result['payment_url'],
            'expires_at'  => $result['expires_at'],
        ]);
    }

    /**
     * POST /api/bookings/{booking}/confirm
     */
    public function confirm(Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        try {
            $this->bookingService->confirmPayment($booking);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
   

        return response()->json([
            'message' => 'Payment confirmed. Your sessions are now scheduled.',
            'data'    => new BookingResource(
                $booking->refresh()->load(['trainerPackage.package', 'trainer.user', 'sessions', 'payment'])
            ),
        ]);
    }

    /**
     * PUT /api/bookings/{booking}/reschedule
     */
    public function reschedule(RescheduleBookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('reschedule', $booking);

        $session = $booking->sessions()
            ->where('session_status', 'scheduled')
            ->orderBy('session_start')
            ->first();

        if (! $session) {
            return response()->json(['message' => 'No scheduled session found to reschedule.'], 422);
        }

        $start = Carbon::parse($request->session_start);
        $session->update([
            'session_start' => $start,
            'session_end'   => $start->copy()->addHour(),
        ]);

        
        $this->notificationService->sessionRescheduled($session->fresh(), auth()->id());

        return response()->json([
            'message' => 'Session rescheduled successfully.',
            'data'    => new BookingResource(
                $booking->load(['trainerPackage.package', 'trainer.user', 'sessions'])
            ),
        ]);
    }

    /**
     * DELETE /api/bookings/{booking}/cancel
     */
    public function cancel(CancelBookingRequest $request, Booking $booking): JsonResponse
    {
        $this->authorize('cancel', $booking);

        $this->bookingService->cancel($booking, $request->cancel_reason);

        return response()->json([
            'message' => 'Booking cancelled successfully.',
            'data'    => new BookingResource(
                $booking->refresh()->load(['trainerPackage.package', 'sessions', 'payment'])
            ),
        ]);
    }

    /**
     * GET /payments/success
     */
    public function success(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Booking completed successfully.',
            'response' => $request
        ]);
    }
}
