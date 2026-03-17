<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraineeSession extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'trainee_sessions';

    protected $fillable = [
        'booking_id',
        'trainer_id',
        'client_id',
        'session_start',
        'session_end',
        'session_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'session_start' => 'datetime',
            'session_end'   => 'datetime',
        ];
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────────

    public function isCompleted(): bool
    {
        return $this->session_status === 'completed';
    }

    public function isScheduled(): bool
    {
        return $this->session_status === 'scheduled';
    }

    public function isCanceled(): bool
    {
        return $this->session_status === 'Canceled';
    }

    // ─── Relations ───────────────────────────────────────────────────────────────

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
