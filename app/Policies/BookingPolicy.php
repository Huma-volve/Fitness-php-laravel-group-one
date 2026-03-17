<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Only the booking owner can view, reschedule, or cancel.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function reschedule(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            && $booking->isConfirmed()
            && now()->lessThan($booking->cancellation_deadline);
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            && ! $booking->isCanceled()
            && now()->lessThan($booking->cancellation_deadline);
    }
}
