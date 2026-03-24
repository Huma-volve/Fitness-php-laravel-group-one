<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function pay(User $user, Booking $booking): bool
    {
        // Only the owner can pay, and only draft bookings that haven't expired
        return $user->id === $booking->user_id
            && ($booking->isDraft() || $booking->isPending())
            && ! $booking->isExpired();
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
