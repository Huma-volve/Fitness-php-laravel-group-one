<?php

namespace App\Console\Commands;

use App\Models\TraineeSession;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendSessionReminders extends Command
{
    protected $signature   = 'sessions:remind';
    protected $description = 'Send 1-hour reminder notifications for upcoming training sessions';

    public function handle(NotificationService $notificationService): int
    {
        // Target sessions starting between 55 and 65 minutes from now
        $from = now()->addMinutes(55);
        $to   = now()->addMinutes(65);

        $sessions = TraineeSession::query()
            ->where('session_status', 'scheduled')
            ->whereBetween('session_start', [$from, $to])
            ->whereNull('reminder_sent_at')
            ->with(['client', 'trainer.user'])
            ->get();

        foreach ($sessions as $session) {
            $notificationService->sessionReminder($session);

            $session->update(['reminder_sent_at' => now()]);
        }

        $count = $sessions->count();
        $this->info("Sent {$count} reminder notification(s).");

        return self::SUCCESS;
    }
}
