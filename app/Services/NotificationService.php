<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\TraineeSession;
use App\Models\User;

class NotificationService
{
    // ─── Core ────────────────────────────────────────────────────────────────────

    /**
     * Persist a notification for a user.
     */
    public function send(
        int    $userId,
        string $type,
        string $title,
        string $message,
        array  $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data ?: null,
            'is_read' => false,
        ]);
    }

    // ─── Booking: Confirmed ───────────────────────────────────────────────────────

    /**
     * Called after payment is verified and booking is activated.
     * Notifies both the trainee and the trainer.
     */
    public function bookingConfirmed(Booking $booking): void
    {
        $booking->loadMissing(['user', 'trainer.user', 'trainerPackage.package']);

        $traineeName = $booking->user->name;
        $trainerName = $booking->trainer->user->name;
        $packageName = $booking->trainerPackage->package->title ?? 'Training Package';
        $data        = ['booking_id' => $booking->id];

        // Notify trainee
        $this->send(
            userId:  $booking->user_id,
            type:    'booking_confirmed',
            title:   'Booking Confirmed!',
            message: "Your booking for \"{$packageName}\" with {$trainerName} has been confirmed.",
            data:    $data
        );

        // Notify trainer
        $this->send(
            userId:  $booking->trainer->user_id,
            type:    'booking_confirmed',
            title:   'New Booking Received',
            message: "{$traineeName} has confirmed a booking for \"{$packageName}\".",
            data:    $data
        );
    }

    // ─── Booking: Cancelled ───────────────────────────────────────────────────────

    /**
     * Called after a booking is cancelled.
     * Determines who cancelled and notifies the other party (or both if admin).
     */
    public function bookingCancelled(Booking $booking, int $actorId, bool $byAdmin = false): void
    {
        $booking->loadMissing(['user', 'trainer.user', 'trainerPackage.package']);

        $packageName   = $booking->trainerPackage->package->title ?? 'Training Package';
        $traineeUserId = $booking->user_id;
        $trainerUserId = $booking->trainer?->user_id;
        $data          = ['booking_id' => $booking->id];

        if ($byAdmin) {
            // Admin cancelled — notify both parties
            $this->send(
                userId:  $traineeUserId,
                type:    'booking_cancelled',
                title:   'Booking Cancelled',
                message: "Your booking for \"{$packageName}\" has been cancelled by the platform.",
                data:    $data
            );

            if ($trainerUserId) {
                $this->send(
                    userId:  $trainerUserId,
                    type:    'booking_cancelled',
                    title:   'Booking Cancelled',
                    message: "A booking for \"{$packageName}\" has been cancelled by the platform.",
                    data:    $data
                );
            }

            return;
        }

        // Trainee cancelled → notify trainer
        if ($actorId === $traineeUserId && $trainerUserId) {
            $traineeName = $booking->user->name;
            $this->send(
                userId:  $trainerUserId,
                type:    'booking_cancelled',
                title:   'Booking Cancelled',
                message: "{$traineeName} has cancelled their booking for \"{$packageName}\".",
                data:    $data
            );
            return;
        }

        // Trainer cancelled → notify trainee
        if ($actorId === $trainerUserId) {
            $trainerName = $booking->trainer->user->name;
            $this->send(
                userId:  $traineeUserId,
                type:    'booking_cancelled',
                title:   'Booking Cancelled',
                message: "Your trainer {$trainerName} has cancelled your booking for \"{$packageName}\".",
                data:    $data
            );
        }
    }

    // ─── Session: Rescheduled ─────────────────────────────────────────────────────

    /**
     * Called after a session is rescheduled.
     * Notifies the other party — not the one who rescheduled.
     */
    public function sessionRescheduled(TraineeSession $session, int $actorUserId): void
    {
        $session->loadMissing(['client', 'trainer.user']);

        $newTime       = $session->session_start->format('D, d M Y \a\t H:i');
        $trainerUserId = $session->trainer->user_id ?? null;
        $traineeName   = $session->client->name ?? 'A trainee';
        $data          = [
            'booking_id' => $session->booking_id,
            'session_id' => $session->id,
            'new_time'   => $session->session_start->toDateTimeString(),
        ];

        // Rescheduled by trainee → notify trainer
        if ($actorUserId === $session->client_id && $trainerUserId) {
            $this->send(
                userId:  $trainerUserId,
                type:    'session_rescheduled',
                title:   'Session Rescheduled',
                message: "{$traineeName} rescheduled their session to {$newTime}.",
                data:    $data
            );
            return;
        }

        // Rescheduled by trainer → notify trainee
        if ($actorUserId === $trainerUserId) {
            $this->send(
                userId:  $session->client_id,
                type:    'session_rescheduled',
                title:   'Session Rescheduled',
                message: "Your trainer rescheduled your session to {$newTime}.",
                data:    $data
            );
        }
    }

    // ─── Session: Reminder ────────────────────────────────────────────────────────

    /**
     * 1-hour reminder — notifies the trainee.
     * Called by the SendSessionReminders scheduled command.
     */
    public function sessionReminder(TraineeSession $session): void
    {
        $session->loadMissing(['trainer.user']);

        $trainerName = $session->trainer->user->name ?? 'your trainer';
        $time        = $session->session_start->format('H:i');

        $this->send(
            userId:  $session->client_id,
            type:    'session_reminder',
            title:   'Session Starting Soon',
            message: "Reminder: Your training session with {$trainerName} starts at {$time} (in ~1 hour).",
            data:    [
                'session_id'    => $session->id,
                'session_start' => $session->session_start->toDateTimeString(),
            ]
        );
    }

    // ─── Profile: Updated ─────────────────────────────────────────────────────────

    /**
     * Called after the user updates their profile information.
     * Notifies the user themselves.
     */
    public function accountUpdated(User $user): void
    {
        $this->send(
            userId:  $user->id,
            type:    'account_updated',
            title:   'Profile Updated',
            message: 'Your account information has been updated successfully.',
            data:    []
        );
    }
}