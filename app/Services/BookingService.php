<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TraineeSession;
use App\Models\TrainerPackage;
use App\Models\TrainerPayout;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    private const PLATFORM_FEE_PERCENT  = 20;
    private const DRAFT_EXPIRES_MINUTES = 30;

    public function __construct(
        private readonly PaymentService      $paymentService,
        private readonly NotificationService $notificationService,
    ) {}

    public function schedule(int $userId, int $trainerPackageId, array $sessionTimes): Booking
    {
        $trainerPackage = TrainerPackage::with('package')->findOrFail($trainerPackageId);

        return DB::transaction(function () use ($userId, $trainerPackage, $sessionTimes) {

            $booking = Booking::create([
                'user_id'               => $userId,
                'trainer_id'            => $trainerPackage->trainer_id,
                'trainer_package_id'    => $trainerPackage->id,
                'status'                => 'draft',
                'payment_status'        => 'pending',
                'expires_at'            => now()->addMinutes(self::DRAFT_EXPIRES_MINUTES),
                'cancellation_deadline' => now()->addHours(24),
            ]);

            foreach ($sessionTimes as $sessionStart) {
                $start = Carbon::parse($sessionStart);

                TraineeSession::create([
                    'booking_id'     => $booking->id,
                    'trainer_id'     => $trainerPackage->trainer_id,
                    'client_id'      => $userId,
                    'session_start'  => $start,
                    'session_end'    => $start->copy()->addHour(),
                    'session_status' => 'pending_payment',
                ]);
            }

            Payment::create([
                'booking_id'     => $booking->id,
                'amount'         => $trainerPackage->price,
                'payment_status' => 'pending',
            ]);

            return $booking;
        });
    }


    public function generatePaymentUrl(Booking $booking, string $method): array
    {
        if ($booking->isExpired()) {
            $this->expireBooking($booking);
            throw new \RuntimeException('This booking has expired. Please start over.');
        }

        if (! $booking->isDraft()) {
            throw new \RuntimeException('A payment URL can only be generated for draft bookings.');
        }

        // Generate hosted payment URL from gateway
        $result = match ($method) {
            'paypal' => $this->paymentService->generatePaypalUrl(
                amount:    (float) $booking->payment->amount,
                bookingId: $booking->id,
            ),
        };

        // Move booking to "pending" and store gateway reference
        DB::transaction(function () use ($booking, $method, $result) {
            $booking->update(['status' => 'pending']);

            $booking->payment()->update([
                'payment_method'    => $method,
                'gateway_reference' => $result['reference'],
            ]);
        });

        return [
            'payment_url' => $result['url'],
            'expires_at'  => $booking->expires_at->toDateTimeString(),
        ];
    }

    /**
     * STEP 3 — Confirm Payment (called after trainee returns from payment page)
     *
     * Verifies the payment with Stripe/PayPal.
     * If confirmed → activates booking + sessions + creates payout record.
     * If failed    → reverts to draft so trainee can retry.
     */
    public function confirmPayment(Booking $booking): void
    {
        if ($booking->isConfirmed()) {
            return; // Idempotent — already confirmed
        }

        if ($booking->isExpired()) {
            $this->expireBooking($booking);
            throw new \RuntimeException('This booking has expired. Please start over.');
        }

        if (! $booking->isPending()) {
            throw new \RuntimeException('No pending payment found for this booking.');
        }

        $payment = $booking->payment;

        // Verify with the gateway using the stored reference
        $result = match ($payment->payment_method) {
//            'stripe' => $this->paymentService->verifyStripe($payment->gateway_reference),
            'paypal' => $this->paymentService->verifyPaypal($payment->gateway_reference),
        };

        if (! $result['verified']) {
            // Payment failed — revert to draft so trainee can try again
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'status'         => 'draft',
                    'payment_status' => 'failed',
                ]);
                $booking->payment()->update(['payment_status' => 'failed']);
            });

            throw new \RuntimeException('Payment was not completed. Please try again.');
        }

        // Verify amount matches — guard against tampered references
        if ((float) $result['amount'] !== (float) $payment->amount) {
            throw new \RuntimeException('Payment amount mismatch. Please contact support.');
        }

        // Activate booking
        DB::transaction(function () use ($booking, $payment, $result) {

            $booking->update([
                'status'         => 'confirmed',
                'payment_status' => 'paid',
            ]);

            $payment->update([
                'payment_status' => 'paid',
                'transaction_id' => $result['transaction_id'],
            ]);

            // Activate all held sessions
            $booking->sessions()
                ->where('session_status', 'pending_payment')
                ->update(['session_status' => 'scheduled']);

            // Create trainer payout record
            $amount     = (float) $payment->amount;
            $trainerCut = round($amount * ((100 - self::PLATFORM_FEE_PERCENT) / 100), 2);

            TrainerPayout::create([
                'trainer_id'     => $booking->trainer_id,
                'booking_id'     => $booking->id,
                'trainer_amount' => $trainerCut,
                'platform_fee'   => round($amount - $trainerCut, 2),
                'payout_status'  => 'pending',
            ]);
        });

       
    }

    /**
     * Cancel a booking.
     * If already paid — triggers a refund via the gateway.
     */
    public function cancel(Booking $booking, ?string $reason): void
    {
        DB::transaction(function () use ($booking, $reason) {

            $booking->update([
                'status'        => 'canceled',
                'cancelled_at'  => now(),
                'cancel_reason' => $reason,
            ]);

            $booking->sessions()
                ->whereIn('session_status', ['pending_payment', 'scheduled'])
                ->update(['session_status' => 'cancelled']);

            if ($booking->payment?->isPaid()) {
                $booking->payment->update(['payment_status' => 'refunded']);
                $booking->update(['payment_status' => 'refunded']);
                $booking->payout?->update(['payout_status' => 'cancelled']);

                $this->paymentService->refund($booking->payment->gateway_reference);
            }
        });
    }

    /**
     * Release held sessions and mark the draft booking as canceled.
     * Called when the 30-minute payment window expires.
     */
    public function expireBooking(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->sessions()
                ->where('session_status', 'pending_payment')
                ->update(['session_status' => 'cancelled']);

            $booking->update([
                'status'        => 'canceled',
                'cancelled_at'  => now(),
                'cancel_reason' => 'Expired — payment not completed within 30 minutes.',
            ]);
        });
    }
}
